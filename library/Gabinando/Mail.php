<?php

require_once('sendgrid-php-master/vendor/autoload.php');
        // EJEMPLO DE USO:
        // $emailObj = new Greenleaf_Mail;
        // $emailObj->setRecipient('bruno.mininno@gmail.com');
        // $emailObj->setTemplate('mails/test'); // views/scripts/mails/test.phtml
        // $emailObj->setFromName('GLD');
        // $emailObj->setFromMail('dev@greenleaf.com');
        // $dataArray = array(
        //     'var' => 'Variable'
        // );
        // $emailObj->setSubject('GLD - Hello');
        // $emailObj->setVariables($dataArray);
        // $emailObj->send();
class Gabinando_Mail {

    protected $templateVariables = array();
    protected $templateName;
    protected $zendMail;
    protected $recipient;
    protected $subject;
    protected $fromName;
    protected $fromMail;
    protected $html;
    protected $sendgrid_apikey;

    public function __construct (){
        $this->zendMail = new Zend_Mail();     
        $this->sendgrid_apikey = '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';
    }

    public function __set ($name, $value){
        $this->templateVariables[$name] = $value;
    }

    public function setVariables($data){
        foreach($data as $key => $value){
            $this->__set($key,$value);
        }
    }

    public function setSubject ($subject){
        $this->subject = $subject;
    }

    public function setTemplate ($filename){
        $this->templateName = $filename;
    }


    public function setRecipient ($email){
        $this->recipient = $email;
    }

    public function setFromMail ($email){
        $this->fromMail = $email;
    }

    public function setFromName ($name){
        $this->fromName = $name;
    }

    public function send (){

        $templateDir = APPLICATION_PATH . "/views/";

        $viewConfig = array('basePath' => $templateDir);
        $subjectView = new Zend_View($viewConfig);

        foreach ($this->templateVariables as $key => $value)
        {
            $subjectView->{$key} = $value;
        }

        $textView = new Zend_View($viewConfig);
        foreach ($this->templateVariables as $key => $value)
        {
            $textView->{$key} = $value;
        }
        try {
            $text = $textView->render($this->templateName . '.txt');
        } catch (Zend_View_Exception $e) {
            $text = false;
        }

        $htmlView = new Zend_View($viewConfig);
        foreach ($this->templateVariables as $key => $value)
        {
            $htmlView->{$key} = $value;
        }

        try {
            $this->html = $htmlView->render($this->templateName . '.phtml');
        } catch (Zend_View_Exception $e) {
            $this->html = false;
        }

        $mail = new Zend_Mail();

        $mail->setFrom($this->fromMail,$this->fromName);

        $mail->addTo($this->recipient);

        $mail->setSubject($this->subject);

        if ($this->html !== false) {
            $mail->setBodyText($text);
        }

        if ($this->html !== false) {
            $mail->setBodyHtml($this->html);
        }

        //Send email
        //$mail->send();

        //Si intenta mandar el mail y no se puede conectar con sendgrid se quedara tildado el newdonation al finalizar la donation!.
        if(!empty($this->sendgrid_apikey)) $this->sendSendgridEmail();
    }

    private function sendSendgridEmail(){
        try {
            $sendgrid = new SendGrid($this->sendgrid_apikey);
            $email    = new SendGrid\Email();
            $email->addTo($this->recipient)
                  ->setFrom($this->fromMail)
                  ->setFromName($this->fromName)
                  ->setSubject($this->subject)
                  ->setHtml($this->html);

            if(!strpos($this->recipient,'@gabinando.com')) $sendgrid->send($email);        

        } catch (Exception $e) {
            echo 'A sendgrid error occurred: - ' . $e->getMessage();
        }

    }

}