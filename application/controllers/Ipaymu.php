<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ipaymu extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	//get create invoice page from duitku
	function pay(){
		$va           = '0000009669001989'; //get on iPaymu dashboard
		$secret       = 'SANDBOX6D167F18-83BB-4C3D-A1DB-E591CAA6935F-20220809222705'; //get on iPaymu dashboard

		$url          = 'https://sandbox.ipaymu.com/api/v2/payment'; // for development mode
		// $url          = 'https://my.ipaymu.com/api/v2/payment'; // for production mode
		
		$method       = 'POST'; //method
		
		//Request Body//
		$body['product']    = array('headset', 'softcase');
		$body['qty']        = array('1', '3');
		$body['price']      = array('100000', '20000');
		$body['returnUrl']  = 'https://abdulaziz.nurulfikri.com/simperu_v2/ipaymu/success';
		$body['cancelUrl']  = 'https://abdulaziz.nurulfikri.com/simperu_v2/ipaymu/cancel';
		$body['notifyUrl']  = 'https://abdulaziz.nurulfikri.com/simperu_v2/ipaymu/callback';
		$body['referenceId'] = '1234'; //your reference id
		//End Request Body//

		//Generate Signature
		// *Don't change this
		$jsonBody     = json_encode($body, JSON_UNESCAPED_SLASHES);
		$requestBody  = strtolower(hash('sha256', $jsonBody));
		$stringToSign = strtoupper($method) . ':' . $va . ':' . $requestBody . ':' . $secret;
		$signature    = hash_hmac('sha256', $stringToSign, $secret);
		$timestamp    = Date('YmdHis');
		//End Generate Signature


		$ch = curl_init($url);

		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'va: ' . $va,
			'signature: ' . $signature,
			'timestamp: ' . $timestamp
		);

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, count($body));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$err = curl_error($ch);
		$ret = curl_exec($ch);
		curl_close($ch);

		if($err) {
			echo $err;
		} else {

			//Response
			$ret = json_decode($ret);
			if($ret->Status == 200) {
				$sessionId  = $ret->Data->SessionID;
				$url        =  $ret->Data->Url;
				// print_r($ret->Data);
				header('Location:' . $url);
			} else {
				print_r($ret);
			}
			//End Response
    }

	}

	function success(){
		echo 'Response from ipaymu : '.$this->input->get('return').'<br>';
		echo 'Trx Id : '.$this->input->get('trx_id').'<br>';
		echo 'Status : '.$this->input->get('status').'<br>';
		echo 'Pembayaran Via : ',$this->input->get('via').'<br>';
		echo 'Name Channel : ',$this->input->get('channel').'<br>';
	}

	function cancle(){
		echo 'Response from ipaymu : '.$this->input->get('return').'<br>';
		echo 'Trx Id : '.$this->input->get('trx_id').'<br>';
		echo 'Status : '.$this->input->get('status').'<br>';
		echo 'Pembayaran Via : ',$this->input->get('via').'<br>';
		echo 'Name Channel : ',$this->input->get('channel').'<br>';
	}

	//callback response from duitku
	function callback(){
		//get all data $_GET variable
		// echo 'Response from ipaymu : '.$this->input->get('return').'<br>';
		// echo 'Trx Id : '.$this->input->get('trx_id').'<br>';
		// echo 'Status : '.$this->input->get('status').'<br>';
		// echo 'Pembayaran Via : ',$this->input->get('via').'<br>';
		// echo 'Name Channel : ',$this->input->get('channel').'<br>';
		echo '00';
	}

	function cekpembayaran(){
		echo "cekpembayaran";
	}
}
