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
		$params = $this->getRequest()->getParams();
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
		$productoPager = $productoproveedorModel->getListFiltered($search,NULL,NULL);
		if($productoPager instanceof Exception)
			$this->sendErrorResponse($productoPager->getMessage());

		$productoList = $productoproveedorModel->getList($search);
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
			$this->sendSuccessResponse(array(
				'listadoProductos' 		=> $listadoProductos
				// 'pages' 		=> $pages,
				// 'page' 			=> $paginate['page']
			));

			// die('TERMINAR SELECT, SI ELIJO UN PROVEEDOR, SOLO ME MUESTRA LOS PRODUCTOS DEL SELECCIONADO');
			// $this->view->listadoProductospByProv = $listadoProductos;


		}
	}

	//FALTA TERMINAR
	public function compararcostoAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();

		}
	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			$paramsProducto= array();
			if($_FILES['imagen_url']['size'] > 0){
				$file = $_FILES['imagen_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$paramsProducto["imagen_url"] = $img['message'];
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
				    "punto_pedido"	=> $params['punto_pedido'],
				    "descripcion"	=> $params['descripcion']
				);
				$paramsStock = array(
				    "id_producto" 	=> $params['id'],
				    "cantidad" 		=> $params['cantidad'],
					'fecha' 		=> date('Y-m-d H:i:s')

				);
				if($img['message']) {
					$paramsProducto["imagen_url"]= $img['message'];
					
				}
				$producto = new Application_Model_Producto();
				$stock = new Application_Model_Existencia();
				$resultProducto = $producto->edit($params['id'], $paramsProducto);
           		$resultStock = $stock->edit($params['id'], $paramsStock);

				$productoEquivalenteModel = new Application_Model_ProductoEquivalente();

				// elimino los equivalentes
				$resultEquivalentes = $productoEquivalenteModel->eliminarEquivalentes($params['codigo']);
				// vuelvo a crear los equivalentes

   				if (sizeof($params['id_original']) > 1) {
					foreach ($params['id_original'] as $id_original) {
		            	$paramsEquivalente = array(
							"id_original" 	=> $id_original,
							"id_producto" 	=> $params['codigo']
						);
						$resultEquivalentes = $productoEquivalenteModel->add($paramsEquivalente);
					}
				}else{
					$paramsEquivalente = array(
						"id_original" 	=> $params['id_original'][0],
						"id_producto" 	=> $params['codigo']
					);
					
					$resultEquivalentes = $productoEquivalenteModel->add($paramsEquivalente);
				}


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
				$producto['equivalente'] = $productoModel->getProductoEquivalenteById($producto['codigo']);
				$originalModel = new Application_Model_ProductoOriginal();
				$listadoOriginal = $originalModel->getList();
				
				if (!$producto['equivalente']) {
					$producto['equivalente'] = $listadoOriginal;
				}

				$this->view->listadoOriginal = $listadoOriginal;
				$this->view->listadoEquivalente = $producto['equivalente'];
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