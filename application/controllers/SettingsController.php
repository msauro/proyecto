<?php

class SettingsController extends Gabinando_Base{

	public function init(){
		parent::init();
	}

    public function orderAction(){
        $model = new Application_Model_Settings();
        
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getPost();

            $result = $model->edit('id_setting',1,$params);

            if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());
            }else{
                Gabinando_Base::addSuccess('Settings changed successfully');
            }
        }
        
        $request = $model->getSettings(1);
        $this->view->data = $request;
    }
}