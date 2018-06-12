<?php

class IndexController extends Gabinando_Base{

	public function init(){
		parent::init();
	}


    public function indexAction() {

    //  rango de fechas del dia de HOY
        $desde = date('Y-m-d 00:00:01');
        $hasta = date('Y-m-d 23:59:59');

        $mesActualDesde = date('Y-m-01 00:00:01');
        $mesActualHasta = date('Y-m-31 23:59:59');
        
        //widget Cantidad de ventas del día de hoy (OK)
        $ventasModel = new Application_Model_Venta();
        $cantVentas = $ventasModel->getCantVentasRango($desde,$hasta);

        //widget clientes con mas compras en el mes (OK)
        $clientesModel = new Application_Model_Cliente();
        $clientesMasVentas = $clientesModel->getClientesMasVentas($mesActualDesde,$mesActualHasta);
        $this->view->clientesMasVentas = $clientesMasVentas;

        //widget cantidad de clientes con/sin deudas
        $clientesModel = new Application_Model_Cliente();
        $clientesDeudas = $clientesModel->getClientesDeudas();

        //widget cantidad de productos en punto de pedido o menor por marca
        $existenciaModel = new Application_Model_Existencia();
        $cantPtoPedido = $existenciaModel->getCantPtoPedido();

// die(var_dump($cantPtoPedido));

        if($cantPtoPedido instanceof Exception){
            Gabinando_Base::addError($result->getMessage());
        }
        else{

          
            $this->view->cantVentas = $cantVentas;
            $this->view->cantPtoPedido = $cantPtoPedido;


            // $this->view->requestList = $requestList;

            // $widgetsData['Pending'] = 0;
            // $widgetsData['Cancelled'] = 0;
            // $widgetsData['Successful'] = 0;
            // $widgetsData['Waiting'] = 0;

            // foreach ($result['requests'] as $request){
            //     switch($request['status']){
            //         case 'Pending':
            //             $widgetsData['Pending']++;
            //             break;
            //         case 'Cancelled':
            //             $widgetsData['Cancelled']++;
            //             break;
            //         case 'Successful':
            //             $widgetsData['Successful']++;
            //             break;
            //         case 'Waiting':
            //             $widgetsData['Waiting']++;
            //             break;
            //     }
            // }
            // $this->view->widgetsData = $widgetsData;
        }
    }


    public function loginAction() {
    	
        $this->_helper->layout()->disableLayout();

        if(isset($this->admin_session->admin)){
            $this->_redirect('index');
        }

        if($this->getRequest()->isPost()){
            $params         = $this->getRequest()->getPost();
            $admin          = new Application_Model_Usuario();
            $loginResult    = $admin->isValidLogin($params);
            if($loginResult instanceof Exception) {
                $this->view->error = 'Se produjo un error, intente mas tarde.';
            }
			elseif(!$loginResult){
                $this->view->error = 'E-mail o contraseña incorrectos.';
            }
            else{
                $this->admin_session->admin = $loginResult;
                $this->_redirect('index');
            }
        }
    }


    public function logoutAction(){
        $this->admin_session->admin = null;
        $this->_redirect('index/login');
    }

    public function printAction(){
       
        $this->_helper->layout()->disableLayout();
        $params = $this->getRequest()->getParams();

        if(isset($params['id'])){
            $request = new Application_Model_Request();
            $result = $request->getOne($params['id']);

            if($result instanceof Exception){
                Gabinando_Base::addError($result->getMessage());
            }
            else{

                $this->view->request = $result;

                $setting = new Application_Model_Settings();
                $result = $setting->getSettings(1);

                if($result instanceof Exception){
                    Gabinando_Base::addError($result->getMessage());
                }
                else{
                    
                    $this->view->footer = $result;
                }
            }
        }
    }
}