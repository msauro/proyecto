<?php

class UserController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			
			$file = $_FILES['avatar_url'];
			
			$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['email']);

			if($img['status'] == 'error'){
				die($img['message']);
			}

			$params['avatar_url'] = $img['message'];
		
			$params["password"]	= base64_encode(pack("H*",sha1(utf8_encode($params["password"]))));

			$admin = new Application_Model_Admin();

			// Search email before add an admin
			$alreadyRegistered = $admin->getAdminByEmail($params['email']);
			
			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/admin/add');
            // If this email wasn't registered in the past -> add a new admin
            }elseif($alreadyRegistered == null){
            	$result = $admin->add($params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/admin/add');
            	}
            	Gabinando_Base::addSuccess('Administrator succesfully added');
            	$this->_redirect('/admin/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered admin
            	$params['isDeleted'] = 0;
            	// Update the admin with new properties and the same id
            	$result = $admin->edit('id_admin', $alreadyRegistered['id_admin'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/admin/add');
	            }
	            Gabinando_Base::addSuccess('Administrator succesfully added');
	            $this->_redirect('/admin/list');
            }
		}
	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id_admin'] = $this->getRequest()->getParam('id');
			
			if($params['password'] != ""){
				$params['password']	= base64_encode(pack("H*",sha1(utf8_encode($params['password']))));
			}
			else{
				unset($params['password']);
			}
			
			if($_FILES['avatar_url']['size'] > 0){
				$file = $_FILES['avatar_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id_admin']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['avatar_url'] = $img['message'];
			}

			
			$admin = new Application_Model_Admin();
			$result = $admin->edit('id_admin', $params['id_admin'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{

            	if($this->admin_session->admin['id_admin'] == $params['id_admin']){
            		            		
            		if(isset($params['avatar_url'])) $this->admin_session->admin['avatar_url'] = $params['avatar_url'];

	            	$this->admin_session->admin['first_name'] = $params['first_name'];
	         		$this->admin_session->admin['last_name'] = $params['last_name'];
	         		$this->admin_session->admin['password'] = $params['password'];
            	}            	

            	Gabinando_Base::addSuccess('Administrator succesfully updated');
            }

            $this->_redirect('/admin/list');
		}
		else{
			$id_admin = $this->getRequest()->getParam('id');
			
			if($id_admin){
				$adminModel = new Application_Model_Admin();
				$admin = $adminModel->getAdminById($id_admin);
				
				if($admin){
					$this->view->data = $admin;
				}
				else{
					$this->_redirect('/admin/list');
				}
			}
			else{
				$this->_redirect('/admin/list');
			}

		}
    }

	public function listAction() {
		$admin = new Application_Model_Admin();
		$adminList = $admin->getList();
		$this->view->adminsList = $adminList;
    }

	public function deleteAction(){
		$id_admin = $this->getRequest()->getParam('id');
		if($id_admin){
			if($id_admin != $this->admin_session->admin['id_admin']){
				$admin = new Application_Model_Admin();
				$result = $admin->remove('id_admin',$id_admin);
				if($result instanceof Exception){
					Gabinando_Base::addError($result->getMessage());
				}else{
					Gabinando_Base::addSuccess('Administrator succesfully deleted');
				}
			}else{
				Gabinando_Base::addError('You cannot delete yourself');
			}
		}
		$this->_redirect('admin/list');
	}


	// isEmailAvailable
	public function isemailavailableAction(){
		if($this->getRequest()->isPost()){
			$email = $this->getRequest()->getParam('email');

			$admin = new Application_Model_Admin();
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