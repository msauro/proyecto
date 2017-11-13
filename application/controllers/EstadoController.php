<?php

class EstadoController extends Gabinando_Base {

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			
		
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			
			$estado = new Application_Model_Estado();
            $result = $estado->add($params);

			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/estado/add');
            // If this email wasn't registered in the past -> add a new estado
            }elseif($alreadyRegistered == null){

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/estado/add');
            	}
            	Gabinando_Base::addSuccess('Estado editado correctamente');
            	$this->_redirect('/estado/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered admin
            	$params['eliminado'] = 0;
            	// Update the admin with new properties and the same id
            	$result = $estado->edit('id', $alreadyRegistered['id'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/estado/add');
	            }
	            Gabinando_Base::addSuccess('Estado editado correctamente');
	            $this->_redirect('/estado/list');
            }
		}
	}

	public function listAction() {
		$estado = new Application_Model_Estado();
		$listadoEstados = $estado->getList();
		$this->view->listadoEstados = $listadoEstados;
    }

    public function removeAction(){
    	$id = $this->getRequest()->getParam('id');
    	$estado = new Application_Model_Estado();
		$result = $estado->remove('id',$id);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Estado eliminado correctamente');
		}
		$this->_redirect('estado/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			$estado = new Application_Model_Estado();
			$result = $estado->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{

            	if($this->admin_session->admin['id_admin'] == $params['id']){
            		            		
            		if(isset($params['image_url'])) $this->admin_session->admin['image_url'] = $params['image_url'];

	            	$this->admin_session->admin['name'] = $params['name'];
	         		$this->admin_session->admin['last_name'] = $params['last_name'];
	         		$this->admin_session->admin['password'] = $params['password'];
            	}            	

            	Gabinando_Base::addSuccess('Estado actualizado correctamente');
            }

            $this->_redirect('/estado/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$estadoModel = new Application_Model_Estado();
				$estado = $estadoModel->getEstadoById($id);

				if($estado){
					$this->view->data = $estado;
				}
				else{
					$this->_redirect('/estado/list');
				}
			}
			else{
				$this->_redirect('/estado/list');
			}
		}
    }

		
		
	
}

?>