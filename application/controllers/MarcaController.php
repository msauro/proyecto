<?php

class MarcaController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// die(var_dump($params));
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			
			$marca = new Application_Model_Marca();

            $marcaExistente = $marca->getMarcaByName($params['nombre']);

			if($marcaExistente instanceof Exception){
                Gabinando_Base::addError($marcaExistente->getMessage());
                $this->_redirect('/marca/add');
            // si no existe -> agregar nueva marca
            }elseif($marcaExistente == null){
            	$result = $marca->add($params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/marca/add');
            	}
            	Gabinando_Base::addSuccess('Marca agregada correctamente');
            	$this->_redirect('/marca/list');
            }else{
            	Gabinando_Base::addError('Ya existe una marca con ese nombre.');
	        }
	        $this->_redirect('/marca/list');
		}
	}

	public function listAction() {
		$marca = new Application_Model_Marca();
		$listadoMarcas = $marca->getList();
		$this->view->listadoMarcas = $listadoMarcas;
    }

    public function removeAction(){
    	$id_marca = $this->getRequest()->getParam('id');
    	$marca = new Application_Model_Marca();
		$result = $marca->remove('id',$id_marca);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Marca eliminada correctamente');
		}
		$this->_redirect('marca/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			if($_FILES['image_url']['size'] > 0){
				$file = $_FILES['image_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['image_url'] = $img['message'];
			}

			$marca = new Application_Model_Marca();
			$result = $marca->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{

            	if($this->admin_session->admin['id_admin'] == $params['id']){
            		            		
            		if(isset($params['image_url'])) $this->admin_session->admin['image_url'] = $params['image_url'];

	            	$this->admin_session->admin['name'] = $params['name'];
	         		$this->admin_session->admin['last_name'] = $params['last_name'];
	         		$this->admin_session->admin['password'] = $params['password'];
            	}            	

            	Gabinando_Base::addSuccess('Marca actualizado correctamente');
            }

            $this->_redirect('/marca/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$marcaModel = new Application_Model_Marca();
				$marca = $marcaModel->getMarcaById($id);

				if($marca){
					$this->view->data = $marca;
				}
				else{
					$this->_redirect('/marca/list');
				}
			}
			else{
				$this->_redirect('/marca/list');
			}
		}
    }

		
		
	
}

?>