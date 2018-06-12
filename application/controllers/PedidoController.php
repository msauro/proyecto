<?php

class PedidoController extends Gabinando_Base{

	public function indexAction(){
		$ventaModel= new Application_Model_Venta();
		// $driverModel = new Application_Model_Driver();

		if($this->getRequest()->isPost()){
			$from = date("Y-m-d",strtotime(str_replace("-","/",$this->getRequest()->getParam('from'))));
			$to = date("Y-m-d",strtotime(str_replace("-","/",$this->getRequest()->getParam('to'))));
			// $id_driver = $this->getRequest()->getParam('id_driver');

			if(!isset($id_driver) || $id_driver == 'all') $id_driver = null;
			$donations = $ventaModel->getListFromTo($this->admin_session->admin['id_dispenser'],$from,$to,$id_driver,'delivery');
		}else{
			$from = date("Y-m-d");
			//$from = date("Y-m-d", strtotime("-2 weeks"));
			$to = date("Y-m-d");
			$donations 	= $ventaModel->getListFromTo($this->admin_session->admin['id_dispenser'],$from,$to,null,'delivery');
		}

		$drivers 	= $driverModel->getDriversSummary($from,$to);
		foreach ($drivers as $key => $driver) {
			$driver_donations = $ventaModel->getPendingNotFixedDonationsForDriver($driver['id_driver'],'delivery');
			$drivers[$key]['pending'] = count($driver_donations);
			$donation = end($driver_donations);

			$eta = strtotime(date("Y-m-d h:i a", strtotime("+30 minutes")));
			$don_eta = 0;
			if ($donation) $don_eta = strtotime($donation['arrival_time']) + ($donation['service_time']*60) + (30*60);
			if ($eta > $don_eta) $drivers[$key]['eta'] = date("h:i a", $eta); else $drivers[$key]['eta'] = date("h:i a", $don_eta);
		}

		$_don['success'] 	= 0;
		$_don['cancel']		= 0;
		$_don['pending'] 	= 0;
		foreach ($donations as $key => $donation) {
			if ($donation['status']=='success') {
				$_don['success'] = $_don['success'] + 1;
			}elseif($donation['status']=='cancel'){
				$_don['cancel'] = $_don['cancel'] + 1;
			}elseif ($donation['status']=='pending') {
				$_don['pending'] = $_don['pending'] + 1;
			}
		}

	    $this->view->from = $from;
		$this->view->to = $to;
		$this->view->donations = $donations;
		$this->view->don = $_don;
		$this->view->drivers = $drivers;
		$this->view->id_driver = $id_driver;
	}

	public function addAction(){
		$params = $this->getRequest()->getParams();
		if (isset($params['patient'])) {
			$this->view->patient = $params['patient'];
		}
		// $patient = new Application_Model_Patient();
		$clientesModel = new Application_Model_Cliente();
		$ventasModel	= new Application_Model_Venta();
		$productosProveedorModel	= new Application_Model_ProductoProveedor();
		
		
		$ventas = $ventasModel->getList();
		
		$clientestypeList = $clientesModel->getListType();
		// $productospreciosList = $productosModel->getListPrecios($tipoCliente,$hoy);

		// $drivers = $driver->getDriversWithAreaWork();
		// $patientList = $patient->getAllowedList($this->admin_session->admin['id_dispenser'],6);

		// foreach ($drivers as $key => $driver) {
		// 	$driver_donations = $ventaModel->getPendingNotFixedDonationsForDriver($driver['id_driver'],'delivery');
		// 	$today_donations = $ventaModel->getTodayDonationsForDriver($driver['id_driver'],'delivery');
		// 	$drivers[$key]['pending'] = count($driver_donations);
		// 	$drivers[$key]['queue'] = count($today_donations);
		// 	$donation = end($driver_donations);

		// 	$eta = strtotime(date("Y-m-d h:i a", strtotime("+30 minutes")));
		// 	$don_eta = 0;
		// 	if ($donation) $don_eta = strtotime($donation['arrival_time']) + ($donation['service_time']*60) + (30*60);
		// 	if ($eta > $don_eta) $drivers[$key]['eta'] = date("h:i a", $eta); else $drivers[$key]['eta'] = date("h:i a", $don_eta);
		// }
		
		//$this->view->patients = $patientList;

		// $rewardModel = new Application_Model_Reward();
		// $reward = $rewardModel->getListbyValue();
		
		// $this->view->dispenser = $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);	
		// $this->view->patients = array();
		$this->view->clientes = $clientestypeList;
		// $this->view->drivers = $drivers;
		// $this->view->productos = $productospreciosList;

		// $this->view->reward = $reward;
		// $this->view->tax = $this->getConfig("tax");	
		// $this->view->discount = $discount;		
	}

	

	public function viewAction(){
		$id_donation = $this->getRequest()->getParam('id');
		
		$discountModel = new Application_Model_Discounts();
		$discount = $discountModel->getDiscountTypeDonationAndStandard($id_donation);	

		if($id_donation){
			$productModel = new Application_Model_Product();
			$ventaModel= new Application_Model_Venta();
			$donation = $ventaModel->getFullDonationForAdmin($id_donation);
			if($donation){
				$dispenserModel = new Application_Model_Dispenser();
				$dEventsModel = new Application_Model_Donationevents();
				$patientModel = new Application_Model_Patient();
				$rewardModel = new Application_Model_Reward();

				$this->view->tax = $this->getConfig("tax");	

				$reward = $rewardModel->getListbyValue();
				$this->view->reward = $reward;
				
				$this->view->events = $dEventsModel->getAllEvents($id_donation);
				$this->view->dispenser = $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);

				$locations =  $patientModel->getLocations($donation['id_patient']);
				foreach ($locations as $key => $location) {
					if($location['name'] == $donation['address']['name']) $donation['id_location'] = $location['id_location'];
				}

				$data = array();
				foreach ($donation['products'] as $product) {
					// Buscar los tier price de cada simple
					$prices = $productModel->getProductTierPrices($product['id_product']);
					if($prices instanceof Exception)
						return $this->sendErrorResponse($prices->getMessage());
					if($prices){
						$product['prices'] = $prices;
					}else{
						$product['prices'] = array();
					}
					$data[] = $product;
				}

				$donation['products'] = $data;
				$donation['disc_amount'] = $discount['disc_amount'];
				$couponModel = new Application_Model_Coupons();
				$donation['coupon'] = $couponModel->getDonationCoupon($id_donation);
				if($donation['points'])
					$donation['points_discount'] = $this->getAmountbyPoints($donation['points']);				
				$this->view->donation = $donation;			
			}else{
				$this->_redirect('admin_donations');
			}
		}else{
			$this->_redirect('admin_donations');
		}
		$this->view->discount = $discount;
	}

	public function editAction(){
		$id_donation = $this->getRequest()->getParam('id');
		if($id_donation){

			$ventaModel= new Application_Model_Venta();								
			$donation = $ventaModel->getFullDonationForAdmin($id_donation);

			if($donation){
				$dispenserModel = new Application_Model_Dispenser();
				$donationAddressModel = new Application_Model_Donationaddresses();
				$patientModel = new Application_Model_Patient();
				$productModel = new Application_Model_Product();
				$driverModel = new Application_Model_Driver();
			
				$this->view->patient = $patientModel->getPatient($donation['id_patient']);
				$this->view->phones = $patientModel->getPhones($donation['id_patient']);
				$this->view->locations = $patientModel->getLocations($donation['id_patient']);
				$this->view->drivers = $driverModel->getDriversWithAreaWork();					
                $this->view->donationAddress = $donationAddressModel->getDonationAddress($id_donation);  
                $this->view->times = $driverModel->allowedtimeAction($donation['id_driver'],"",$this->admin_session->admin['id_dispenser']);
                $this->view->timeselected = $driverModel->getrangetimeAction($donation['arrival_time']);
				$this->view->dispenser = $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);
			
				$this->view->donation = $donation;
			}else{
				$this->_redirect('admin_donations');
			}
		}else{
			$this->_redirect('admin_donations');
		}
	}



	public function setpedidoAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getPost();
			$productos = $params['productos'];
			$proveedorModel  	= new Application_Model_Proveedor();
			$proveedor	   	= $proveedorModel->getProveedorById($params['data']['id_proveedor']);
			
			try{
				$pedidoModel= new Application_Model_Pedido();
				$pedidoObj = array(
						'id_proveedor' 		=> $params['data']['id_proveedor'],
						'fecha' 			=> date('Y-m-d H:i:s')
					
				);
				

				// Start - Crea el pedido al proveedor
				$idPedido = $pedidoModel->add($pedidoObj);	
				// End - Crea el pedido al proveedor

				$pedidos_detalleModel = new Application_Model_PedidoDetalle();
				$productoModel	= new Application_Model_Producto();

				if ($params['data']['simple'] == 'true') {
					$id_producto = $productos['id_producto'];
						
					// Start - Crea linea de pedido
					$dProductsObj = array(
							'id_producto'	=> (int)$productos['id_producto'],
							'cantidad'		=> (float)$productos['cant'],
							'id_pedido'		=> (int)$idPedido
					);

					$pedidos_detalleModel->add($dProductsObj);

					//ACTUALIZA STOCK
					// $actualizarStock = array(
					// 	'id_producto'	=> $id_producto,
					// 	'cantidad'		=> $nuevaCantidad,
					// 	'fecha' 		=> date('Y-m-d H:i:s')
					// );

					//disminuye el stock en la existencia
					// $result = $existenciaModel->add($actualizarStock);
	            	// End Inventario

				}else{
					foreach ($productos as $prod) {
						$id_producto = $prod['id_producto'];
						
						// $productoMaxExistencia = $existenciaModel->getUltimaExistencia($id_producto);
						// $nuevaCantidad = $productoMaxExistencia['cantidad'] - $prod['cant'];

						// Start - Crea linea de pedido
						$dProductsObj = array(
								'id_producto'	=> (int)$prod['id_producto'],
								'cantidad'		=> (float)$prod['cant'],
								// 'precio'		=> $prod['precio'],
								'id_pedido'		=> (int)$idPedido
						);
						$pedidos_detalleModel->add($dProductsObj);
						
						// Start descontar stock Inventario 
						// $actualizarStock = array(
						// 	'id_producto'	=> $id_producto,
						// 	'cantidad'		=> $nuevaCantidad,
						// 	'fecha' 		=> date('Y-m-d H:i:s')
						// );
						//disminuye el stock en la existencia
						// $result = $existenciaModel->add($actualizarStock);
		            	// End Inventario
					}
				}
		
		

		

				// Start - Mail al proveedor para enviar el pedido

				// $sender 	= new Application_Model_Mail_Sender();
				// $message 	= "Hola ".$venta['nombre']." ".$venta['apellido']."! Gracias por tu compra. Total:".$venta['total']." <br><br> Fecha: " . date('d-m-Y h:i a',strtotime($venta['fecha'])) ;

				// $result 	= $sender->sendEmail($venta['email'],"Nueva compra",$message);
				// End - Mail al cliente para avisarle que tiene una nueva venta

				
	      
	           			

				return $this->sendSuccessResponse(true,"Pedido guardado");
			}catch(Exception $e){
				return $this->sendErrorResponse($e->getMessage('error'));
			}
	        $this->_redirect('/pedido/list');
		}
	}

	public function listAction() {
		$pedidoModel = new Application_Model_Pedido();
		$listadoPedidos = $pedidoModel->getList();
		$this->view->listadoPedidos = $listadoPedidos;
    }

    public function detalleAction() {
		if($this->getRequest()->isPost()){
  
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$pedidoModel = new Application_Model_Pedido();
				$pedidoDetalleModel = new Application_Model_PedidoDetalle();

				$detallePedido =$pedidoDetalleModel->getDetallePedidoById($id);
				$pedido = $pedidoModel->getFullPedido($id);
			// die(var_dump($pedido));

				if($pedido){
					$pedido['detalle'] = $detallePedido;
					$this->view->pedido = $pedido;
				}
				else{
					$this->_redirect('/pedido/list');
				}
			}
		}
    }

    public function recibidoAction(){
		$params = $this->getRequest()->getPost();
        $actualiza['recibido'] = 1;
		
		$pedidoModel = new Application_Model_Pedido();
        $result = $pedidoModel->edit($params["id_pedido"], $actualiza);
        if($result instanceof Exception){
            return $this->sendErrorResponse('Error al poner el pedido como recibido');
        }

		$existenciaModel = new Application_Model_Existencia();
		$detallepedidoModel = new Application_Model_PedidoDetalle();
        $lineasDetalle = $detallepedidoModel->getDetallePedidoById($params["id_pedido"]);

        foreach ($lineasDetalle as $lineaDetalle) {
			$productoMaxExistencia = $existenciaModel->getUltimaExistencia($lineaDetalle["id_producto"]);
			$nuevaCantidad = $productoMaxExistencia['cantidad'] + $lineaDetalle['cantidad'];

			$actualizarStock = array(
				'id_producto'	=> $lineaDetalle["id_producto"],
				'cantidad'		=> $nuevaCantidad,
				'fecha' 		=> date('Y-m-d H:i:s')
			);

			//disminuye el stock en la existencia
			$result = $existenciaModel->add($actualizarStock);
        	// End Inventario

        	// $actualizaStock['recibido'] = 1;
        	// $actualizaStock['recibido'] = 1;
        	// $actualizaStock['recibido'] = 1;
        	// $actualizaStock['recibido'] = 1;

        	// $result = $existenciasModel->edit($params["id_pedido"], $actualizaStock);
        	
        }




        if($result instanceof Exception){
            return $this->sendErrorResponse('Error al poner el pedido como recibido');
        }else{
           return $this->sendSuccessResponse($result);
        }
    }





}