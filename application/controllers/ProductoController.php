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
			if ($file['size'] > 0) {

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['nombre']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['imagen_url'] = $img['message'];
			}else{
				$img['message'] = NULL;
			}
           // $params['eliminado'] = 0;
			
			$producto = new Application_Model_Producto();
			$productoEquivalenteModel = new Application_Model_ProductoEquivalente();
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

            	$id_producto = $result;

				if (sizeof($params['id_original']) > 1) {
					foreach ($params['id_original'] as $id_original) {
		            	$paramsEquivalente = array(
							"id_original" 	=> $id_original,
							"id_producto" 	=> $params['codigo']
						);
						$resultEquivalentes = $productoEquivalenteModel->add($paramsEquivalente);
					}
				}

				

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
			$originalModel = new Application_Model_ProductoOriginal();

			$listadoMarcas = $marcaModel->getList();
			$listadoOriginal = $originalModel->getList();

			$this->view->listadoMarcas = $listadoMarcas;
			$this->view->listadoOriginal = $listadoOriginal;
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

				$productoEquivalenteModel = new Application_Model_ProductoEquivalente();

				// elimino los equivalentes
				$resultEquivalentes = $productoEquivalenteModel->remove('$id_producto',$params['codigo']);

				// vuelvo a crear los equivalentes

   				if (sizeof($params['id_original']) > 1) {
					foreach ($params['id_original'] as $id_original) {
		            	$paramsEquivalente = array(
							"id_original" 	=> $id_original,
							"id_producto" 	=> $params['codigo']
						);
						$resultEquivalentes = $productoEquivalenteModel->add($paramsEquivalente);
					}
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

    public function getproductosAction(){
		$producto = new Application_Model_Producto();		
		$id_proveedor = NULL;
		$params["id_proveedor"] = NULL;
		$params 	 = $params = $this->getRequest()->getParams();
		// die(var_dump($params));
		if ($params["id_proveedor"]) {
			$id_proveedor = $params["id_proveedor"];
		}
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

		$productos = $producto->getListFiltered($search,$paginate,$id_proveedor);
	// die(var_dump($productos));
		if($productos instanceof Exception)
			$this->sendErrorResponse($productos->getMessage());
		$productoPager = $producto->getListFiltered($search,NULL);
		if($productoPager instanceof Exception)
			$this->sendErrorResponse($productoPager->getMessage());

		$productoList = $producto->getList($search);
		if($productoList instanceof Exception)
			$this->sendErrorResponse($productoList->getMessage());

		$pages = ceil(count($productoPager) / $paginate['per_page']);
// die(var_dump(count($productoPager)));
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

	public function verequivalentesAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$codigo = $params['codigo'];
		
			$productoEquivalenteModel = new Application_Model_ProductoEquivalente();
			$listadoEquivalentes = $productoEquivalenteModel->getEquivalentes($codigo);
		// die(var_dump($listadoEquivalentes));
		$arrayEquivalente= [];
			
		// 	$a = array_unique($listadoEquivalentes['id_producto_equivalente']);
			foreach ($listadoEquivalentes as $equivalente) {

				
				$arrayEquivalente[] = $equivalente['id_producto_equivalente'];
				$arrayEquivalente[] = $equivalente['id_original'];
			}
		$arrayEquivalente = array_unique($arrayEquivalente);


			$this->view->listadoEquivalentes =  $arrayEquivalente;
			$render = $this->view->render('/producto/modal_productos_equivalentes.phtml');
            return $this->sendSuccessResponse($render);
		}
	}

	public function addoriginalAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();

			$productoOriginalModel = new Application_Model_ProductoOriginal();
			$productoOriginal = $productoOriginalModel->getProductosByParams($params['id_producto_original']);
			
			if($productoOriginal instanceof Exception){
                Gabinando_Base::addError($productoOriginal->getMessage());
                $this->_redirect('/producto/add');
            // si el producto no fue agregado anteriormente -> agrego nuevo producto
            }elseif($productoOriginal == null){
            	$paramsProducto = array(
				    "id_producto_original" 		=> $params['id_producto_original'],
				    "descripcion"				=> $params['descripcion']
				);
				
           		$result = $productoOriginalModel->add($paramsProducto);
           		if($result instanceof Exception){
            		Gabinando_Base::addError('Ya existe un producto original con ese código.');
	                
            	}

            	Gabinando_Base::addSuccess('Producto agregado correctamente');
            	$this->_redirect('/producto/listoriginal');
            }
        	$this->_redirect('/producto/addoriginal');
		}
	}

	public function listoriginalAction() {
		$productoOriginalModel = new Application_Model_ProductoOriginal();
		$listadoProductos = $productoOriginalModel->getList();
		$this->view->listadoProductos = $listadoProductos;
    }
	    
	public function editoriginalAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			$productoOriginalModel = new Application_Model_ProductoOriginal();

			$productoExistente = $productoOriginalModel->getProductosByParams($params['id_producto_original'], $params['id']);
			
			if($productoExistente instanceof Exception){
                Gabinando_Base::addError($productoExistente->getMessage());
            // si el producto no fue agregado anteriormente -> edito producto
            }elseif($productoExistente == null){
            	$paramsProducto = array(
				    "id_producto_original" 		=> $params['id_producto_original'],
				    "descripcion"				=> $params['descripcion']
				);
			

				$resultProducto = $productoOriginalModel->edit($params['id'], $paramsProducto);

				

				if($resultProducto instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());

	            }else{
	            	           		
	            	Gabinando_Base::addSuccess('Producto actualizado correctamente');
	            	$this->_redirect('/producto/listoriginal');
	            }
	        }else{

            	Gabinando_Base::addError('Ya existe un producto con ese código.');
                $this->_redirect('/producto/editoriginal/id/'.$params['id']);
            	
            }

		}//si no es POST
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){

				$productoOriginalModel = new Application_Model_ProductoOriginal();
				$producto = $productoOriginalModel->getProductoById($id);
				
			// die(var_dump($producto));
		
				if($producto){
					$this->view->data = $producto;
				}
			}
	

		}
			// $this->_redirect('/producto/listoriginal');
    }
		
		
		
	
}

?>