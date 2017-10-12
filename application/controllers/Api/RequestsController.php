<?php

class Api_RequestsController extends Gabinando_Api{


    public function init(){
  		parent::init();
  	}

    public function createAction(){
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($params);

            // Verify JSON params
            $verify = array('type','place','pushToken');
            $this->verifyJson($verify, $params);
            if(isset($params['user'])) {
                $verify = array('id','first_name','last_name','email','avatar_medium');
                $this->verifyJson($verify, $params['user']);
                $user = $params['user']['first_name'].' '.$params['user']['last_name'];
            }else{
              $user = 'Guest';
            }
            
            $date = date('Y-m-d');
            $params['date'] = date('Y-m-d');
            $params['time'] = date('H:i:s');
            $params['status'] = 'Pending';

            $request = new Application_Model_Request();
            $result = $request->add($params);


            if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{

              // Send notification to front
                $requestSocket = new Gabinando_Requestsocket();
                $body = array(
                  'action'            => 'created',
                  'id_request'        => $result,
                  'user'              => $user,
                  'time'              => $params['time']
                  );
                $requestSocket->sendMessage($body);

                $response['request'] = array(
                    'id' => $result,
                    'type' => $params['type'],
                    'status' => $params['status'],
                    'user' => $params['user'],
                    'place' => $params['place'],
                    'date' =>  $params['date'].' '.$params['time']);

                return $this->sendSuccessResponse($response);
            }
        }
    }


    public function cancelAction(){
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getRawBody();
            $params = Zend_Json::decode($params);

            // Verify JSON params
            $verify = array('id');
            $this->verifyJson($verify, $params);

            $cancelParams = array(
                    'status' => 'Cancelled'
                    );

            $request = new Application_Model_Request();
            $result = $request->edit('id_request', $params['id'], $cancelParams);

            if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{

                $user = $request->getUser($params['id']);

                if($result instanceof Exception){
                    return $this->sendErrorResponse($result->getMessage(), $result->getCode());
                }
                else{

                  // Send notification to front
                    $requestSocket = new Gabinando_Requestsocket();
                    $body = array(
                      'action'            => 'cancelled',
                      'id_request'        => $params['id'],
                      'user'              => $user,
                      'time'              => date('H:i:s')
                      );
                    $requestSocket->sendMessage($body);

                    return $this->sendSuccessResponse();
                }
            }
        }
    }


    public function updateAction(){
        
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getRawBody();
 
            $params = Zend_Json::decode($params);

            $params = $params['request'];

            // Verify JSON params
            $verify = array('id');
            $this->verifyJson($verify, $params);
            
            // If param is setted and is not empty then set it as a param to update
            if(isset($params['type'])){
                if($params['type'] != ''){
                    $updateParams['type'] = $params['type'];
                }
            }

            if(isset($params['status'])){
                if($params['status'] != ''){
                    $updateParams['status'] = $params['status'];
                }
            }

            if(isset($params['user']['id'])){
                if($params['user']['id'] != ''){
                    $updateParams['id_user'] = $params['user']['id'];
                }
            }

            if(isset($params['place'])){
                if($params['place'] != ''){
                    $updateParams['place'] = $params['place'];
                }
            }

            $sendNotificaction = false; // Send notification to user = false
            if(isset($params['status'])){
                if($params['status'] != ''){
                    $updateParams['status'] = $params['status'];

                    if($params['status'] == 'Waiting'){
                        $updateParams['waitingSince'] = date('H:i:s');
                    }

                    if(isset($params['pushToken'])){
                        if($params['pushToken'] != ''){
                            $updateParams['pushToken'] = $params['pushToken'];

                            $sendNotificaction = true;  // Send notification to user = true;
                        }
                    }
                }
            }

            $request = new Application_Model_Request();
    		    $result = $request->edit('id_request', $params['id'], $updateParams);

            if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{
                // Get updated request in order to return it
                $result = $request->getOne($params['id']);

                if($result instanceof Exception){
                    return $this->sendErrorResponse($result->getMessage(), $result->getCode());
                }
                else{

                    if($sendNotificaction){
                        $notification = new Application_Model_Notification();
                        $notificationResponse = $notification->send($params['pushToken'], $params['status']);
                        if($notificationResponse['code'] == 200){
                            $response['request'] = $result;

                            return $this->sendSuccessResponse($response);
                        }else{
                            return $this->sendErrorResponse("Order status was changed successfully but user was not notified about that. Push notification service error: " . $notificationResponse['message'], $notificationResponse['code']);
                        }
                    }

                    $response['request'] = $result;
                    return $this->sendSuccessResponse($response);
                }
            }
        }
    }


    public function typesAction(){
       // HARDCODED RESPONSE - TEMPORAL
       $types = [];

        array_push($types, 
            ['id' => 1, 'name' => 'Ice', 'icon_url' => 'http://cmx-metre.snacktesting.com/admin-public/img/icons/mm_ice.png', 'active' => true],
            ['id' => 2, 'name' => 'Cleaning', 'icon_url' => 'http://cmx-metre.snacktesting.com/admin-public/img/icons/mm_cleaning.png', 'active' => true],
            ['id' => 3, 'name' => 'Seasoning', 'icon_url' => 'http://cmx-metre.snacktesting.com/admin-public/img/icons/mm_seasoning.png', 'active' => true],
            ['id' => 4, 'name' => 'Assistance', 'icon_url' => 'http://cmx-metre.snacktesting.com/admin-public/img/icons/mm_assistance.png', 'active' => true]
            );
        
        $response['types'] = $types;

        return $this->sendSuccessResponse($response);
    }


    public function listAction(){
        $request = new Application_Model_Request();
		$result = $request->getList();
		
        if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{
                return $this->sendSuccessResponse($result);
            }
    }

    public function listbyuserAction(){
        $id_user = $this->getRequest()->getParam('id_user');
        $request = new Application_Model_Request();
        $result = $request->getListByUser($id_user);
        
        if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{
                return $this->sendSuccessResponse($result);
            }
    }

    public function listbyguestAction(){
        $id_guest = $this->getRequest()->getParam('id_guest');
        $request = new Application_Model_Request();
        $result = $request->getListByGuest($id_guest);
        
        if($result instanceof Exception){
                return $this->sendErrorResponse($result->getMessage(), $result->getCode());
            }
            else{
                return $this->sendSuccessResponse($result);
            }
    }


    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->sendErrorResponse('Invalid method.');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendErrorResponse($errors->exception->getMessage());
                break;
        }
    }


    public function canceloldrequestsAction(){
        // Disable layout and view
        // It just run the script
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $cancelOldRequets = new Gabinando_CancelOldRequests();

        $cancelOldRequets->cancelOldRequests();
    }
}

?>
