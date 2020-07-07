<?php

/**
 * Email Service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EmailService implements EmailServiceInterface {

    const EMAIL_EVENT_BEFORE_SEND = 'event:email:send:before';

    //authentication object
    public $smtpAuthenticationObject = null;

    /** @var EmailProcedure $EmailProcedure */
    private $EmailProcedure;

    private $addressString;
    private $unsubscribeString;

    /**
     * EmailService constructor.
     */
    function __construct(){
        if(!function_exists('PHPMailerAutoload')) {
            require 'inc/PHPMailer/PHPMailerAutoload.php';
        }
        $this->EmailProcedure = CoreLogic::getProcedure('EmailProcedure');
    }

    /**
     * Setter for smtp authentication object
     * @param EmailSmtpAuthenticationObject $smtpAuthenticationObject
     */
    public function setSmtpAuthenticationObject(EmailSmtpAuthenticationObject $smtpAuthenticationObject = null){
        $this->smtpAuthenticationObject = $smtpAuthenticationObject;
    }

    /**
     * Send email SMTP
     *
     * @param EmailObject $EmailObject
     * @return bool
     */
    public function sendSmtp(EmailObject $EmailObject = null){

        CoreObserver::dispatch(self::EMAIL_EVENT_BEFORE_SEND, $EmailObject);

        try {

            /**
             * Use SMTP Mailer
             */
            $PHPMailer = new PHPMailer();
            $PHPMailer->isSMTP();

            /**
             * Set SMTP Details
             */
            $PHPMailer->Host = $this->smtpAuthenticationObject->getServer();
            $PHPMailer->SMTPAuth = true;
            $PHPMailer->Port = $this->smtpAuthenticationObject->getPort();
            $PHPMailer->Username = $this->smtpAuthenticationObject->getUsername();
            $PHPMailer->Password = $this->smtpAuthenticationObject->getPassword();

            /**
             * Enable debugging
             */
            //$PHPMailer->SMTPDebug = 2;
            //$PHPMailer->Debugoutput = 'html';

            /**
             * Set from address
             */
            if(null != $EmailObject->getSentfrom()) {
                $PHPMailer->setFrom($EmailObject->getSentfrom()->getEmail(), $EmailObject->getSentfrom()->getName());
            }else{
                $PHPMailer->setFrom(SITE_EMAIL, SITE_NAME);
            }

            /**
             * Set to address
             * @var EmailAddresseeObject $EmailAddresseeObject
             */
            $c = 0;
            foreach($EmailObject->getAddressees() as $EmailAddresseeObject){
                if(self::unsubscribed($EmailAddresseeObject->getEmail())){
                    CoreNotification::set($EmailAddresseeObject->getEmail() . ' has un-subscribed. Email was not sent, <a href="/email/resubscribe?email=' . urlencode($EmailAddresseeObject->getEmail()) . '&amp;token=' . urlencode(self::generateSubscribeToken($EmailAddresseeObject->getEmail())) . '">click to re-subscribe</a>.', CoreNotification::WARNING);
                    continue;
                }
                $PHPMailer->addAddress($EmailAddresseeObject->getEmail(), $EmailAddresseeObject->getName());
                $c++;
            }
            if($c == 0) return;

            /**
             * Append required portions
             */
            $EmailObject = self::appendUnsubscribeAndAddress($EmailObject);

            /**
             * Create Message
             */
            $PHPMailer->isHTML(true);
            $PHPMailer->Subject = $EmailObject->getSubject();
            $PHPMailer->msgHTML(CoreRender::renderEmail($EmailObject->getHtmlBody()));

            /**
             * Attempt to send email
             */
            if(false === $PHPMailer->send()){

                /**
                 * Throw an exception
                 */
                CoreLog::error("Mailer Error: " . $PHPMailer->ErrorInfo);

                //something went wrong
                return false;

            }

            //all went well!
            return true;

        } catch(Exception $e){

            /**
             * Rethrow Exception
             */
            CoreLog::error("An exception was caught when attempting to send an email via SMTP. Info: " . $e->getMessage());

        }

        return false;

    }

    /**
     * Generate subscribe token
     *
     * @param null $email
     * @return string
     */
    public function generateSubscribeToken($email = null){
        return CoreStringUtils::saltString($email, SHORT_SALT);
    }

    /**
     * Un-subscribe email
     *
     * @param null $email
     * @param null $token
     * @return bool
     */
    public function unsubscribe($email = null, $token = null){
        try {
            return $this->EmailProcedure->unsubscribeEmail($email, $token);
        } catch(Exception $e){
            CoreNotification::set('Unable to un-subscribe. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Is email un-subscribed
     *
     * @param null $email
     * @return bool
     */
    public function unsubscribed($email = null){
        try {
            return $this->EmailProcedure->emailUnsubscribed($email);
        } catch(Exception $e){
            CoreNotification::set('Unable to check if email is un-subscribed. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Re-subscribe email
     *
     * @param null $email
     * @param null $token
     * @return bool
     */
    public function resubscribe($email = null, $token = null){
        try {
            return $this->EmailProcedure->reSubscribeEmail($email, $token);
        } catch(Exception $e){
            CoreNotification::set('Unable to re-subscribe email. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Append un-subscribe and address block
     *
     * @param EmailObject $emailObject
     * @return EmailObject
     */
    public function appendUnsubscribeAndAddress(EmailObject $emailObject){
        $data = array();
        $addressees = $emailObject->getAddressees();
        if(!empty($addressees)){
            /** @var EmailAddresseeObject $addressee */
            $addressee = $addressees[0];
            $data['email'] = $addressee->email;
            $data['token'] = self::generateSubscribeToken($addressee->email);
        }
        $this->unsubscribeString = CoreTemplate::render('emailunsubscribeblock', $data);
        if(false !== stripos($emailObject->getHtmlBody(), '</body>')) {
            $emailObject->htmlbody = str_ireplace('</body>', $this->addressString . $this->unsubscribeString . '</body>', $emailObject->htmlbody);
        }else{
            $emailObject->htmlbody .= $this->addressString . $this->unsubscribeString;
        }
        return $emailObject;
    }

    /**
     * Send email by mail()
     *
     * @param EmailObject $EmailObject
     * @return bool
     */
    public function send(EmailObject $EmailObject = null){

        //check email object
        if(empty($EmailObject)){
            CoreLog::error('Null email object passed');
            return false;
        }

        //get addressees
        $addressees = $EmailObject->getAddressees();

        //check addressees
        if(empty($addressees)){
            CoreLog::error('Need addressees to send email');
            return false;
        }

        /**
         * Append required portions
         */
        $EmailObject = self::appendUnsubscribeAndAddress($EmailObject);

        try {

            //build addressee string
            $addresseesString = '';
            foreach($addressees as $EmailAddresseeObject){
                if(self::unsubscribed($EmailAddresseeObject->getEmail())){
                    CoreNotification::set($EmailAddresseeObject->getEmail() . ' has un-subscribed. Email was not sent, <a href="/email/resubscribe?email=' . urlencode($EmailAddresseeObject->getEmail()) . '&amp;token=' . urlencode(self::generateSubscribeToken($EmailAddresseeObject->getEmail())) . '">click to re-subscribe</a>.', CoreNotification::WARNING);
                    continue;
                }
                $addresseesString .= $EmailAddresseeObject->__toString();
            }
            if(empty($addresseesString)) return;

            //send email
            return mail($addresseesString, $EmailObject->getSubject(), CoreRender::renderEmail($EmailObject->getHtmlBody()));

        } catch (Exception $e){

            //handle error
            CoreLog::error($e->getMessage());

        }

        //something went wrong
        return false;

    }

    /**
     * @param mixed $addressString
     */
    public function setAddressString($addressString)
    {
        $this->addressString = $addressString;
    }

    /**
     * @param mixed $unsubscribeString
     */
    public function setUnsubscribeString($unsubscribeString)
    {
        $this->unsubscribeString = $unsubscribeString;
    }

}