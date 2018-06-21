<?php

class ProveedorController extends Gabinando_Base {

	public function init(){
		parent::init();
		if ($this->admin){
			$this->_redirect('index/');
		}
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			$proveedor = new Application_Model_Proveedor();
			$alreadyRegisteredByCuit = $proveedor->getProveedorByCuit($params['cuit']);
			$alreadyRegisteredByEmail = $proveedor->getProveedorByEmail($params['email']);

			if(!is_null($alreadyRegisteredByCuit) OR !is_null($alreadyRegisteredByEmail)){
                Gabinando_Base::addError('Existe un proveedor con ese CUIT o Email');
                $this->_redirect('/proveedor/edit/id/'.$params['id']);
            // If this email wasn't registered in the past -> add a new proveedor
            }else{
           		$result = $proveedor->add($params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/proveedor/add');
            	}
            	Gabinando_Base::addSuccess('Proveedor agregado correctamente');
            	$this->_redirect('/proveedor/list');
            }
		}else{
			// $paisModel 		= new Application_Model_Pais();
			// $provinciaModel = new Application_Model_Provincia();
			// $ciudadModel 	= new Application_Model_Ciudad();

			// $listadoPaises 		= $paisModel->getList();
			// $listadoProvincias 	= $provinciaModel->getList();
			// $listadoCiudades 	= $ciudadModel->getList();

			$this->view->listadoMarcas = $listadoMarcas;
		}
	}

	public function listAction() {
		$proveedor = new Application_Model_Proveedor();
		$listadoProveedores = $proveedor->getList();
		$this->view->listadoProveedores = $listadoProveedores;
    }

    public function removeAction(){
    	$id_proveedor = $this->getRequest()->getParam('id');
    	$proveedor = new Application_Model_Proveedor();
		$result = $proveedor->remove('id',$id_proveedor);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Proveedor eliminado correctamente');
		}
		$this->_redirect('proveedor/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');

			$proveedor = new Application_Model_Proveedor();
			$prov = $proveedor->getProveedorById($params['id']);

			$error = null;
			if ($prov['cuit'] != $params['cuit']) {
				$alreadyRegistered = $proveedor->getProveedorByCuit($params['cuit']);
				if(!is_null($alreadyRegistered)){
					$error = 'Existe un proveedor con ese CUIT';
				} 
			}

			if ($prov['email'] != $params['email']) {
				$alreadyRegistered = $proveedor->getProveedorByEmail($params['email']);
				if(!is_null($alreadyRegistered)){
					$error = 'Existe un proveedor con ese Email';
				}
			}

			if(!is_null($error)){
                Gabinando_Base::addError($error);
                $this->_redirect('/proveedor/edit/id/'.$params['id']);
            }else{
				$result = $proveedor->edit($params['id'], $params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/proveedor/edit');
            	}
            	
            	Gabinando_Base::addSuccess('Proveedor editado correctamente');
	            $this->_redirect('/proveedor/list');

            }
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$proveedorModel = new Application_Model_proveedor();
				$proveedor = $proveedorModel->getProveedorById($id);

				if($proveedor){
					$this->view->data = $proveedor;
				}
				else{
					$this->_redirect('/proveedor/list');
				}
			}
			else{
				$this->_redirect('/proveedor/list');
			}
		}
    }

    public function getproveedoresAction(){
		$proveedorModel = new Application_Model_Proveedor();		
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

		if (isset($params['page']) AND $params['page']) {
			$paginate['page']  	= $params['page'];
		} else {
			$paginate['page'] 	= 1;
		}

		$paginate['per_page'] 	= 15;

		$paginate['start_from'] = ($paginate['page']-1) * $paginate['per_page'];

		$proveedor = $proveedorModel->getListFiltered($search,$paginate);
		if($proveedor instanceof Exception)
			$this->sendErrorResponse($proveedor->getMessage());
		$proveedorPager = $proveedorModel->getListFiltered($search);
		if($proveedorPager instanceof Exception)
			$this->sendErrorResponse($proveedorPager->getMessage());

		$proveedorList = $proveedorModel->getList($search);
		if($proveedorList instanceof Exception)
			$this->sendErrorResponse($proveedorList->getMessage());

		$pages = ceil(count($proveedorPager) / $paginate['per_page']);
		$this->sendSuccessResponse(array(
				'search' 		=> $search,
				'proveedores' 	=> $proveedor,
				'total' 		=> count($proveedorList),
				'pages' 		=> $pages,
				'page' 			=> $paginate['page']
			));

	}
		
	public function addproductsAction(){
			if($this->getRequest()->isPost()){
				$params = $this->getRequest()->getPost();

				$productoproveedorModel = new Application_Model_ProductoProveedor();
				$productoExistente = $productoproveedorModel->getProductosByParams($params['codigo_proveedor'], $params['id_marca']);

				
				if($productoExistente instanceof Exception){
	                Gabinando_Base::addError($productoExistente->getMessage());
	                $this->_redirect('/proveedor/addproducts');
	            // si el producto no fue agregado anteriormente -> agrego nuevo producto
	            }elseif($productoExistente == null){
	            	$paramsProducto = array(
					    "id_proveedor"			=> $params['id_proveedor'],
					    "codigo_proveedor" 		=> $params['codigo_proveedor'],
					    "descripcion"			=> $params['descripcion'],
					    "descuento"				=> $params['descuento'],
					    "precio"				=> $params['precio'],
					    "fecha"					=> $params['fecha'],
					    "id_marca" 				=> $params['id_marca']
					);
					
	           		$result = $productoproveedorModel->add($paramsProducto);

	           		if($result instanceof Exception){
		                Gabinando_Base::addError($result->getMessage());
	            	}
	           		
	            	Gabinando_Base::addSuccess('Producto del proveedor agregado correctamente');
	            	$this->_redirect('/proveedor/listproducts');
	            }else{

	            	Gabinando_Base::addError('Ya existe un producto con ese código para esa marca.');
	            }
	        	$this->_redirect('/proveedor/addproducts');
			}else{
				$productoModel = new Application_Model_Producto();
				$marcaModel = new Application_Model_Marca();
				$originalModel = new Application_Model_ProductoOriginal();
				$proveedorModel = new Application_Model_Proveedor();
				
				$listadoProveedores = $proveedorModel->getList();
				$listadoProductos = $productoModel->getList();
				$listadoMarcas = $marcaModel->getList();
				$listadoOriginal = $originalModel->getList();

				$this->view->listadoProveedores = $listadoProveedores;
				$this->view->listadoMarcas = $listadoMarcas;
				$this->view->listadoOriginal = $listadoOriginal;
			}
	}
	
}

?>