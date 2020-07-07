<?php

class SSOService {

    /** @var SSOProcedure $SSOProcedure */
    private $SSOProcedure;

    /** @var bool $SimpleSamlLoaded */
    private $SimpleSamlLoaded = false;

    /** @var SimpleSAML_Configuration $SimpleSAML_Configuration */
    private $SimpleSAML_Configuration;

    /** @var SimpleSAML_Auth_Simple $SimpleSAML_Auth_Simple */
    private $SimpleSAML_Auth_Simple;

    const SIMPLE_SAML_ROOT_DIR = '/lib/simplesamlphp-1.13.2';

    function __construct(){
        $this->SSOProcedure = CoreLogic::getProcedure('SSOProcedure');
    }

    public function loasSimpleSaml(){

        if(!$this->SimpleSamlLoaded) {
            require_once(__DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/lib/_autoload.php');
            $this->SimpleSamlLoaded = true;
        }

        $this->SimpleSAML_Configuration = SimpleSAML_Configuration::getInstance();


        $this->SimpleSAML_Auth_Simple = new SimpleSAML_Auth_Simple('default-sp');


        /**
         * Generate certificates
         */
        self::generateIdP();

        die('stopped');

        /**
         * Authenticate if needed
         */
        if (!$this->SimpleSAML_Auth_Simple->isAuthenticated()) {

            //return url
            $url = 'https://webprod.msoe.edu/apply2msoe2/auth.php?as=default-sp';

            //login configuration
            $params = array(
                'ErrorURL' => $url,
                'ReturnTo' => $url,
            );

            //login
            $this->SimpleSAML_Auth_Simple->login($params);

        }

        /**
         * Get assertion attributes
         */
        $attributes = $this->SimpleSAML_Auth_Simple->getAttributes();

        var_dump($attributes);

    }

    private function generateIdP(){

        /**
         * Enable IdP
         */
        $this->SimpleSAML_Configuration->set(array(
            'enable.saml20-idp' => true
        ));

        // For SSL certificates, the commonName is usually the domain name of
        // that will be using the certificate, but for S/MIME certificates,
        // the commonName will be the name of the individual who will use the certificate.

        $privateKey = openssl_pkey_new();
        $csr = openssl_csr_new(array(
            "countryName" => "UK",
            "stateOrProvinceName" => "Somerset",
            "localityName" => "Glastonbury",
            "organizationName" => "The Brain Room Limited",
            "organizationalUnitName" => "PHP Documentation Team",
            "commonName" => "Wez Furlong",
            "emailAddress" => "wez@example.com"
        ), $privateKey);
        $ssCert = openssl_csr_sign($csr, null, $privateKey, 365);
        openssl_csr_export($csr, $csrOut);
        openssl_x509_export($ssCert, $certOut);
        openssl_pkey_export($privateKey, $publicKeyOut, null);

        /*
         *
         * openssl genrsa -des3 -out googleappsidp.key 1024
openssl rsa -in googleappsidp.key -out googleappsidp.pem
openssl req -new -key googleappsidp.key -out googleappsidp.csr
openssl x509 -req -days 9999 -in googleappsidp.csr -signkey googleappsidp.key -out googleappsidp.crt
         *
         */

        /**
         * Log errors
         */
        while (($e = openssl_error_string()) !== false) {
            CoreLog::error('Issue generating certificate. Info: ' . $e);
        }

        /**
         * Get certificates directory
         */
        $certDir = $this->SimpleSAML_Configuration->getValue('certdir');

        /**
         * Attempt to create certs directory
         */
        if(false === file_exists(__DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir)){
            if(false === mkdir(__DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir, 0777)){
                CoreLog::error('unable to create directory: ' . __DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir);
            }
        }

        /**
         * We need it writable to create our certificates
         */
        if(false === is_writable(__DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir)){
            CoreLog::error(__DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir . ' is not writable!');
        }

        /**
         * Store private key
         */
        $publicKeyFile = __DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir . '/' . DOMAIN_NAME .'.key';
        if(false === file_exists($publicKeyFile)){
            if(false === file_put_contents($publicKeyFile, $publicKeyOut)){
                CoreLog::error('Unable to generate ' . $publicKeyFile);
            }
        }

        /**
         * Store certificate
         */
        $certificateFile = __DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/' . $certDir . '/' . DOMAIN_NAME .'.cer';
        if(false === file_exists($certificateFile)){
            if(false === file_put_contents($certificateFile, $certOut)){
                CoreLog::error('Unable to generate ' . $certificateFile);
            }
        }

        /**
         * Touch module enabled
         */
        $enabledFile = __DIR__ . self::SIMPLE_SAML_ROOT_DIR . '/modules/exampleauth/enable';
        if(false === file_exists($enabledFile)){
            if(false === touch($enabledFile)){
                CoreLog::error('Unable to touch(' .$enabledFile . '). Are you sure it is writable?');
            }
        }

    }

}