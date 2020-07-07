<?php

class EmailUnsubscribeAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $template = 'emailunsubscribe';
    public $title = 'Un-Subscribe';
    public $description = 'Un-subscribe from all future email';

    /** @var EmailService $EmailService */
    private $EmailService;

    public function getMenus(){

    }

    /** @var FormUI $UnsubscribeForm */
    public $UnsubscribeForm;

    public $unsubscribed = false;

    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/email/unsubscribe', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register(){

    }

    public function build($params){

        $this->EmailService = CoreLogic::getService('EmailService');

        $email = isset($_GET['email']) ? $_GET['email'] : false;
        $token = isset($_GET['token']) ? $_GET['token'] : false;

        $formFields = array();
        $formFields[] = array(
            'name' => 'email',
            'label' => 'Confirm Email',
            'type' => 'forminputemail',
            'template' => 'formfieldflexible',
            'condition' => '*',
            'placeholder' => 'ie: john.parker@mymail.com',
            'helper' => 'Please confirm your email',
            'value' => ''
        );
        $formFields[] = array(
            'name' => 'reason',
            'label' => 'Reason',
            'type' => 'forminputtextarea',
            'template' => 'formfieldflexible',
            'placeholder' => 'ie: Reason for un-subscribing',
            'helper' => 'Would you let us know how we can do better?',
            'value' => ''
        );
        $formFields[] = array(
            'name' => 'unsubscribe_submit',
            'label' => '',
            'type' => 'forminputsubmit',
            'template' => 'formfieldflexible',
            'placeholder' => '',
            'value' => 'Un-subscribe'
        );

        /**
         * Set form data
         */
        $this->UnsubscribeForm = CoreForm::register('unsubscribe_form', array('method' => 'post', 'action' => ''));
        $this->UnsubscribeForm->buildFormFromArray($formFields);

        /**
         * Process un-subscribe
         * send reason
         */
        if($this->UnsubscribeForm->validFormSubmitted()){
            if($this->UnsubscribeForm->validateSubmission()){
                if($email != $this->UnsubscribeForm->grabFieldValue('email')){
                    CoreNotification::set('Email does not match', CoreNotification::ERROR);
                    return;
                } else if($this->EmailService->unsubscribe($email, $token)){
                    $reason = $this->UnsubscribeForm->grabFieldValue('reason');
                    if(!empty($reason)){
                        $EmailObject = new EmailObject();
                        $EmailObject->addAddressee(new EmailAddresseeObject(SITE_NAME, SITE_EMAIL));
                        $EmailObject->setSubject('Reason for un-subscribing');
                        $EmailObject->setHtmlBody('Reason for un-subscribing for ' . $email . ' is: ' . $reason);
                        $EmailObject->setTextBody('Reason for un-subscribing for ' . $email . ' is: ' . $reason);
                        $this->EmailService->sendSmtp($EmailObject);
                    }
                    CoreNotification::set('Email Un-subscribed', CoreNotification::SUCCESS);
                    $this->unsubscribed = true;
                }
            }
        }

    }

}