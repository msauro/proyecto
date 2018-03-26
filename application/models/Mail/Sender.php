<?php

class Application_Model_Mail_Sender {

    public $dispenser;

    public function __construct(){
        $dispenserModel     = new Application_Model_Dispenser();
        $this->dispenser    = $dispenserModel->getDispenser("1");
    }

	public function sendRecoveryPassword($email,$token){
		$emailObj = new Greenleaf_Mail;
        $emailObj->setRecipient($email);
        $emailObj->setTemplate('mails/passwordrecovery');
        $emailObj->setFromName($this->dispenser['name']);
        $emailObj->setFromMail($this->dispenser['email']);
        $dataArray = array(
            'token' => $token
        );
        $emailObj->setSubject('Password recovery');
        $emailObj->setVariables($dataArray);
        $emailObj->send();
	}

	public function sendNewPassword($email,$password){
		$emailObj = new Greenleaf_Mail;
        $emailObj->setRecipient($email);
        $emailObj->setTemplate('mails/newpassword');
        $emailObj->setFromName($this->dispenser['name']);
        $emailObj->setFromMail($this->dispenser['email']);
        $dataArray = array(
            'password' => $password
        );
        $emailObj->setSubject('New Password');
        $emailObj->setVariables($dataArray);
        $emailObj->send();
	}

    public function sendCronCancelDonation($count){
        $emailObj = new Greenleaf_Mail;
        $emailObj->setRecipient($this->dispenser_email);
        $emailObj->setTemplate('mails/cron');
        $emailObj->setFromName($this->dispenser['name']);
        $emailObj->setFromMail($this->dispenser['email']);
        $dataArray = array(
            'message' => "Donations Cancelled ({$count})"
        );
        $emailObj->setSubject('Cron: Donation Cancelled');
        $emailObj->setVariables($dataArray);
        $emailObj->send();
    }

    public function sendEmail($email,$subject,$message){
        $emailObj = new Greenleaf_Mail;
        $emailObj->setRecipient($email);
        $emailObj->setTemplate('mails/general');
        $emailObj->setFromName($this->dispenser['name']);
        $emailObj->setFromMail($this->dispenser['email']);
        $dataArray = array(
            'message' => $message
        );
        $emailObj->setSubject($subject);
        $emailObj->setVariables($dataArray);
        $emailObj->send();
    }

}