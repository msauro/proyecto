<?php

class ClienteController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$file = $_FILES['imagen_url'];
			//solo intenta subir la foto si el tamaño es mayor a cero
			if ($file['size'] > 0) {
				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['email']);

				if($img['status'] == 'error'){
					die($img['message']);
				}
			}
			//si es monotributista, no es Resp. Inscripto, por lo tanto sería como exento
			if ($params['id_tipo_empresa'] == 1) {
				$params['exento'] = 1;
			}

			$params['estado'] = 1;

			$params['imagen_url'] = $img['message'];
            $params['eliminado'] = 0;
			$cliente = new Application_Model_cliente();
			$alreadyRegisteredByCuit = $cliente->getClienteByCuit($params['cuit']);
			$alreadyRegisteredByEmail = $cliente->getClienteByEmail($params['email']);

			if(!is_null($alreadyRegisteredByCuit) OR !is_null($alreadyRegisteredByEmail)){
                Gabinando_Base::addError('Existe un cliente con ese CUIT o Email');
                $this->_redirect('/cliente/add');
            }else{
           		$result = $cliente->add($params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/cliente/add');
            	}
            	
            	Gabinando_Base::addSuccess('Cliente agregado correctamente');
            	$this->_redirect('/cliente/list');
            }
		}else{
			$estadoModel = new Application_Model_Estado();
			$tiposclientesModel = new Application_Model_TipoCliente();

			$listadoEstados = $estadoModel->getList();
			$listadoTiposClientes = $tiposclientesModel->getList();

			$tipoempresa = new Application_Model_TipoEmpresa();
			$listadoTipoempresa = $tipoempresa->getList();
			
			$this->view->listadoTipoempresa = $listadoTipoempresa;
			$this->view->listadoEstados = $listadoEstados;
			$this->view->listadoTiposClientes = $listadoTiposClientes;
		}
	}

	public function listAction() {
		$cliente = new Application_Model_Cliente();
		// $ventaModel = new Application_Model_Venta();

		$listadoclientes = $cliente->getList();
		// $deuda = $ventaModel->getDeuda($params['id']);
// die(var_dump($deuda));
		$this->view->listadoclientes = $listadoclientes;
		$this->view->deuda = $deuda;

    }

    public function removeAction(){
    	$id_cliente = $this->getRequest()->getParam('id');
    	$cliente = new Application_Model_cliente();
		$result = $cliente->remove('id',$id_cliente);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Cliente eliminado correctamente');
		}
		$this->_redirect('cliente/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// die(var_dump($params));
			$params['id'] = $this->getRequest()->getParam('id');

			$cliente = new Application_Model_Cliente();
			$prov = $cliente->getClienteById($params['id']);

			if($_FILES['imagen_url']['size'] > 0){
				$file = $_FILES['imagen_url'];

				$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['id']);

				if($img['status'] == 'error'){
					die($img['message']);
				}

				$params['imagen_url'] = $img['message'];
			}

			$error = null;
			if ($prov['cuit'] != $params['cuit']) {
				$alreadyRegistered = $cliente->getClienteByCuit($params['cuit']);
				if(!is_null($alreadyRegistered)) $error = 'Existe un cliente con ese CUIT';
			}

			if ($prov['email'] != $params['email']) {
				$alreadyRegistered = $cliente->getClienteByEmail($params['email']);
				if(!is_null($alreadyRegistered)) $error = 'Existe un cliente con ese Email';
			}


			if(!is_null($error)){
                Gabinando_Base::addError($error);
                $this->_redirect('/cliente/edit');
            }else{
				$result = $cliente->edit($params['id'], $params);
				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/cliente/edit');
            	}
            	
            	Gabinando_Base::addSuccess('Cliente editado correctamente');
	            $this->_redirect('/cliente/list');

            }


		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$estadoModel = new Application_Model_Estado();
				$tiposclientesModel = new Application_Model_TipoCliente();
				
				$listadoTiposClientes = $tiposclientesModel->getList();
				$listadoEstados = $estadoModel->getList();

				$this->view->listadoEstados = $listadoEstados;
				$this->view->listadoTiposClientes = $listadoTiposClientes;

				$clienteModel = new Application_Model_Cliente();
				$cliente = $clienteModel->getClienteById($id);
				if($cliente){
					$this->view->data = $cliente;
				}
				else{
					$this->_redirect('/cliente/list');
				}
			}
			else{
				$this->_redirect('/cliente/list');
			}
		}
    }

	public function getclientesAction(){
		$cliente = new Application_Model_Cliente();		
		$params 	 = $params = $this->getRequest()->getParams();
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

		$paginate['per_page'] 	= 6;

		$paginate['start_from'] = ($paginate['page']-1) * $paginate['per_page'];

		$clienteFilteredList = $cliente->getListFiltered($search,$paginate);
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

	public function ultimascomprasAction(){
        $params = $this->getRequest()->getParams();
			$ventaModel = new Application_Model_Venta();
			$ultimasCompras = $ventaModel->ultimasVentas($params['id_client']);

			if($ultimasCompras instanceof Exception){
			    $this->addError("Error al mostrar las últimas compras.");
			}else{
			    $this->view->ultimasCompras = $ultimasCompras;
			}


		$render = $this->view->render('/cliente/modal_ultimas_compras.phtml');
        return $this->sendSuccessResponse($render);
		

	}

	public function resumenAction(){
		$params = $this->getRequest()->getParams();

		$ventaModel = new Application_Model_Venta();
		$listadoPendientes = $ventaModel->getPendientes($params['id']);
		$deuda = $ventaModel->getDeuda($params['id']);
// die(var_dump($deuda));

		if($listadoPendientes instanceof Exception){
		    $this->addError("Error al mostrar las últimas compras.");
		}else{
		    $this->view->listadoPendientes = $listadoPendientes;
		    $this->view->deuda = $deuda;
		}


	
	}

	
		



}

?>