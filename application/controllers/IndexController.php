<?php

class IndexController extends Cmx_Base{

	public function init(){
		parent::init();
	}


    public function indexAction() {
        
        // Get requests list and set into the view
        $request = new Application_Model_Request();
        $result = $request->getList();

        if($result instanceof Exception){
            Cmx_Base::addError($result->getMessage());
        }
        else{
            $assistanceList = array();
            $requestList = array();

            foreach ($result['requests'] as $key => $request) {
                // Set current waiting time for each request
                if(isset($request['waitingSince'])){
                    $waitingTime = date("H:i:s", strtotime(date('00:00:00')) + strtotime(date('H:i:s')) - strtotime($request['waitingSince']));
                                        
                    $splitedTime = split(":", $waitingTime);

                    $request['waitingMin'] = $splitedTime[1];
                    $request['waitingSec'] = $splitedTime[2];
                }else{
                    $request['waitingMin'] = '00';
                    $request['waitingSec'] = '00';
                }

                // Clasify request depending its type
                if($request['type'] == 'Assistance'){
                    array_push($assistanceList, $request); 
                }else{
                    array_push($requestList, $request);
                }
            }
            $this->view->assistanceList = $assistanceList;
            $this->view->requestList = $requestList;

            $widgetsData['Pending'] = 0;
            $widgetsData['Cancelled'] = 0;
            $widgetsData['Successful'] = 0;
            $widgetsData['Waiting'] = 0;

            foreach ($result['requests'] as $request){
                switch($request['status']){
                    case 'Pending':
                        $widgetsData['Pending']++;
                        break;
                    case 'Cancelled':
                        $widgetsData['Cancelled']++;
                        break;
                    case 'Successful':
                        $widgetsData['Successful']++;
                        break;
                    case 'Waiting':
                        $widgetsData['Waiting']++;
                        break;
                }
            }
            $this->view->widgetsData = $widgetsData;
        }
    }


    public function loginAction() {
    	
        $this->_helper->layout()->disableLayout();

        if(isset($this->admin_session->admin)){
            $this->_redirect('index');
        }

        if($this->getRequest()->isPost()){
            $params         = $this->getRequest()->getPost();
            $admin     = new Application_Model_Admin();
            $loginResult    = $admin->isValidLogin($params);

            if($loginResult instanceof Exception) {
                $this->view->error = 'There was an error. Please, try again.';
            }
			elseif(!$loginResult){
                $this->view->error = 'Incorrect e-mail or password.';
            }
            else{
                $this->admin_session->admin = $loginResult;
                $this->_redirect('index');
            }
        }
    }


    public function logoutAction(){
        $this->admin_session->admin = null;
        $this->_redirect('index/login');
    }

    public function printAction(){
       
        $this->_helper->layout()->disableLayout();
        $params = $this->getRequest()->getParams();

        if(isset($params['id'])){
            $request = new Application_Model_Request();
            $result = $request->getOne($params['id']);

            if($result instanceof Exception){
                Cmx_Base::addError($result->getMessage());
            }
            else{

                $this->view->request = $result;

                $setting = new Application_Model_Settings();
                $result = $setting->getSettings(1);

                if($result instanceof Exception){
                    Cmx_Base::addError($result->getMessage());
                }
                else{
                    
                    $this->view->footer = $result;
                }
            }
        }
    }
}