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

    public function getproductosAction(){
		$cliente = new Application_Model_Cliente();		
		$params 	 = $params = $this->getRequest()->getParams();
		die(var_dump($params['id_cliente']));
		if( isset($params['search']) ){
			$search 	 = array(
				'search' => $params['search'],
				'filter' => 'active'
			);
		}else{
			$search 	 = array(
				'search' => ''
			);
		}

		$id_cliente = $params['id_cliente'];

		if (isset($params['page']) AND $params['page']) {
			$paginate['page']  	= $params['page'];
		} else {
			$paginate['page'] 	= 1;
		}

		$paginate['per_page'] 	= 6;

		$paginate['start_from'] = ($paginate['page']-1) * $paginate['per_page'];

		$clienteFilteredList = $cliente->getFullList($id_cliente,$search,$paginate);
		if($clienteFilteredList instanceof Exception)
			$this->sendErrorResponse($clienteFilteredList->getMessage());



		$clientePager = $cliente->getListFiltered($search);
		if($clientePager instanceof Exception)
			$this->sendErrorResponse($clientePager->getMessage());

		$clienteList = $cliente->getList($search);
		if($clienteList instanceof Exception)
			$this->sendErrorResponse($clienteList->getMessage());

		$pages = ceil(count($clientePager) / $paginate['per_page']);

		$this->sendSuccessResponse(array(
				'search' 	=> $search,
				'clientes' 	=> $clienteFilteredList,
				'total' 	=> count($clienteList),
				'pages' 	=> $pages,
				'page' 		=> $paginate['page']
			));

	}
		
		
		
	
}

?>