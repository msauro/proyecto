<?php

class TipoempresaController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
            $params['eliminado'] = 0;
			
			$tipoempresa = new Application_Model_TipoEmpresa();

            $tipoempresaExistente = $tipoempresa->getTipoEmpresaByName($params['nombre']);

			if($tipoempresaExistente instanceof Exception){
                Gabinando_Base::addError($tipoempresaExistente->getMessage());
                $this->_redirect('/tipoempresa/add');
            // si no existe -> agregar nueva tipoempresa
            }elseif($tipoempresaExistente == null){
            	$result = $tipoempresa->add($params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/tipoempresa/add');
            	}
            	Gabinando_Base::addSuccess('Tipo de empresa agregado correctamente');
            	$this->_redirect('/tipoempresa/list');
            }else{
            	Gabinando_Base::addError('Ya existe una tipo de empresa con ese nombre.');
	        }
	        $this->_redirect('/tipoempresa/list');
		}
	}

	public function listAction() {
		$tipoempresa = new Application_Model_TipoEmpresa();
		$listadoTipoempresa = $tipoempresa->getList();
		$this->view->listadoTipoempresa = $listadoTipoempresa;
    }

    public function removeAction(){
    	$id_tipoempresa = $this->getRequest()->getParam('id');
    	$tipoempresa = new Application_Model_TipoEmpresa();
		$result = $tipoempresa->remove('id',$id_tipoempresa);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('TipoEmpresa eliminada correctamente');
		}
		$this->_redirect('tipoempresa/list');

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

			$tipoempresa = new Application_Model_TipoEmpresa();
			$result = $tipoempresa->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{

            	if($this->admin_session->admin['id_admin'] == $params['id']){
            		            		
            		if(isset($params['image_url'])) $this->admin_session->admin['image_url'] = $params['image_url'];

	            	$this->admin_session->admin['name'] = $params['name'];
	         		$this->admin_session->admin['last_name'] = $params['last_name'];
	         		$this->admin_session->admin['password'] = $params['password'];
            	}            	

            	Gabinando_Base::addSuccess('TipoEmpresa actualizado correctamente');
            }

            $this->_redirect('/tipoempresa/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$tipoempresaModel = new Application_Model_TipoEmpresa();
				$tipoempresa = $tipoempresaModel->getTipoEmpresaById($id);

				if($tipoempresa){
					$this->view->data = $tipoempresa;
				}
				else{
					$this->_redirect('/tipoempresa/list');
				}
			}
			else{
				$this->_redirect('/tipoempresa/list');
			}
		}
    }

		
		
	
}

?>