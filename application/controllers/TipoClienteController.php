<?php

class TipoClienteController extends Gabinando_Base {

	// const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// $params['fecha'] = new DateTime($params['fecha']);
            $params['eliminado'] = 0;
			
			$tipo_cliente = new Application_Model_TipoCliente();
            $result = $tipo_cliente->add($params);

            Gabinando_Base::addSuccess('Tipo de cliente agregado correctamente');
            $this->_redirect('/tipocliente/list');
            
		}else{
			$productoModel = new Application_Model_TipoCliente();
			$tipoClienteModel = new Application_Model_TipoCliente();

			$listadoTipoClientes = $tipoClienteModel->getList();
			$listadoProductos = $productoModel->getList();

			$this->view->listadoProductos = $listadoProductos;
			$this->view->listadoTipoClientes = $listadoTipoClientes;
		}
	}

	public function listAction() {
		$precio = new Application_Model_TipoCliente();
		$listadoTipoClientes = $precio->getList();
		$this->view->listadoTipoClientes = $listadoTipoClientes;
    }

    public function removeAction(){
    	$id_precio = $this->getRequest()->getParam('id');
    	$precio = new Application_Model_TipoCliente();
		$result = $precio->remove('id',$id_precio);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Tipo de cliente eliminado correctamente');
		}
		$this->_redirect('/tipocliente/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			
			$precio = new Application_Model_TipoCliente();
			$result = $precio->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{
            	           		
            	Gabinando_Base::addSuccess('Tipo de cliente actualizado correctamente');
            }

            $this->_redirect('/tipocliente/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$tipoClienteModel = new Application_Model_TipoCliente();
				$tipoClientes = $tipoClienteModel->getTipoClienteById($id);

				$this->view->tipoClientes = $tipoClientes;

				if($tipoClientes){
					$this->view->data = $tipoClientes;
				}
				else{
					$this->_redirect('/tipocliente/list');
				}
			}
			else{
				$this->_redirect('/tipocliente/list');
			}
		}
    }
		
		
	
}

?>