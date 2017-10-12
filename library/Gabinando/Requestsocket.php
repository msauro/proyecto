<?php

class Gabinando_Requestsocket {

	public function sendMessage($body){
		$response = $this->curl_request(SOCKET_URI . '/request_update', $body);
		return $response;
	}

	protected function curl_request($uri, $body){
    try {
			$cURL=curl_init();
			curl_setopt($cURL, CURLOPT_URL, $uri);
			curl_setopt($cURL, CURLOPT_POST, 1);
			curl_setopt($cURL, CURLOPT_POSTFIELDS,http_build_query($body));
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
			$cResponse = trim(curl_exec($cURL));
			curl_close($cURL);
		} catch (Exception $e) {
        throw new Exception("socket ex: " . $e->getMessage());
    }

		return $cResponse;
	}
}

?>