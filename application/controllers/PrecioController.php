<?php

class PrecioController extends Gabinando_Base {

	// const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// $params['fecha'] = new DateTime($params['fecha']);
            $params['eliminado'] = 0;
			
			$params['fecha'] = strtotime($params['fecha']);

            $params['fecha'] = date("Y-m-d",($params['fecha']));
			
			$precio = new Application_Model_Precio();
            $result = $precio->add($params);

			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/precio/add');
            // If this email wasn't registered in the past -> add a new precio
            }elseif($alreadyRegistered == null){

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/precio/add');
            	}
            	Gabinando_Base::addSuccess('precio agregado correctamente');
            	$this->_redirect('/precio/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered admin
            	$params['eliminado'] = 0;
            	// Update the admin with new properties and the same id
            	$result = $precio->edit('id', $alreadyRegistered['id'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/precio/add');
	            }
	            Gabinando_Base::addSuccess('precio agregado correctamente');
	            $this->_redirect('/precio/list');
            }
		}else{
			$productoModel = new Application_Model_Producto();

			$listadoProductos = $productoModel->getList();

			$this->view->listadoProductos = $listadoProductos;
		}
	}

	public function listAction() {
		$precio = new Application_Model_Precio();
		$listadoprecios = $precio->getList();
		$this->view->listadoprecios = $listadoprecios;
    }

    public function removeAction(){
    	$id_precio = $this->getRequest()->getParam('id');
    	$precio = new Application_Model_Precio();
		$result = $precio->remove('id',$id_precio);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('precio eliminado correctamente');
		}
		$this->_redirect('precio/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			$precio = new Application_Model_Precio();
			$result = $precio->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{
            	           		
            	Gabinando_Base::addSuccess('precio actualizado correctamente');
            }

            $this->_redirect('/precio/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$marcaModel = new Application_Model_Marca();

				$listadoMarcas = $marcaModel->getList();

				$precioModel = new Application_Model_Precio();
				$precio = $precioModel->getPrecioById($id);

				$this->view->listadoMarcas = $listadoMarcas;

				if($precio){
					$this->view->data = $precio;
				}
				else{
					$this->_redirect('/precio/list');
				}
			}
			else{
				$this->_redirect('/precio/list');
			}
		}
    }
		
		
	
}

?>