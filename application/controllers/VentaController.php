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
		
		// $driver = new Application_Model_Driver();

	 //    $dispenserModel = new Application_Model_Dispenser();
		// $dispenser 		= $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);

		// $discountModel = new Application_Model_Discounts();
		// $discount = $discountModel->getList();
		$ventas = $ventasModel->getList();
		// $tipoCliente = 0;
		// $hoy = 0;
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

	public function editstatusAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
    		if (empty($params["status"])) {
				return $this->sendErrorResponse("No status selected.");
			}
			if (empty($params["content"])) {
				return $this->sendErrorResponse("No reason for changing.");
			}

			$ventaModel= new Application_Model_Venta();

			$beforeDonation = $ventaModel->getVenta($params['id_donation']);

           	if ($beforeDonation['final_time']=='0000-00-00 00:00:00') {
				$donationParams = array(
					'status' => $params["status"],
					'final_time' => date('Y-m-d H:i:s')
				);
           	}else{
				$donationParams = array(
					'status' => $params["status"]
				);
           		
           	}

           	$donationPaymentsModel = new Application_Model_Donationpayments();

           	if((isset($params['cc_amount']) && (float)$params['cc_amount'] > 0) || (isset($params['cash_amount']) && (float)$params['cash_amount'] > 0))
           		$donationPaymentsModel->remove('id_donation',$params['id_donation']);

			

			
			if(isset($params['cc_amount']) && (float)$params['cc_amount'] > 0){

				$donationPaymentsModel->add(array(
					'id_donation'		=> $params['id_donation'],
					'amount'			=> $params['cc_amount'],
					'method'			=> 'ccre'
				));
			}

			if(isset($params['cash_amount']) && (float)$params['cash_amount'] > 0){

				$donationPaymentsModel->add(array(
					'id_donation'		=> $params['id_donation'],
					'amount'			=> $params['cash_amount'],
					'method'			=> 'cash'
				));
			}

			if($params["status"] == "cancel"){
				$donationParams['amount'] = 0;
				$donationParams['discount'] = 0;
				$donationParams['subtotal'] = 0;
            }

            $donationParams['service_type'] = "delivery";

			if (!empty($params["amount"])){
				$donationParams["amount"] = (float)$params["amount"];
				if($beforeDonation['amount'] != $params['amount']){				
					$donationParams['discount'] = 0;
					$donationParams['points'] = 0;
				}
			}

		
			$result = $ventaModel->edit('id_donation', $params['id_donation'], $donationParams);

			if($result instanceof Exception){
				return $this->sendErrorResponse($result->getMessage());
			}else{
				$notificationModel = $this->getPushNotificationModel();
	            $dispenserModel = new Application_Model_Dispenser();
	            $dispenser = $dispenserModel->getDispenser();
	            $donation = $ventaModel->getFullVenta($params['id_donation']);				
				$patientModel = new Application_Model_Patient();
				
				if($params["status"]=="cancel"){

					//Start - Actualiza los points del paciente cuando la donacion se cancela
					$points = $this->getPointsbyAmount($donation['amount']);
					$where	= "id_patient = {$donation['patient']['id_patient']}";
					$data 	= array('points' => new Zend_Db_Expr("points - {$points}"));
	            	$edit 	= $patientModel->editWithConditions($data,$where);
					//End - Actualiza los points del paciente cuando la donacion se cancela
					
					//Start - Actualiza inventario si tiene productos
					$inventoryDriverModel	= new Application_Model_Productinventorydriver();
					if($donation['products'] && $donation['driver']['id_driver']){
						foreach ($donation['products'] as $key => $_prod) {
				           	$where	= "id_driver = {$donation['driver']['id_driver']} AND id_product = {$_prod['id_product']}";
			            	$cant 	= $_prod['quantity'];
							$data 	= array('cant' => new Zend_Db_Expr("cant + {$cant}")); 
			            	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
			            	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());		            	
						}
					}
					//End - Actualiza inventario si tiene productos

	            	// Send a Push to Driver and updateRoute
				
					// Recalcula todos los tiempos posteriores a la $donationId
					$apiRoute 	= new Application_Model_Api_Route();
					$apiRoute->updateRoute($donation['driver']['id_driver']);
	            	
	            	// Start - Push al Driver para avisarle que tiene una nueva Donation
	            	$message = "Hey {$donation['driver']['first_name']}! Donation #{$donation['id_donation']} was cancelled by the dispatcher.";
	            	$data = array(
		                'title'     => $dispenser['name'],
		                'body'      =>  $message,
		                'token'		=> 	$donation['driver']['parse_token'],
		                'icon'      => 'icon',
		                'sound'     => 'default',
		                'color'     => '#66AB30',
		                'type'		=> 'CANCEL_DONATION'
		            );					

					$result = $notificationModel->sendPush($data);
					// End - Push al Driver para avisarle que tiene una nueva Donation

				}

				$arrayEvent = array(
					'event_type' 	=> 'admincancel',
					'content' 		=> $params['content'],
					'date'			=> date('Y-m-d H:i:s'),
					'id_donation'	=> $params['id_donation'],
					'id_admin'		=> $this->admin_session->admin['id_admin']
				);
				if($params["status"] == "success"){
					
					//Start - Actualiza los points del paciente cuando la donacion se cambia a succcess
					$points = $this->getPointsbyAmount($donation['amount']);
					$where	= "id_patient = {$donation['patient']['id_patient']}";
					$data 	= array('points' => new Zend_Db_Expr("points + {$points}")); 
	            	$edit 	= $patientModel->editWithConditions($data,$where);
					//End - Actualiza los points del paciente cuando la donacion se cambia a succcess

					$message = "{$donation['patient']['first_name']} thank you for your donation! Rate the driver and help us improve our services.";
					$data = array
		            (
		                'title'     => $dispenser['name'],
		                'body'      =>  $message,
		                'token'		=> 	$donation['patient']['parse_token'],
		                'icon'      => 'icon',
		                'sound'     => 'default',
		                'color'     => '#66AB30',
		                'type'		=> 'SUCCESS_DONATION'
		            );			
				
					$result = $notificationModel->sendPush($data);
					$arrayEvent["event_type"] = "adminsetamount";					
				
				}

				$donationEventsModel = new Application_Model_Donationevents();
				$result = $donationEventsModel->add($arrayEvent);

				if($result instanceof Exception){
					return $this->sendErrorResponse($result->getMessage());
				}else{
					$this->sendSuccessResponse($params["status"],"Status successfully changed.");
				}
			}
		}
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


	public function getnewallowedtimeAction(){

		$params = $this->getRequest()->getParams();

		$ventaModel= new Application_Model_Venta();
		$driverModel = new Application_Model_Driver();
		$modelRoute 	= new Application_Model_Route($this->admin_session->admin['id_dispenser']);

		$donations = array();

		$diff = [];

		foreach ($params['donations'] as $key => $donation) {
			if($donation == "new"){

				$locationPoints = array(
					'lat' => $params['datadonation']['lat'],
					'lon' => $params['datadonation']['lon']
				);

				$apiRoute 	= new Application_Model_Route($params['datadonation']['id_dispenser']);

				$assignResult  = $apiRoute->assignDriver($params['datadonation']['id_dispenser'], $locationPoints, $params['datadonation']['id_patient'], $params['datadonation']['id_driver']);
					// Start - Calculamos el Tiempo de Servicio
				$service = $this->_serviceTime($params['datadonation']['id_patient'],$params['datadonation']['id_driver']);				
				// End - Calculamos el Tiempo de Servicio					
		
				$donationObj = array(
					'id_donation'   => 'new',	
					'arrival_time' 	=> $assignResult['arrival_time'],
					'service_time' 	=> $service,
					'id_driver' 	=> $assignResult['id_driver'],
					'is_fixed_time' => 0,
					'address'       => $locationPoints												
				);

				$donations[$key] = $donationObj;


			}else{
            	$donations[$key] = $ventaModel->getFullVenta($donation);            
			}
						
		}

		$new_arrival_time = $modelRoute->getNewAllowedTime($donations,$params['id_driver']);

		foreach ($new_arrival_time as $key => $donation) {	

			$time1 = $donation['arrival_time'];
			$time2 = $donation['arrival_time_new'];
			if($time1 >= $time2){
				$interval  = -(strtotime($time1) - strtotime($time2));
			}else{
				$interval  = strtotime($time2) - strtotime($time1);
			}
			$minutes   = round($interval / 60);
			$diff[] = array(
				'id_donation' => $donation['id_donation'],
				'arrival_time_new' => $donation['arrival_time_new'],
				'arrival_time' => $donation['arrival_time'],
				'difference' => $minutes,
				'range' => $driverModel->getrangetimeAction($donation['arrival_time_new'])
				);
	    }		

		$this->sendSuccessResponse(array('diff'	=> $diff));		
	}

	public function invoiceAction(){
		// Sin layout de admin
        $this->_helper->layout()->disableLayout();

        $id_donation = $this->getRequest()->getParam('id');

        $dispenserModel = new Application_Model_Dispenser();
        $dispenser = $dispenserModel->getDispenser();

        $ventaModel= new Application_Model_Venta();
        $donation = $ventaModel->getVenta($id_donation);

        $patientModel = new Application_Model_Patient();
        $patient = $patientModel->getPatient($donation['id_patient']);

        $donation['patient_name'] = $patient['first_name'].' '.$patient['last_name']; 

        $donationproductsModel = new Application_Model_Donationproducts();
        $donation_products = $donationproductsModel->getDonationProducts($id_donation);

        $donationpaymentsModel = new Application_Model_Donationpayments();
        $donation_payment = $donationpaymentsModel->getPaymentsByDonationId($id_donation);


    	$tax = $this->getConfig('tax');

    	$this->view->dispenser 	= $dispenser;
        $this->view->donation 	= $donation;
        $this->view->products 	= $donation_products;
        $this->view->payments 	= $donation_payment;
        $this->view->tax 		= $tax;       
	}

	public function labelAction(){
		// Sin layout de admin
        $this->_helper->layout()->disableLayout();

        $id_donation = $this->getRequest()->getParam('id');

        $dispenserModel = new Application_Model_Dispenser();
        $dispenser = $dispenserModel->getDispenser();

        $ventaModel= new Application_Model_Venta();
        $donation = $ventaModel->getVenta($id_donation);

        $patientModel = new Application_Model_Patient();
        $patient = $patientModel->getPatient($donation['id_patient']);

        $donation['patient'] = $patient;

        $donation['patient_name'] = $patient['first_name'].' '.$patient['last_name']; 

        $donationproductsModel = new Application_Model_Donationproducts();
        $donation_products = $donationproductsModel->getDonationProducts($id_donation);

        $donationpaymentsModel = new Application_Model_Donationpayments();
        $donation_payment = $donationpaymentsModel->getPaymentsByDonationId($id_donation);

    	$this->view->dispenser 	= $dispenser;
        $this->view->donation 	= $donation;
        $this->view->products 	= $donation_products;
        $this->view->payments 	= $donation_payment;       
	}
		
	public function getdonationsformapAction(){

		$params = $this->getRequest()->getParams();
		$id_driver = $params['id_driver'];		

		if(!isset($id_area) || $id_area == 'all')
			$id_area = null;
		if(!isset($id_driver) || $id_driver == 'all')
			$id_driver = null;
		if(!isset($day))
			$day = date('Y-m-d');

		$start = true;

		$ventaModel= new Application_Model_Venta();
		$dispenserModel = new Application_Model_Dispenser();

		$dispenser = $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);

		$donationStart = array(
				'address' => array(
					'lat' => $dispenser["lat"],
					'lon' => $dispenser["lon"]
					)
				);

		
		$donations = $ventaModel->getDonationsMap($day, $id_area, $id_driver);

		$donationsArray = array();

		foreach ($donations as $key => $_donation) {
			$fullDonation = $ventaModel->getFullVenta($_donation['id_donation']);
			if($fullDonation['status'] == 'pending'){
				$donationsArray[] = $fullDonation;
			}elseif($fullDonation['status'] == 'success' && $start){
				$donationStart = $fullDonation;
				$start = false;				
			}				
		}

		$this->sendSuccessResponse(array('donations' 	=> $donationsArray, 'start' => $donationStart));		
	}
	



    public function mapAction(){
		$id_area = $this->getRequest()->getParam('id_area');
		$id_driver = $this->getRequest()->getParam('id_driver');
		$day = $this->getRequest()->getParam('day');

		if(!isset($id_area) || $id_area == 'all')
			$id_area = null;
		if(!isset($id_driver) || $id_driver == 'all')
			$id_driver = null;
		if(isset($day)) $day = date("Y-m-d",strtotime(str_replace("-","/",$day)));
		else $day = date('Y-m-d');

		$ventaModel= new Application_Model_Venta();
		$dEventsModel = new Application_Model_Donationevents();
		$donations = $ventaModel->getDonationsMap($day, $id_area, $id_driver);

		$dispenserModel = new Application_Model_Dispenser();
		$dispenser 		= $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);

		if($dispenser instanceof Exception){
            GL::addError($result->getMessage());
            $this->_redirect('admin_areas');
        }

		$coordinates = array(
			'lat' => $dispenser["lat"],
			'lon' => $dispenser["lon"]
		);

		$donationsArray = array();

		foreach ($donations as $key => $_donation) {
			$fullDonation = $ventaModel->getFullVenta($_donation['id_donation']);
			$events = $dEventsModel->getAllEvents($_donation['id_donation']);
			
			$fullDonation['events'] = $events;
			$donationsArray[] = $fullDonation;
		}

		$driverModel= new Application_Model_Driver();
		$drivers 	= $driverModel->getDriversSummary($day,$day);
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
		foreach ($donationsArray as $key => $donation) {
			if ($donation['status']=='success') {
				$_don['success'] = $_don['success'] + 1;
			}elseif($donation['status']=='cancel'){
				$_don['cancel'] = $_don['cancel'] + 1;
			}elseif ($donation['status']=='pending') {
				$_don['pending'] = $_don['pending'] + 1;
			}
		}

		$this->view->donations = $donationsArray;
		$this->view->dispenser = $dispenserModel->getDispenser($this->admin_session->admin['id_dispenser']);
		$this->view->date = date('d F', strtotime($day));
		$this->view->year = date('Y', strtotime($day));			
		$this->view->day = $day;
		$this->view->id_driver = $id_driver;
		$this->view->id_area = $id_area;
		$this->view->coordinates 	= $coordinates;
		$this->view->don = $_don;
		$this->view->driverssummary = $drivers;

		$areasModel = new Application_Model_Area();
		$this->view->areas = $areasModel->getList($this->admin_session->admin['id_dispenser']);

		$driversModel = new Application_Model_Driver();
		$this->view->drivers = $driversModel->getList($this->admin_session->admin['id_dispenser']);
	}

	public function editproductsAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getPost();
			if(isset($params["data"]))
				parse_str($params["data"], $params);

			$ventaModel= new Application_Model_Venta();
			$donation = $ventaModel->getFullVenta($params['id_donation']);

			if(isset($params['id_product']) && count($params['id_product'])){

				$dProductsModel = new Application_Model_Donationproducts();			
				$inventoryDriverModel	= new Application_Model_Productinventorydriver();

				foreach ($donation['products'] as $key => $_prod) {
					// Start Inventario
	            	$where	= "id_driver = {$donation['driver']['id_driver']} AND id_product = {$_prod['id_product']}";
	            	$cant 	= $_prod['quantity'];
					$data 	= array('cant' => new Zend_Db_Expr("cant + {$cant}")); 
	            	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
	            	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());
	            	// End Inventario
				}

				$dProductsModel->remove('id_donation',$params['id_donation']);
				$count = 0;
				foreach ($params['id_product'] as $_prod) {
					// Armar el precio del proucto con la funcion getPrice()
					$dProductsObj = array(
							'id_product'	=> $params['id_product'][$count],
							'quantity'		=> $params['cant'][$count],
							'subtotal'		=> $params['subtotal'][$count],
							'id_donation'	=> $params['id_donation']
						);
					// Start - Crea los productos en la donation
					$dProductsModel->add($dProductsObj);

					// Start Inventario
		           	$where	= "id_driver = {$donation['driver']['id_driver']} AND id_product = {$params['id_product'][$count]}";
		           	$cant 	= $params['cant'][$count];
					$data 	= array('cant' => new Zend_Db_Expr("cant - {$cant}")); 
		           	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
		           	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());
		           	// End Inventario
			
					$count++;
				}					
			}			

			$this->sendSuccessResponse(true,"Product's edited.");
		}
	}

	public function sortdonationsAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getRawBody();
			$params 		= Zend_Json::decode($params);

			$ventaModel= new Application_Model_Venta();

			foreach ($params as $key => $donation) {

				$donationObj = array(
							'arrival_time' 	=> $donation['arrival_time']										
				);

				if($donation == "new"){
				    $lastdonation = $ventaModel->getLastPendingVenta($donation['id_driver'],'delivery');
				  				 
					$ventaModel->edit('id_donation',$lastdonation['id_donation'],$donationObj);
				}else{
					$ventaModel->edit('id_donation',$donation['id_donation'],$donationObj);
				}			
				
			}

			$this->sendSuccessResponse(true);

		}

	}

	public function editdonationAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getPost();

			if(isset($params["data"]))
				parse_str($params["data"], $params);

			if(!isset($params['id_patient']) || $params['id_patient'] == "none")
				return $this->sendErrorResponse("No patient selected.");

			if(!isset($params['id_location']) || $params['id_location'] == "none")
				return $this->sendErrorResponse("No location selected.");

			// if(!isset($params['id_product'])) return $this->sendErrorResponse("No products selected");

			if($params['time']=='')
				return $this->sendErrorResponse("No time selected.");

			if($params['time']=='Dispensary closed')
				return $this->sendErrorResponse("Collective is closed.");

			// Chequeamos que ningun producto venga con cant 0
			$count = 0;
			if(isset($params['id_product'])) :
				foreach ($params['id_product'] as $_prod) {
					if($params['cant'][$count]==0)
						return $this->sendErrorResponse("Please insert at least 1 in quantity.");
					$count++;
				}
			endif;

			$patientModel  	= new Application_Model_Patient();
			$patient	   	= $patientModel->getPatient($params['id_patient']);

			// Validamos que al paciente le corresponda la location
			$locationsModel = new Application_Model_Location();
			$location	   	= $locationsModel->getLocation($params['id_location'],$params['id_patient']);
			if($location instanceof Exception)
				return $this->sendErrorResponse($location->getMessage());
			if(!count($location))
				return $this->sendErrorResponse('mm... this location does not correspond to this patient.');

			$locationPoints = array(
					'lat' => $location['lat'],
					'lon' => $location['lon']
				);

			$apiRoute 	= new Application_Model_Route($this->admin_session->admin['id_dispenser']);

			try{
				
				$ventaModel= new Application_Model_Venta();

				$beforeDonation = $ventaModel->getFullVenta($params['id_donation']);
				// Auth Patient
				// Ver si recommendation_allowed esta true
				if ($patient["status"] == "0")
					return $this->sendErrorResponse('mm... Patient status is inactive.');
				// Ver si tiene expirada la expired_recommendation_id
				// Ver en Settings si el not authorized puede comprar
				$settingsModel = new Application_Model_Setting();
				$settings = $settingsModel->getAll();
				if($settings['enablenotauthorized']=="disabled") {
	            	if($patient["recommendation_allowed"]=="1"){
	                    if($patient["expire_recommendation_id"] < date("Y-m-d"))
	                        return $this->sendErrorResponse('mm... your Recommendation ID has expired.');
	            	}elseif ($patient["recommendation_allowed"]=="2") {
	                    return $this->sendErrorResponse('mm... your Recommendation ID is pending authorization.');
	                }else{
	                    return $this->sendErrorResponse('mm... your Recommendation ID is not authorized.');
	            	}
	            }
				
				// Ver si ya tiene alguna donacion con estado pendiente el día de hoy
            	$lastDonation = $ventaModel->getDonationForPatient($patient['id_patient']);
            	if($lastDonation instanceof Exception)
					return $this->sendErrorResponse($lastDonation->getMessage());
                
                if($params['changetime'] == "true" || $params['status'] == "requested"){
                	// Buscamos los tiempos del Driver seleccionado en el Edit Donation
					$assignResult  = $apiRoute->assignDriver($patient['id_dispenser'], $locationPoints, $params['id_patient'], $params["id_driver"]);
					// Start - Calculamos el Tiempo de Servicio
					$service = $this->_serviceTime($params["id_patient"],$params["id_driver"]);				
					// End - Calculamos el Tiempo de Servicio					
			
					$donationObj = array(
						'id_patient' 	=> $params['id_patient'],
						'date' 			=> date('Y-m-d H:i:s'),
						'arrival_time' 	=> $assignResult['arrival_time'],
						'service_time' 	=> $service,
						'status'        => 'pending',
						'id_driver' 	=> $assignResult['id_driver'],
						'note'          => $params['donation_note'],
						'id_phone'      => $params['id_phone'],
						'id_dispenser' 	=> $patient['id_dispenser'],
						'service_type'  => 'delivery'						
					);

					if(isset($params["total"]) && !empty($params['total'])){
						$donationObj['subtotal'] = $params['total'];
						$donationObj['amount'] = $params['total'];
	                };                

	                if($params['status'] == 'requested'){
	                	//push notification de cambio de request a pending
						$donationObj['status'] = 'pending';
					}else{
						$donationObj['status'] = $params['status'];
					};
				

					if(isset($params["id_driver"]))
						$donationObj["id_driver"] = $params["id_driver"];

					// $time_in_24_hour_format  = date("H:i", strtotime($params["time"]));
					if(isset($params["timeset"]) && !empty($params["timeset"])){
	                  	$time = substr($params["time"], 0, 8);
						$time = date('Y-m-d H:i:s', strtotime($time));
						$donationObj["arrival_time"] = $time;
						$donationObj['is_fixed_time'] = 1;									
					}
					else{
						$donationObj['is_fixed_time'] = 0;
					}
     			}else{
					$donationObj = array(
						'status'        => $params['status'],
						'id_phone'      => $params['id_phone'],
						'note'          => $params['donation_note']						
					);
					
					if(isset($params["total"]) && !empty($params['total'])){
						$donationObj['subtotal'] = $params["status"] == "cancel" ? 0 : $params['total'];
						$donationObj['amount'] = $params["status"] == "cancel" ? 0  : $params['total'];
	                };
				}

				if( isset($params['total']) && !empty($params['total']) && $beforeDonation['amount'] != $params['total']){						
					$donationObj['discount'] = 0;
					$donationObj['points'] = 0;
				}

				// Start - Edita la donation
				$donationId = $ventaModel->edit('id_donation',$params['id_donation'],$donationObj);
				// End - Edita la donation

				$dAddressesModel = new Application_Model_Donationaddresses();
				$dAddressesObj = array(
						'name'			=> $location['name'],
						'street'		=> $location['street'],
						'number'		=> $location['number'],						
						'apartment'		=> $location['apartment'],
						'meetup'		=> $location['meetup'],
						'city'			=> $location['city'],
						'state'			=> $location['state'],
						'lat'			=> $location['lat'],
						'lon'			=> $location['lon'],
						'note'			=> $location['note'],						
						'id_donation'	=> $params['id_donation']
					);
		
				// Start - Crea la address de la Donation
				$dAddressesModel->edit('id_donation',$params['id_donation'],$dAddressesObj);

				$id_driver = $donationObj['id_driver'] ? $donationObj['id_driver'] : $beforeDonation['driver']['id_driver'];

				$inventoryDriverModel	= new Application_Model_Productinventorydriver();

				//Start devuelve los productos al driver
				foreach ($beforeDonation['products'] as $key => $_prod) {
					// Start Inventario
	            	$where	= "id_driver = {$beforeDonation['driver']['id_driver']} AND id_product = {$_prod['id_product']}";
	            	$cant 	= $_prod['quantity'];
					$data 	= array('cant' => new Zend_Db_Expr("cant + {$cant}")); 
	            	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
	            	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());
	            	// End Inventario
				}
				//End devuelve los productos al driver

                // Products - se fija si cambio los productos y si los cambio los carga
				if(isset($params['id_product']) && count($params['id_product'])){

					$dProductsModel = new Application_Model_Donationproducts();
				
					
					$dProductsModel->remove('id_donation',$params['id_donation']);
					$count = 0;
					foreach ($params['id_product'] as $_prod) {
						// Armar el precio del proucto con la funcion getPrice()
						$dProductsObj = array(
								'id_product'	=> $params['id_product'][$count],
								'quantity'		=> $params['cant'][$count],
								'subtotal'		=> $params['subtotal'][$count],
								'id_donation'	=> $params['id_donation']
							);
						// Start - Crea los productos en la donation
						$dProductsModel->add($dProductsObj);

						// Start Inventario
		            	$where	= "id_driver = {$id_driver} AND id_product = {$params['id_product'][$count]}";
		            	$cant 	= $params['cant'][$count];
						$data 	= array('cant' => new Zend_Db_Expr("cant - {$cant}")); 
		            	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
		            	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());
		            	// End Inventario


						$count++;
					}
				}else{
					//Start actualiza el stock del driver
					foreach ($beforeDonation['products'] as $key => $_prod) {
						// Start Inventario
		            	$where	= "id_driver = {$id_driver} AND id_product = {$_prod['id_product']}";
		            	$cant 	= $_prod['quantity'];
						$data 	= array('cant' => new Zend_Db_Expr("cant - {$cant}")); 
		            	$edit 	= $inventoryDriverModel->editWithConditions($data,$where);
		            	if($edit instanceof Exception) $this->sendErrorResponse($edit->getMessage());
		            	// End Inventario
					}
					//End actualiza el stock del drivers
				}

				// Start - Crea la donation en donation_msg_read
				$donationMessagesModel = new Application_Model_Donationmessagesread();
				$readParamas = array(
								'id_donation' 	=> $donationId
							);
				$result = $donationMessagesModel->add($readParamas);
				// End - Crea la donation en donation_msg_read
                if($params['status'] == "requested" || $params["status"] == "pending"){
                	
					$arrayEvent = array(
						'event_type' 	=> 'editdonation',
						'date'			=> date('Y-m-d H:i:s'),					
						'id_donation'	=> $params['id_donation'],
						'id_admin'		=> $this->admin_session->admin['id_admin']
					);

					if($params['status'] == 'requested'){
						$arrayEvent['content'] = "Donation set to pending by Admin.";
					}else{
						$arrayEvent['content'] = "Donation edited by Admin.";
					}
					$donationEventsModel = new Application_Model_Donationevents();
					$result = $donationEventsModel->add($arrayEvent);
				}
				
                $donation = $ventaModel->getFullVenta($params['id_donation']);

				if($params['changetime'] == "true" || $params['status'] == 'requested'){
					// Recalcula todos los tiempos posteriores a la $donationId
					$apiRoute->updateRoute($assignResult['id_driver'],$params['id_donation']);

					// Start - Mail al Patient para avisarle que tiene una nueva Donation
					if ($params['status'] == 'requested') {
						$sender           = new Application_Model_Mail_Sender();
	                    $messagePatient   = "Hey {$donation['patient']['first_name']}! Your request was confirmed. <br><br> Date: " . date('m-d-Y h:i a',strtotime($donation['date'])) . "<br> ETA: " . date('h:i a',strtotime($donation['arrival_time'])) . " <br> Driver assigned: {$donation['driver']['first_name']} <br><br> <a href='" . front_uri . "/index/donations' target='_blank'>Check your donation's status</a>";
	                    $result           = $sender->sendEmail($patient['email'],"Donation Request Confirmed",$messagePatient);
					}
					// End - Mail al Patient para avisarle que tiene una nueva Donation
				}

            	// Start - Push al Driver y Patient para avisarle que tiene una nueva Donation
             	$notificationModel = $this->getPushNotificationModel();
               	$dispenserModel = new Application_Model_Dispenser();
            	$dispenser = $dispenserModel->getDispenser();
            	if($params['id_driver'] != $beforeDonation['driver']['id_driver'] || $params['status'] == "requested"){
            		$message = "Hey {$donation['driver']['first_name']}! There's a new donation assigned to you: {$donation['patient']['first_name']} {$donation['patient']['last_name']} at {$donation['address']['number']} {$donation['address']['street']} ({$donation['address']['city']})";

            		$data = array
		            (
		                'title'     => $dispenser['name'],
		                'body'      =>  $message,
		                'token'		=> $donation['driver']['parse_token'],
		                'icon'      => 'icon',
		                'sound'     => 'default',
		                'color'     => '#66AB30',
		                'type'		=> 'NEW_DONATION'
		            );
				
					$result = $notificationModel->sendPush($data);
			    }
			    if($params['status'] == "requested"){
				    $message = "Hey {$donation['patient']['first_name']}! Your request was confirmed.";
					$data = array
		            (
		                'title'     => $dispenser['name'],
		                'body'      =>  $message,
		                'token'		=> $donation['patient']['parse_token'],
		                'icon'      => 'icon',
		                'sound'     => 'default',
		                'color'     => '#66AB30',
		                'type'		=> 'PENDING_DONATION'
		            );
					$result = $notificationModel->sendPush($data);
					// Start - Sms al Patient para avisarle que tiene una nueva Donation
					if($settings['enablesms']['value'] == "enabled"){
						if($donation['patient']['sms_enabled']){
							$phoneModel = new Application_Model_Phone();
							$sms = $this->getSMSNotificationModel();
							$defaultPhone = $phoneModel->getDefaultPhone($donation['patient']['id_patient']);
							$data = array(
								'message' 		=> $message,
								'phone'     	=> $defaultPhone['number']				
							);							
							$status = $sms->sendSMS($data);							
						}						
					}
					// End - Sms al Patient para avisarle que tiene una nueva Donation 
				
				}

				if($params['time'] == "timedefault") $this->sendSuccessResponse(true,"Donation accepted.");

				$this->sendSuccessResponse(true,"Donation edited.");


			}catch(Exception $e){	
		    	return $this->sendEmail($e->getMessage());
			}
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

				$forma_entrega = ($params['data']['envio'] != 0) ? 'delivery' : 'retira';
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
		
					// if($result instanceof Exception){
		   //              Gabinando_Base::addError($result->getMessage());
		   //          }else{
		   //          	Gabinando_Base::addSuccess('Producto actualizado correctamente');
		   //          	// $this->_redirect('/producto/list');
		   //          }

				
            	$venta = $ventaModel->getFullVenta($idVenta);
            	// die(var_dump($venta));
				// Start - Mail al cliente para avisarle que tiene una nueva VENTA  VER/TERMINAR
				
				$sender 	= new Application_Model_Mail_Sender();
				$message 	= "Hola ".$venta['nombre']." ".$venta['apellido']."! Gracias por tu compra. Total:".$venta['total']." <br><br> Fecha: " . date('d-m-Y h:i a',strtotime($venta['fecha'])) ;

				$result 	= $sender->sendEmail($venta['email'],"Nueva compra",$message);

				// End - Mail al cliente para avisarle que tiene una nueva venta
							

				$this->sendSuccessResponse(true,"Venta guardada y stock actualizado");

			}catch(Exception $e){
				return $this->sendErrorResponse($e->getMessage('error'));
			}
		}
	}

	public function getselecteddriverAction(){
		if($this->getRequest()->isPost()){
			$params 		= $this->getRequest()->getPost();

			$locationPoints = array(
					'lat' => $params['lat'],
					'lon' => $params['lon']
				);

			$apiRoute 	= new Application_Model_Route($this->admin_session->admin['id_dispenser']);
			try{
				if(isset($params["id_driver"])){
					$assignResult 	= $apiRoute->assignDriver($params['id_dispenser'], $locationPoints, $params['id_patient'],$params["id_driver"]);
				}else{
					$assignResult 	= $apiRoute->assignDriver($params['id_dispenser'], $locationPoints, $params['id_patient']);
				}
			}catch(Exception $e){
				return $this->sendErrorResponse($e->getMessage());
			}

			$driverModel = new Application_Model_Driver();
			$driver = $driverModel->getDriverWithArea($assignResult['id_driver']);

			$assignResult["driver"] = $driver;

			$time = $driverModel->allowedtimeAction($assignResult['id_driver'],$assignResult["arrival_time"],$this->admin_session->admin['id_dispenser']);

			$assignResult["times"] = $time;

			if($driver instanceof Exception){
	            $this->sendErrorResponse($driver->getMessage());
	        }else{
	        	$this->sendSuccessResponse($assignResult,"The system selected a recommended driver for this patient's location.");
	        }
		}
	}

	public function validatecouponAction(){
     	if($this->getRequest()->isPost()){        
	        $params = $this->getRequest()->getParams();
			$response = $this->validateCoupon($params['coupon']);
	        $this->sendSuccessResponse(array('coupon' => $response));        
        }
    }     

	protected function _calculateQueueNumber($id_donation, $id_driver){
		$ventaModel= new Application_Model_Venta();
		$pendingDonations = $ventaModel->getPendingDonationsForDriver($id_driver);

		$queue = false;

		foreach ($pendingDonations as $key => $_donation) {
			if((int)$_donation['id_donation'] == (int)$id_donation){
				$queue = $key;
				break;
			}
		}

		if($queue)
			$queue = $queue + 1;

		return $queue;
	}

	protected function _driverStarted($id_driver){
		$now 			= time();
		$driverModel 	= new Application_Model_Driver();
		$started 		= $driverModel->getDriverStarted($id_driver);
		$finished 		= $driverModel->getDriverFinished($id_driver);

		if($started && strtotime($started['date']) <= $now){
			if($finished && strtotime($finished['date']) < $now){
				return false;
			}
		}else{
			return false;
		}

		return true;
	}

	protected function _newPatient($id_patient){		
		$ventaModel	= new Application_Model_Venta();
		$result 		= $ventaModel->getDonationsForPatient($id_patient);

		if($result instanceof Exception){
			return $this->sendErrorResponse($result->getMessage());
		}else{
			if( count($result)>1 ){
				return false;
			}else{
				$patientModel 	= new Application_Model_Patient();
				$patient = $patientModel->getPatient($id_patient);					
				if(implementation_date < date('Y-m-d', strtotime($patient['created_at']))){
					return true;
				}else{				
					return false;
				}
			}
		}

		return false;
	}

	protected function _serviceTime($id_patient,$id_driver){
		$settingsModel = new Application_Model_Setting();
		$favoriteModel 	= new Application_Model_Favdriver();

		$settings = $settingsModel->getAll();
		if($settings instanceof Exception)
			return $this->sendErrorResponse($settings->getMessage());

		// Service Time
		$service = intval($settings['drivertime']['value']);
		// New Patient
		$patientNew = $this->_newPatient($id_patient);
		if ($patientNew) $service = $service + intval($settings['patientnew']['value']);
		// Favorite Driver
		$driverFavorited = $favoriteModel->getFavorite($id_patient,$id_driver);
		if ($driverFavorited) $service = $service + intval($settings['favoritedriver']['value']);

		return $service;
	}

}