<?php

abstract class Gabinando_Api extends Zend_Controller_Action {

    public function init(){
        parent::init();
    }

    public function sendErrorResponse($message = null, $code = null){
        $json = array(
                'status'    => "error",
                'message'   => $message,
                'code'      => $code    // Exception code
            );
        $this->_helper->json($json);
    }


    public function sendSuccessResponse($data = null){
        if($data != null){
            $json = array(
                'status'    => "success",
                'data'      => $data
            );
        }
        else{
            $json = array(
                'status'    => "success"
            );
        }
        
        $this->_helper->json($json);
    }


    public function verifyJson(Array $params, Array $jsonParsed){
        foreach ($params as $alias => $param) {
            if(is_string($alias)){
                if(!isset($jsonParsed[$alias]) OR $jsonParsed[$alias]==''){
                    $this->sendErrorResponse('Field ' . $alias . ' is required.');
                }elseif(gettype($jsonParsed[$alias]) != $param){
                    $this->sendErrorResponse('Field ' . $alias . ' must be ' . $param);
                }
            }else{
                if(!isset($jsonParsed[$param]) OR $jsonParsed[$param]==''){
                    $this->sendErrorResponse('Field -' . $param . '- is required.');
                }
            }
        }
    }
}