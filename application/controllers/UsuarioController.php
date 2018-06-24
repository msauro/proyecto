<?php

class UsuarioController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();

			(isset($params["administrador"])) ? $params["administrador"] = 1 : $params["administrador"] = 0;
				
			$file = $_FILES['avatar_url'];
			if ($file['size'] > 0) {
			
				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['email']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['avatar_url'] = $img['message'];
			}
		
			$params["password"]	= base64_encode(pack("H*",sha1(utf8_encode($params["password"]))));

			$usuario = new Application_Model_Usuario();

			// Search email before add an usuario
			$alreadyRegistered = $usuario->getUserByEmail($params['email']);
			
			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/usuario/add');
            // If this email wasn't registered in the past -> add a new usuario
            }elseif($alreadyRegistered == null){
            	$result = $usuario->add($params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/usuario/add');
            	}
            	Gabinando_Base::addSuccess('Usuario agregado correctamente');
            	$this->_redirect('/usuario/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered usuario
            	$params['isDeleted'] = 0;
            	// Update the usuario with new properties and the same id
            	$result = $usuario->edit('id', $alreadyRegistered['id'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/usuario/add');
	            }
	            Gabinando_Base::addSuccess('Usuario agregado correctamente');
	            $this->_redirect('/usuario/list');
            }
		}
	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			(isset($params["administrador"])) ? $params["administrador"] = 1 : $params["administrador"] = 0;

			if($params['password'] != ""){
				$params['password']	= base64_encode(pack("H*",sha1(utf8_encode($params['password']))));
			}
			else{
				unset($params['password']);
			}
			
			if($_FILES['avatar_url']['size'] > 0){
				$file = $_FILES['avatar_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['avatar_url'] = $img['message'];
			}

			
			$admin = new Application_Model_Usuario();
			$result = $admin->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{

            	if($this->admin_session->admin['id'] == $params['id']){
            		            		
            		if(isset($params['avatar_url'])) $this->admin_session->admin['avatar_url'] = $params['avatar_url'];

	            	$this->admin_session->admin['nombre'] = $params['nombre'];
	         		$this->admin_session->admin['apellido'] = $params['apellido'];
	         		$this->admin_session->admin['password'] = $params['password'];
            	}            	

            	Gabinando_Base::addSuccess('Usuario editado correctamente');
            }

            $this->_redirect('/usuario/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$usuarioModel = new Application_Model_Usuario();
				$usuario = $usuarioModel->getUserById($id);
				
				if($usuario){
					$this->view->data = $usuario;
				}
				else{
					$this->_redirect('/usuario/list');
				}
			}
			else{
				$this->_redirect('/usuario/list');
			}

		}
    }

	public function listAction() {
		$usuario = new Application_Model_Usuario();
		$usuarioList = $usuario->getList();
		$this->view->usuariosList = $usuarioList;
    }

	public function deleteAction(){
		$id = $this->getRequest()->getParam('id');
		if($id){
			if($id != $this->admin_session->admin['id']){
				$admin = new Application_Model_Usuario();
				$result = $admin->remove('id',$id);
				if($result instanceof Exception){
					Gabinando_Base::addError($result->getMessage());
				}else{
					Gabinando_Base::addSuccess('Usuario eliminado.');
				}
			}else{
				Gabinando_Base::addError('You cannot delete yourself');
			}
		}
		$this->_redirect('usuario/list');
	}


	// isEmailAvailable
	public function isemailavailableAction(){
		if($this->getRequest()->isPost()){
			$email = $this->getRequest()->getParam('email');

			$admin = new Application_Model_Usuario();
			$isAvailable = $admin->isEmailAvailable($email);
			
			if($isAvailable instanceof Exception){
				Gabinando_Base::addError("There was an error validating your e-mail address.");
			}else{
				$this->_helper->json->sendJson($isAvailable);
			}
		}
	}
	
}

?>