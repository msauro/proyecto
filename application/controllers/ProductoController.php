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
			$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['nombre']);

			if($img['status'] == 'error'){
				die($img['message']);
			}

			$params['imagen_url'] = $img['message'];
		
            $params['eliminado'] = 0;
			
			$producto = new Application_Model_Producto();
			$productoExistente = $producto->getProductosByParams($params['descripcion'], $params['nombre'], $params['id_marca']);
			
			if($productoExistente instanceof Exception){
                Gabinando_Base::addError($productoExistente->getMessage());
                $this->_redirect('/producto/add');
            // si el producto no fue agregado anteriormente -> agrego nuevo producto
            }elseif($productoExistente == null){

           		$result = $producto->add($params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
            	}

            	Gabinando_Base::addSuccess('Producto agregado correctamente');
            	$this->_redirect('/producto/list');
            }else{
            	
            	Gabinando_Base::addError('Ya existe un producto con ese nombre, esa descripción y esa marca.');
				
            }
        	$this->_redirect('/producto/add');
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