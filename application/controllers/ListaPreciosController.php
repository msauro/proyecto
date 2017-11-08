<?php

class ListaPreciosController extends Gabinando_Base {

	// const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['fecha_vigencia'] = date("Y-m-d", strtotime($params['fecha_vigencia']));
            $params['eliminado'] = 0;
			$lista_precios = new Application_Model_ListaPrecios();
            $result = $lista_precios->add($params);
            if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());
                $this->_redirect('/listaprecios/list');
        	}
            Gabinando_Base::addSuccess('Lista de precios agregada correctamente');
            $this->_redirect('/listaprecios/list');
            
		}else{
			$tipoClienteModel = new Application_Model_TipoCliente();
			$listadoTipoCliente = $tipoClienteModel->getListClientes();

			$this->view->listadoTipoCliente = $listadoTipoCliente;
		}
	}

	public function listAction() {
		$lista_precios = new Application_Model_ListaPrecios();
		$listas_precios = $lista_precios->getList();
		$this->view->listas_precios = $listas_precios;
    }

    public function removeAction(){
    	$id_precio = $this->getRequest()->getParam('id');
    	$precio = new Application_Model_ListaPrecios();
		$result = $precio->remove('id',$id_precio);
		if($result instanceof Exception){
			Gabinando_Base::addError($result->getMessage());
		}else{
			Gabinando_Base::addSuccess('Lista de precios eliminada correctamente');
		}
		$this->_redirect('/listaprecios/list');

	}

	public function editAction() {
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			$params['id'] = $this->getRequest()->getParam('id');
			$params['fecha_vigencia'] = date("Y-m-d", strtotime($params['fecha_vigencia']));
			
			$listaprecioModel = new Application_Model_ListaPrecios();
			$result = $listaprecioModel->edit($params['id'], $params);

			if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());

            }else{
            	           		
            	Gabinando_Base::addSuccess('Lista de precios actualizada correctamente');
            }

            $this->_redirect('/listaprecios/list');
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$listaprecioModel = new Application_Model_ListaPrecios();
				$listaPrecio = $listaprecioModel->getListaPrecioById($id);

				$tipoClienteModel = new Application_Model_TipoCliente();
				$listadoTipoCliente = $tipoClienteModel->getListClientes();

				$this->view->listadoTipoCliente = $listadoTipoCliente;
				$this->view->data = $listaPrecio;

			}
			// else{
			// 	$this->_redirect('/listaprecios/list');
			// }
		}
    }
		
		
	
}

?>