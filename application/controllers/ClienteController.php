<?php

class ClienteController extends Gabinando_Base {

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// die(var_dump($params));
			// Set idDeleted property to false in order to "reactivate" the registered admin
            $params['eliminado'] = 0;
			$cliente = new Application_Model_cliente();
			$alreadyRegistered = $cliente->getClienteBycuit($params['cuit'],$params['email']);

			if(!is_null($alreadyRegistered)){
                Gabinando_Base::addError('Existe un cliente con ese CUIT o Email');
                $this->_redirect('/cliente/add');
            // If this email wasn't registered in the past -> add a new cliente
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

			$listadoEstados = $estadoModel->getList();

			$this->view->listadoEstados = $listadoEstados;
		}
	}

	public function listAction() {
		$cliente = new Application_Model_Cliente();
		$listadoclientes = $cliente->getList();
		$this->view->listadoclientes = $listadoclientes;
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
			$params['id'] = $this->getRequest()->getParam('id');

			$cliente = new Application_Model_Cliente();
			$prov = $cliente->getClienteById($params['id']);

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

				$listadoEstados = $estadoModel->getList();

				$this->view->listadoEstados = $listadoEstados;
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

	public function getClientesAction(){
		$patient = new Application_Model_Cliente();		
		$params 	 = $params = $this->getRequest()->getParams();
die(var_dump($params));
		if( isset($params['search']) ){
			$search 	 = array(
				'search' => $params['search'],
				'filter' => 'active'
			);
		}else{
			$search 	 = array(
				'search' => '',
				'filter' => 'active'
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

		$patientPager		 = $patient->getListFiltered($search);
		if($patientPager instanceof Exception)
			$this->sendErrorResponse($patientPager->getMessage());

		$clienteList = $cliente->getList($search);
		if($clienteList instanceof Exception)
			$this->sendErrorResponse($clienteList->getMessage());

		$pages = ceil(count($clientePager) / $paginate['per_page']);

		$this->sendSuccessResponse(array(
				'search' 	=> $search,
				'clientes' 	=> $clienteFilteredList,
				'total' 	=> count($patientList),
				'pages' 	=> $pages,
				'page' 		=> $paginate['page']
			));

	}
		
	
}

?>