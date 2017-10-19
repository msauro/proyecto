<?php

class ProductoController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$file = $_FILES['imagen_url'];
			
			$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['email']);

			if($img['status'] == 'error'){
				die($img['message']);
			}

			$params['imagen_url'] = $img['message'];
		
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			
			$producto = new Application_Model_Producto();
            $result = $producto->add($params);

			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/producto/add');
            // If this email wasn't registered in the past -> add a new producto
            }elseif($alreadyRegistered == null){

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/producto/add');
            	}
            	Gabinando_Base::addSuccess('Producto agregado correctamente');
            	$this->_redirect('/producto/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered admin
            	$params['eliminado'] = 0;
            	// Update the admin with new properties and the same id
            	$result = $producto->edit('id', $alreadyRegistered['id'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/producto/add');
	            }
	            Gabinando_Base::addSuccess('Producto agregado correctamente');
	            $this->_redirect('/producto/list');
            }
		}else{
			$marcaModel = new Application_Model_Marca();

			$listadoMarcas = $marcaModel->getList();

			$this->view->listadoMarcas = $listadoMarcas;
		}
	}

	public function listAction() {
		$producto = new Application_Model_Producto();
		$listadoProductos = $producto->getList();
		$this->view->listadoProductos = $listadoProductos;
    }

    public function removeAction(){
    	$id_producto = $this->getRequest()->getParam('id');
    	$producto = new Application_Model_Producto();
		$result = $producto->remove('id',$id_producto);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Producto eliminado correctamente');
		}
		$this->_redirect('producto/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			if($_FILES['imagen_url']['size'] > 0){
				$file = $_FILES['imagen_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['imagen_url'] = $img['message'];
			}

			$producto = new Application_Model_Producto();
			$result = $producto->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{
            	           		
            	Gabinando_Base::addSuccess('Producto actualizado correctamente');
            }

            $this->_redirect('/producto/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$marcaModel = new Application_Model_Marca();

				$listadoMarcas = $marcaModel->getList();

				$productoModel = new Application_Model_Producto();
				$producto = $productoModel->getProductoById($id);

				$this->view->listadoMarcas = $listadoMarcas;

				if($producto){
					$this->view->data = $producto;
				}
				else{
					$this->_redirect('/producto/list');
				}
			}
			else{
				$this->_redirect('/producto/list');
			}
		}
    }
		
		
	
}

?>