<?php

class ProductoProveedorController extends Gabinando_Base {

	public function init(){
		parent::init();
	}

	public function addAction(){
			if($this->getRequest()->isPost()){
				$params = $this->getRequest()->getPost();
				$productoproveedorModel = new Application_Model_ProductoProveedor();
				$productoExistente = $productoproveedorModel->getProductosByParams($params['cod_producto_proveedor']);
				
				if($productoExistente instanceof Exception){
	                Gabinando_Base::addError($productoExistente->getMessage());
	                $this->_redirect('/productoproveedor/add');
	            // si el producto no fue agregado anteriormente -> agrego nuevo producto
	            }elseif($productoExistente == null){
	            	$paramsProducto = array(
					    "id_proveedor"				=> $params['id_proveedor'],
					    "codigo_producto_proveedor" 	=> $params['cod_producto_proveedor'],
					    "codigo_producto"				=> $params['cod_producto'],
					);
	           		$result = $productoproveedorModel->add($paramsProducto);
	           		if($result instanceof Exception){
		                Gabinando_Base::addError($result->getMessage());
	            	}
	           		
	            	Gabinando_Base::addSuccess('Producto del proveedor agregado correctamente');
	            	$this->_redirect('/productoproveedor/list');
	            }else{

	            	Gabinando_Base::addError('Ya existe un producto con ese código para esa marca.');
	            }
	        	$this->_redirect('/productoproveedor/add');
			}else{
				$productoModel = new Application_Model_Producto();
				$proveedorModel = new Application_Model_Proveedor();
				
				$listadoProveedores = $proveedorModel->getList();
				$listadoProductos = $productoModel->getList();

				$this->view->listadoProveedores = $listadoProveedores;
				$this->view->listadoProductos = $listadoProductos;
			}
	}

	public function costoAction(){
			if($this->getRequest()->isPost()){
				$params = $this->getRequest()->getPost();
				$costoproductoproveedorModel = new Application_Model_ProductoProveedorPrecio();
				$fecha = date("Y-m-d", strtotime($params['fecha']));
				// die(var_dump($fecha));

            	$paramsProducto = array(
				    "id_proveedor"				=> $params['id_proveedor'],
				    "codigo_producto_proveedor" 	=> $params['codigo_producto_proveedor'],
				    "fecha"				=> $fecha,
				    "descuento"				=> $params['descuento'],
				    "precio"				=> $params['precio']
				);

           		$result = $costoproductoproveedorModel->add($paramsProducto);
           		if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
            	}
           		
            	Gabinando_Base::addSuccess('Costo del producto del proveedor agregado correctamente');
	            
	        	$this->_redirect('/productoproveedor/costo');
			}else{
				$productoproveedorModel = new Application_Model_ProductoProveedor();
				$proveedorModel = new Application_Model_Proveedor();
				
				$listadoProveedores = $proveedorModel->getList();
				$listadoProductos = $productoproveedorModel->getProductos();

				$this->view->listadoProveedores = $listadoProveedores;
				$this->view->listadoProductos = $listadoProductos;
			}
	}

	public function listAction() {
		$producto = new Application_Model_ProductoProveedor();
		$listadoProductos = $producto->getList();
		$this->view->listadoProductos = $listadoProductos;
    }

    public function getproductosAction(){
		$productoproveedorModel = new Application_Model_ProductoProveedor();		
		$id_proveedor = NULL;
		$params["id_proveedor"] = NULL;
		$params 	 = $params = $this->getRequest()->getParams();
		
		if ($params["id_proveedor"]) {
			$id_proveedor = $params["id_proveedor"];
		}

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

		$productos = $productoproveedorModel->getListFiltered($search,$paginate,$id_proveedor);
		if($productos instanceof Exception)
			$this->sendErrorResponse($productos->getMessage());
		$productoPager = $producto->getListFiltered($search,NULL);
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

	public function getcodigosproveedorAction(){
		if($this->getRequest()->isPost()){
			$params = $params = $this->getRequest()->getParams();
			$producto = new Application_Model_ProductoProveedor();
			$listadoProductos = $producto->getProductosByProveedor($params["id_proveedor"]);
			// die(var_dump($listadoProductos));
			$this->sendSuccessResponse(array(
				'listadoProductos' 		=> $listadoProductos
				// 'pages' 		=> $pages,
				// 'page' 			=> $paginate['page']
			));

			// die('TERMINAR SELECT, SI ELIJO UN PROVEEDOR, SOLO ME MUESTRA LOS PRODUCTOS DEL SELECCIONADO');
			// $this->view->listadoProductospByProv = $listadoProductos;


		}
	}

	public function verequivalentes(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();


		}
	}



}