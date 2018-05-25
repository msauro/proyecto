<?php

class VentaController extends Gabinando_Base{

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
		$productosModel	= new Application_Model_Producto();
		
		$ventas = $ventasModel->getList();
		$clientestypeList = $clientesModel->getListType();
		
		$this->view->clientes = $clientestypeList;
				
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



	public function setventaAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getPost();
			$productos = $params['productos'];
			$clienteModel  	= new Application_Model_Cliente();
			$cliente	   	= $clienteModel->getClienteById($params['data']['id_cliente']);
			
			try{
				$ventaModel= new Application_Model_Venta();
				$forma_entrega = ($params['data']['envio'][0] != 0) ? 'delivery' : 'retira';
				$ventaObj = array(
						'id_cliente' 		=> $params['data']['id_cliente'],
						'fecha' 			=> date('Y-m-d H:i:s'),
						'forma_pago'		=> $params['data']['forma_pago'],
						'descuento'			=> $params['data']['descuento'],
						'total'      		=> $params['data']['total'],
						'subtotal' 			=> $params['data']['subtotal_products'],
						'envio' 			=> $params['data']['envio'],
						'iva'				=> $params['data']['iva'],
						'forma_entrega'		=> $forma_entrega
				);
				if ($cliente['nom_tipo'] == 'Monotributista') {
					$ventaObj['tipo'] = 'B';
				}elseif ($cliente['exento'] == 0) {
					$ventaObj['tipo'] = 'A';
					
				}else{
					$ventaObj['tipo'] = 'B';

				};

				// Start - Crea la donation
				$idVenta = $ventaModel->add($ventaObj);	
				// End - Crea la donation

				$ventas_detalleModel = new Application_Model_VentaDetalle();
				$productoModel	= new Application_Model_Producto();
				$existenciaModel	= new Application_Model_Existencia();
				if ($productos['simple'] == true) {
					$id_producto = $productos['id_producto'];
					$productoMaxExistencia = $existenciaModel->getUltimaExistencia($id_producto);
						
					$nuevaCantidad = $productoMaxExistencia['cantidad'] - $productos['cant'];
					// Start - Crea linea de venta
					$dProductsObj = array(
							'id_producto'	=> $productos['id_producto'],
							'cantidad'		=> $productos['cant'],
							'precio'		=> $productos['precio'],
							'id_venta'		=> $idVenta
					);

					$ventas_detalleModel->add($dProductsObj);
					$actualizarStock = array(
						'id_producto'	=> $id_producto,
						'cantidad'		=> $nuevaCantidad,
						'fecha' 		=> date('Y-m-d H:i:s')
					);
					//disminuye el stock en la existencia
					$result = $existenciaModel->add($actualizarStock);
	            	// End Inventario
				}else{
					foreach ($productos as $prod) {
						$id_producto = $prod['id_producto'];
						$productoMaxExistencia = $existenciaModel->getUltimaExistencia($id_producto);
						
						$nuevaCantidad = $productoMaxExistencia['cantidad'] - $prod['cant'];

						// Start - Crea linea de venta
						$dProductsObj = array(
								'id_producto'	=> $prod['id_producto'],
								'cantidad'		=> $prod['cant'],
								'precio'		=> $prod['precio'],
								'id_venta'		=> $idVenta
						);

						$ventas_detalleModel->add($dProductsObj);
						
						// Start descontar stock Inventario 
						$actualizarStock = array(
							'id_producto'	=> $id_producto,
							'cantidad'		=> $nuevaCantidad,
							'fecha' 		=> date('Y-m-d H:i:s')
						);
						//disminuye el stock en la existencia
						$result = $existenciaModel->add($actualizarStock);
		            	// End Inventario
					}
				}
		
		

		

				// Start - Mail al cliente para avisarle que tiene una nueva VENTA  VER/TERMINAR

				// $sender 	= new Application_Model_Mail_Sender();
				// $message 	= "Hola ".$venta['nombre']." ".$venta['apellido']."! Gracias por tu compra. Total:".$venta['total']." <br><br> Fecha: " . date('d-m-Y h:i a',strtotime($venta['fecha'])) ;

				// $result 	= $sender->sendEmail($venta['email'],"Nueva compra",$message);
				// End - Mail al cliente para avisarle que tiene una nueva venta

				
	      
	           			

				return $this->sendSuccessResponse(true,"Venta guardada y stock actualizado");
			}catch(Exception $e){
				return $this->sendErrorResponse($e->getMessage('error'));
			}
	        $this->_redirect('/venta/list');
		}
	}

	public function listAction() {
		$ventaModel = new Application_Model_Venta();
		$listadoVentas = $ventaModel->getList();
		$this->view->listadoVentas = $listadoVentas;
    }

    public function detalleAction() {
		if($this->getRequest()->isPost()){
			// $params = $this->getRequest()->getPost();
			// $params['id'] = $this->getRequest()->getParam('id');

			// $proveedor = new Application_Model_Proveedor();
			// $prov = $proveedor->getProveedorById($params['id']);

			// $error = null;
			// if ($prov['cuit'] != $params['cuit']) {
			// 	$alreadyRegistered = $proveedor->getProveedorByCuit($params['cuit']);
			// 	if(!is_null($alreadyRegistered)){
			// 		$error = 'Existe un proveedor con ese CUIT';
			// 	} 
			// }

			// if ($prov['email'] != $params['email']) {
			// 	$alreadyRegistered = $proveedor->getProveedorByEmail($params['email']);
			// 	if(!is_null($alreadyRegistered)){
			// 		$error = 'Existe un proveedor con ese Email';
			// 	}
			// }

			// if(!is_null($error)){
   //              Gabinando_Base::addError($error);
   //              $this->_redirect('/proveedor/edit/id/'.$params['id']);
    //         }else{
				// $result = $proveedor->edit($params['id'], $params);
				// if($result instanceof Exception){
	   //              Gabinando_Base::addError($result->getMessage());
	   //              $this->_redirect('/proveedor/edit');
    //         	}
            	
    //         	Gabinando_Base::addSuccess('Proveedor editado correctamente');
	   //          $this->_redirect('/proveedor/list');

    //         }
		}
		else{
			$id = $this->getRequest()->getParam('id');
			if($id){
				$ventaModel = new Application_Model_Venta();
				$ventaDetalleModel = new Application_Model_VentaDetalle();

				$detalleVenta =$ventaDetalleModel->getDetalleVentaById($id);
				$venta = $ventaModel->getFullVenta($id);
			// die(var_dump($venta));
				if ($venta['descuento']>0) {
					$subDesc = $venta['subtotal'] - ($venta['subtotal'] *$venta['descuento']/100);
					$venta['iva_calculado'] = (round($subDesc*0.21,2));
				}else{
					$venta['iva_calculado'] = (round($venta['subtotal'] * 0.21,2));
				}

				if($venta){
					$venta['detalle'] = $detalleVenta;
					$this->view->venta = $venta;
				}
				else{
					$this->_redirect('/venta/list');
				}
			}
		}
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
			$render = $this->view->render('/venta/modal_prod_equivalente.phtml');
            return $this->sendSuccessResponse($render);
		}
	}

}