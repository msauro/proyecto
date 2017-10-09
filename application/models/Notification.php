<?php

class Application_Model_Notification
{
	public function send($pushToken, $status){

		// Set notification's body depends on the status
		switch ($status) {
			case 'Waiting':
				$body = "Your request has been accepted by the metre! In a few minutes you'll have it.";
				break;

			case 'Cancelled':
				$body = "Your request has been cancelled!";
				break;

			case 'Successful':
				$body = "Your request has been completed!";
				break;
		};

		// Set notification fields 
		$notification = array
		(
			'title' 	=> 'Request status updated!',
			'body'		=> $body,
			'icon'		=> 'icon',
			'sound'		=> 'default'
		);

		$data = array
		(
			'deeplink' => 'cmx://com.cmx/moviemode'
		);

		$fields = array
		(
			'to' 			=> $pushToken,
			'notification'	=> $notification,
			'priority'  	=> 'high',
			'data' 			=> $data
		);
		
		// Set request's headers
		$headers = array
		(
			'Authorization: key=' . FIREBASE_API_KEY,
			'Content-Type: application/json'
		);

		// Set curl options
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

		////////////////////////////////////////
		///	TO DO: IMPLEMENT EXPONENTIAL BACKOFF
		///	Firebase requires exponential backoff
		///	in server error cases.
		////////////////////////////////////////
		
		// Execute curl
		$curlResponse = curl_exec($ch );
		// Close curl 
		curl_close( $ch );
		// Decode response
		$params = Zend_Json::decode($curlResponse);

		if($params['success'] == 1){
			$response['code'] = 200;
		}else{

			$response['message'] = $params['results'][0]['error'];
        	$response['code'] = 202;
		}
		 
        return $response;

		////////////////////////////////////////
		////////////////////////////////////////
		////////////////////////////////////////
	}
}

?>