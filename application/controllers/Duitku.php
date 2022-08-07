<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duitku extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	function pay(){
		$userId         =  3551; 
		$secretKey      = 'de56f832487bc1ce1de5ff2cfacf8d9486c61da69df6fd61d5537b6b7d6d354d';
		$amountTransfer =  50000; 
		$bankAccount    = '8760673566';
		$bankCode       = '014'; 
		$email          = 'test@chakratechnology.com'; 
		$purpose        = 'Test Disbursement with duitku';
		$timestamp      = round(microtime(true) * 1000); 
		$senderId       = 123456789; 
		$senderName     = 'John Doe'; 
		$paramSignature = $email . $timestamp . $bankCode . $bankAccount . $amountTransfer . $purpose . $secretKey; 

		$signature = hash('sha256', $paramSignature);

		$params = array(
			'userId'         => $userId,
			'amountTransfer' => $amountTransfer,
			'bankAccount'    => $bankAccount,
			'bankCode'       => $bankCode,
			'email'          => $email,
			'purpose'        => $purpose,
			'timestamp'      => $timestamp,
			'senderId'       => $senderId,
			'senderName'     => $senderName,
			'signature'      => $signature
		);

		$params_string = json_encode($params);
		$url = 'https://sandbox.duitku.com/webapi/api/disbursement/inquirysandbox'; // Sandbox
		// $url = 'https://passport.duitku.com/webapi/api/disbursement/inquiry'; // Production
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($params_string))                                                                       
		);   
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		//execute post
		$request = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 200)
		{
			$result = json_decode($request, true);
			header('location: '. $result['paymentUrl']);
			echo "email          :". $result['email']          . "<br />";
			echo "bankCode       :". $result['bankCode']       . "<br />";
			echo "bankAccount    :". $result['bankAccount']    . "<br />";
			echo "amountTransfer :". $result['amountTransfer'] . "<br />";
			echo "accountName    :". $result['accountName']    . "<br />";
			echo "custRefNumber  :". $result['custRefNumber']  . "<br />";
			echo "disburseId     :". $result['disburseId']     . "<br />";
			echo "responseCode   :". $result['responseCode']   . "<br />";
			echo "responseDesc   :". $result['responseDesc']   . "<br />";
		}
		else{
			echo $httpCode;

		}
	}


	function callback(){
		$secretKey = 'de56f832487bc1ce1de5ff2cfacf8d9486c61da69df6fd61d5537b6b7d6d354d';

		$json = file_get_contents('php://input');

		$result = json_decode(stripslashes($json),true);

		$disburseId     = $result['disburseId']; 
		$userId         = $result['userId']; 
		$email          = $result['email']; 
		$bankCode       = $result['bankCode'];
		$bankAccount    = $result['bankAccount'];
		$amountTransfer = $result['amountTransfer']; 
		$accountName    = $result['accountName'];
		$custRefNumber  = $result['custRefNumber'];   
		$statusCode     = $result['statusCode']; 
		$statusDesc     = $result['statusDesc'] ;
		$errorMessage   = $result['errorMessage']; 
		$signature      = $result['signature']; 

		if(!empty($email) && !empty($bankCode) && !empty($bankAccount) && !empty($accountName) && !empty($custRefNumber) && !empty($amountTransfer) && !empty($disburseId) && !empty($signature))
		{
			$params = $email . $bankCode . $bankAccount . $accountName . $custRefNumber .  $amountTransfer . $disburseId . $secretKey;
			$calcSignature = hash('sha256', $params);
			if($signature == $calcSignature)
			{
				//Your code here
				echo "SUCCESS"; // Please response with success

			}
			else
			{
				throw new Exception('Bad Signature');
			}
		}else
		{
			throw new Exception('Bad Parameter');
		}

	}
}
