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
		
           // $params['eliminado'] = 0;
			
			$producto = new Application_Model_Producto();
			$stock = new Application_Model_Existencia();
			$precioModel = new Application_Model_Precio();
			$productoExistente = $producto->getProductosByParams($params['codigo'], $params['id_marca']);
			
			if($productoExistente instanceof Exception){
                Gabinando_Base::addError($productoExistente->getMessage());
                $this->_redirect('/producto/add');
            // si el producto no fue agregado anteriormente -> agrego nuevo producto
            }elseif($productoExistente == null){
            	$paramsProducto = array(
				    "codigo" 		=> $params['codigo'],
				    "id_marca" 		=> $params['id_marca'],
				    "nombre"		=> $params['nombre'],
				    "eliminado"		=> 0,
				    "imagen_url"	=> $img['message'],
				    "punto_pedido"	=> $params['punto_pedido'],
				    "descripcion"	=> $params['descripcion']
				);
				
           		$result = $producto->add($paramsProducto);

           		if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
            	}
           		
           		$paramsStock = array(
				    "id_producto" 	=> $result,
				    "cantidad" 		=> $params['cantidad'],
					'fecha' 		=> date('Y-m-d H:i:s')

				);


           		$resultStock = $stock->add($paramsStock);
				if($resultStock instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
            	}

				$paramsPrecio = array(
					"id_producto" 	=> $result,
					"precio" 	=> $params['precio'],
					'fecha' 		=> date('Y-m-d H:i:s')
				);

				$resultPrecio = $precioModel->add($paramsPrecio);
				if($resultStock instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
            	}


            	Gabinando_Base::addSuccess('Producto agregado correctamente');
            	$this->_redirect('/producto/list');
            }else{

            	Gabinando_Base::addError('Ya existe un producto con ese código para esa marca.');
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

				//$params['imagen_url'] = $img['message'];
			}

			$producto = new Application_Model_Producto();
			$productoExistente = $producto->getProductosByParams($params['codigo'], $params['id_marca'], $params['id']);
			
			if($productoExistente instanceof Exception){
                Gabinando_Base::addError($productoExistente->getMessage());
            // si el producto no fue agregado anteriormente -> edito producto
            }elseif($productoExistente == null){
            	$paramsProducto = array(
				    "codigo" 		=> $params['codigo'],
				    "id_marca" 		=> $params['id_marca'],
				    "nombre"		=> $params['nombre'],
				    "eliminado"		=> 0,
				    "imagen_url"	=> $img['message'],
				    "punto_pedido"	=> $params['punto_pedido'],
				    "descripcion"	=> $params['descripcion']
				);
				$paramsStock = array(
				    "id_producto" 	=> $params['id'],
				    "cantidad" 		=> $params['cantidad'],
					'fecha' 		=> date('Y-m-d H:i:s')

				);
				$producto = new Application_Model_Producto();
				$stock = new Application_Model_Existencia();

				$resultProducto = $producto->edit($params['id'], $paramsProducto);
           		$resultStock = $stock->edit($params['id'], $paramsStock);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());

	            }else{
	            	           		
	            	Gabinando_Base::addSuccess('Producto actualizado correctamente');
	            	$this->_redirect('/producto/list');
	            }
	        }else{

            	Gabinando_Base::addError('Ya existe un producto con ese código para esa marca.');
                $this->_redirect('/producto/edit/id/'.$params['id']);
            	
            }

		}//si no es POST
		else{
			$id = $this->getRequest()->getParam('id');
			
			if($id){
				$marcaModel = new Application_Model_Marca();

				$listadoMarcas = $marcaModel->getList();

				$productoModel = new Application_Model_Producto();
				$producto = $productoModel->getProductoById($id);
// die(var_dump($producto));
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
		$producto = new Application_Model_Producto();		
		$params 	 = $params = $this->getRequest()->getParams();
	// die(var_dump($params));
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

		// $id_cliente = $params['id_cliente'];

		if (isset($params['page']) AND $params['page']) {
			$paginate['page']  	= $params['page'];
		} else {
			$paginate['page'] 	= 1;
		}

		$paginate['per_page'] 	= 15;

		$paginate['start_from'] = ($paginate['page']-1) * $paginate['per_page'];

		$productos = $producto->getListFiltered($search,$paginate);
	//die(var_dump($productos));
		if($productos instanceof Exception)
			$this->sendErrorResponse($productos->getMessage());
		$productoPager = $producto->getListFiltered($search);
		if($productoPager instanceof Exception)
			$this->sendErrorResponse($productoPager->getMessage());

		$productoList = $producto->getList($search);
		if($productoList instanceof Exception)
			$this->sendErrorResponse($productoList->getMessage());

		$pages = ceil(count($productoPager) / $paginate['per_page']);

		$this->sendSuccessResponse(array(
				'search' 		=> $search,
				'productos' 	=> $productos,
				'total' 		=> count($productoList),
				'pages' 		=> $pages,
				'page' 			=> $paginate['page']
			));

	}

	public function getProductoById($id=null){
		$producto = new Application_Model_Producto();		
		$params 	 = $params = $this->getRequest()->getParams();
		//die(var_dump($id));
	}
		
		
		
	
}

?>