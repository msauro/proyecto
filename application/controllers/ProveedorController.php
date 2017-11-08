<?php

class ProveedorController extends Gabinando_Base {

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			$proveedor = new Application_Model_Proveedor();
			$alreadyRegisteredByCuit = $proveedor->getProveedorByCuit($params['cuit']);
			$alreadyRegisteredByEmail = $proveedor->getProveedorByEmail($params['email']);

			if(!is_null($alreadyRegisteredByCuit) OR !is_null($alreadyRegisteredByEmail)){
                Gabinando_Base::addError('Existe un proveedor con ese CUIT o Email');
                $this->_redirect('/proveedor/edit/id/'.$params['id']);
            // If this email wasn't registered in the past -> add a new proveedor
            }else{
           		$result = $proveedor->add($params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/proveedor/add');
            	}
            	Gabinando_Base::addSuccess('Proveedor agregado correctamente');
            	$this->_redirect('/proveedor/list');
            }
		}
	}

	public function listAction() {
		$proveedor = new Application_Model_Proveedor();
		$listadoProveedores = $proveedor->getList();
		$this->view->listadoProveedores = $listadoProveedores;
    }

    public function removeAction(){
    	$id_proveedor = $this->getRequest()->getParam('id');
    	$proveedor = new Application_Model_Proveedor();
		$result = $proveedor->remove('id',$id_proveedor);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Proveedor eliminado correctamente');
		}
		$this->_redirect('proveedor/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');

			$proveedor = new Application_Model_Proveedor();
			$prov = $proveedor->getProveedorById($params['id']);

			$error = null;
			if ($prov['cuit'] != $params['cuit']) {
				$alreadyRegistered = $proveedor->getProveedorByCuit($params['cuit']);
				if(!is_null($alreadyRegistered)){
					$error = 'Existe un proveedor con ese CUIT';
				} 
			}

			if ($prov['email'] != $params['email']) {
				$alreadyRegistered = $proveedor->getProveedorByEmail($params['email']);
				if(!is_null($alreadyRegistered)){
					$error = 'Existe un proveedor con ese Email';
				}
			}

			if(!is_null($error)){
                Gabinando_Base::addError($error);
                $this->_redirect('/proveedor/edit/id/'.$params['id']);
            }else{
				$result = $proveedor->edit($params['id'], $params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/proveedor/edit');
            	}
            	
            	Gabinando_Base::addSuccess('Proveedor editado correctamente');
	            $this->_redirect('/proveedor/list');

            }
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$proveedorModel = new Application_Model_proveedor();
				$proveedor = $proveedorModel->getProveedorById($id);

				if($proveedor){
					$this->view->data = $proveedor;
				}
				else{
					$this->_redirect('/proveedor/list');
				}
			}
			else{
				$this->_redirect('/proveedor/list');
			}
		}
    }

		
		
	
}

?>