<?php

class ListaPreciosController extends Gabinando_Base {

	// const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			// $params['fecha'] = new DateTime($params['fecha']);
            $params['eliminado'] = 0;
			
			$params['fecha'] = strtotime($params['fecha']);

            $params['fecha'] = date("Y-m-d",($params['fecha']));
			
			$lista_precios = new Application_Model_ListaPrecios();
            $result = $precio->add($params);

            Gabinando_Base::addSuccess('Lista de precios agregada correctamente');
            $this->_redirect('/listaprecios/list');
            
		}else{
			$tipoClienteModel = new Application_Model_TipoCliente();
			$listadoTipoCliente = $tipoClienteModel->getListClientes();
	// die(var_dump($listadoTipoCliente));
			$this->view->listadoTipoCliente = $listadoTipoCliente;
		}
	}

	public function listAction() {
		$precio = new Application_Model_ListaPrecios();
		$listadoprecios = $precio->getList();
		$this->view->listadoprecios = $listadoprecios;
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
			
			$precio = new Application_Model_ListaPrecios();
			$result = $precio->edit($params['id'], $params);

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
				$marcaModel = new Application_Model_Marca();

				$listadoMarcas = $marcaModel->getList();

				$precioModel = new Application_Model_Precio();
				$precio = $precioModel->getPrecioById($id);

				$this->view->listadoMarcas = $listadoMarcas;

				if($precio){
					$this->view->data = $precio;
				}
				else{
					$this->_redirect('/listaprecios/list');
				}
			}
			else{
				$this->_redirect('/listaprecios/list');
			}
		}
    }
		
		
	
}

?>