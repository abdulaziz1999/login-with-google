<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duitku extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	//get create invoice page from duitku
	function pay(){
		$merchantCode = 'D9174'; // dari duitku
		$merchantKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // dari duitku

		$timestamp = round(microtime(true) * 1000); //in milisecond
		$paymentAmount = 10000;
		$merchantOrderId = time() . ''; // dari merchant, unique
		$productDetails = 'Test Pay with duitku';
		$email = 'azizmentor96@gmail.com'; // email pelanggan merchant
		$phoneNumber = '08123456789'; // nomor tlp pelanggan merchant (opsional)
		$additionalParam = ''; // opsional
		$merchantUserInfo = ''; // opsional
		$customerVaName = 'John Doe'; // menampilkan nama pelanggan pada tampilan konfirmasi bank
		$callbackUrl = 'https://payment.kampuskode.com/duitku/callback'; // url untuk callback
		$returnUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/cekpembayaran';//'http://example.com/return'; // url untuk redirect
		$expiryPeriod = 10; // untuk menentukan waktu kedaluarsa dalam menit
		$signature = hash('sha256', $merchantCode.$timestamp.$merchantKey);
		// $paymentMethod = '014'; //digunakan untuk direksional pembayaran

		// Detail pelanggan
		$firstName = "John";
		$lastName = "Doe";

		// Alamat
		$alamat = "Jl. Kembangan Raya";
		$city = "Jakarta";
		$postalCode = "11530";
		$countryCode = "ID";

		$address = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address' => $alamat,
			'city' => $city,
			'postalCode' => $postalCode,
			'phone' => $phoneNumber,
			'countryCode' => $countryCode
		);

		$customerDetail = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			'billingAddress' => $address,
			'shippingAddress' => $address
		);


		$item1 = [
			'name' => 'Test Item 1',
			'price' => 10000,
			'quantity' => 1];

		// $item2 = array(
		// 	'name' => 'Test Item 2',
		// 	'price' => 30000,
		// 	'quantity' => 3);

		$itemDetails = [$item1];

		$params = array(
			'paymentAmount' => $paymentAmount,
			'merchantOrderId' => $merchantOrderId,
			'productDetails' => $productDetails,
			'additionalParam' => $additionalParam,
			'merchantUserInfo' => $merchantUserInfo,
			'customerVaName' => $customerVaName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			'itemDetails' => $itemDetails,
			'customerDetail' => $customerDetail,
			'callbackUrl' => $callbackUrl,
			'returnUrl' => $returnUrl,
			'expiryPeriod' => $expiryPeriod,
			// 'paymentMethod' => $paymentMethod
		);

		$params_string = json_encode($params);
		//echo $params_string;
		$url = 'https://api-sandbox.duitku.com/api/merchant/createinvoice'; // Sandbox
		// $url = 'https://api-prod.duitku.com/api/merchant/createinvoice'; // Production

		//log transaksi untuk debug 
		// file_put_contents('log_createInvoice.txt', "* log *\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', $params_string . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-signature:' . $signature . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-timestamp:' . $timestamp . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-merchantcode:' . $merchantCode . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($params_string),
			'x-duitku-signature:' . $signature ,
			'x-duitku-timestamp:' . $timestamp ,
			'x-duitku-merchantcode:' . $merchantCode    
			)                                                                       
		);   
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		//execute post
		$request = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 200)
		{
			$result = json_decode($request, true);
			// header('location: '. $result['paymentUrl']);
			// print_r($result, false);
			echo "paymentUrl :". $result['paymentUrl'] . "<br />";
			echo "reference :". $result['reference'] . "<br />";
			echo "statusCode :". $result['statusCode'] . "<br />";
			echo "statusMessage :". $result['statusMessage'] . "<br />";
		}
		else
		{
			// echo $httpCode . " " . $request ;
			echo $request ;
		}
	}

	//get payment gateway bank list
	function get_payment(){
		// Set kode merchant anda 
		$merchantCode = "D9174"; 
		// Set merchant key anda 
		$apiKey = "11fca2d38ac9a876a5ad337006aa8aa3";
		// catatan: environtment untuk sandbox dan passport berbeda 
	
		$datetime = date('Y-m-d H:i:s');  
		$paymentAmount = 10000;
		$signature = hash('sha256',$merchantCode . $paymentAmount . $datetime . $apiKey);
	
		$params = array(
			'merchantcode' => $merchantCode,
			'amount' => $paymentAmount,
			'datetime' => $datetime,
			'signature' => $signature
		);
	
		$params_string = json_encode($params);
	
		$url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'; 
	
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
			$results = json_decode($request, true);
			// echo "<pre>"; print_r($results['paymentFee'], false); echo "</pre>";
			
				?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
				<div class="container">
					<div class="row">
						<?php foreach($results['paymentFee'] as $result){?>
						<div class="col-lg-3 mb-5 mr-5">
							<div class="card" style="width: 18rem;">
								<img src="<?= $result['paymentImage']?>" class="card-img-top img-thumbnail" alt="...">
								<div class="card-body text-center">
									<h5 class="card-title"><?= $result['paymentName']?></h5>
									<p class="card-text"><?= $result['paymentMethod']?></p>
									<a href="#" class="btn btn-block btn-success">Bayar</a>
								</div>
							</div>
						</div>
						<?php }?>
					</div>
				</div>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
				<?php
		}
		else{
			$request = json_decode($request);
			$error_message = "Server Error " . $httpCode ." ". $request->Message;
			echo $error_message;
		}
	}

	//get pay in bank specific
	function pay2(){
		$merchantCode = 'D9174'; // dari duitku
		$apiKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // dari duitku
		$paymentAmount = 40000;
		$paymentMethod = 'BC'; // VC = Credit Card
		$merchantOrderId = time() . ''; // dari merchant, unik
		$productDetails = 'Tes pembayaran menggunakan Duitku';
		$email = 'azizmentor96@gmail.com'; // email pelanggan anda
		$phoneNumber = '089669001989'; // nomor telepon pelanggan anda (opsional)
		$additionalParam = ''; // opsional
		$merchantUserInfo = ''; // opsional
		$customerVaName = 'John Doe'; // tampilan nama pada tampilan konfirmasi bank
		$callbackUrl = 'https://payment.kampuskode.com/duitku/callback'; // url untuk callback
		$returnUrl = 'https://payment.kampuskode.com/duitku/cekpembayaran';//'http://example.com/return'; // url untuk redirect
		$expiryPeriod = 5000; // atur waktu kadaluarsa dalam hitungan menit
		$signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

		// Customer Detail
		$firstName = "John";
		$lastName = "Doe";

		// Address
		$alamat = "Jl. Kembangan Raya";
		$city = "Jakarta";
		$postalCode = "11530";
		$countryCode = "ID";

		$address = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address' => $alamat,
			'city' => $city,
			'postalCode' => $postalCode,
			'phone' => $phoneNumber,
			'countryCode' => $countryCode
		);

		$customerDetail = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			'billingAddress' => $address,
			'shippingAddress' => $address
		);


		$item1 = array(
			'name' => 'Test Item 1',
			'price' => 10000,
			'quantity' => 1);

		$item2 = array(
			'name' => 'Test Item 2',
			'price' => 30000,
			'quantity' => 3);

		$itemDetails = array(
			$item1, $item2
		);

		/*Khusus untuk metode pembayaran OL dan SL
		$accountLink = array (
			'credentialCode' => '7cXXXXX-XXXX-XXXX-9XXX-944XXXXXXX8',
			'ovo' => array (
				'paymentDetails' => array ( 
					0 => array (
						'paymentType' => 'CASH',
						'amount' => 40000,
					),
				),
			),
			'shopee' => array (
				'useCoin' => false,
				'promoId' => '',
			),
		);*/

		$params = array(
			'merchantCode' => $merchantCode,
			'paymentAmount' => $paymentAmount,
			'paymentMethod' => $paymentMethod,
			'merchantOrderId' => $merchantOrderId,
			'productDetails' => $productDetails,
			'additionalParam' => $additionalParam,
			'merchantUserInfo' => $merchantUserInfo,
			'customerVaName' => $customerVaName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			// 'accountLink' => $accountLink,
			'itemDetails' => $itemDetails,
			'customerDetail' => $customerDetail,
			'callbackUrl' => $callbackUrl,
			'returnUrl' => $returnUrl,
			'signature' => $signature,
			'expiryPeriod' => $expiryPeriod
		);

		$params_string = json_encode($params);
		//echo $params_string;
		$url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
		// $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
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
			// echo "paymentUrl :". $result['paymentUrl'] . "<br />";
			// echo "merchantCode :". $result['merchantCode'] . "<br />";
			// echo "reference :". $result['reference'] . "<br />";
			// echo "vaNumber :". $result['vaNumber'] . "<br />";
			// echo "amount :". $result['amount'] . "<br />";
			// echo "statusCode :". $result['statusCode'] . "<br />";
			// echo "statusMessage :". $result['statusMessage'] . "<br />";
			$data = [
				'id_user' => '1',
				'app_name' => 'SIMPERU',
				'merchantOrderId' => $merchantOrderId,
				'paymentUrl' => $result['paymentUrl'],
				'merchantCode' => $result['merchantCode'],
				'reference' => $result['reference'],
				'vaNumber' => $result['vaNumber'],
				'amount' => $result['amount'],
				'statusCode' => $result['statusCode'],
				'statusMessage' => 'WAITING',
				'paymentMethod' => $paymentMethod,
			];
			$this->db->insert('duitku', $data);
			header('location: '. $result['paymentUrl']);
		}
		else
		{
			$request = json_decode($request);
			$error_message = "Server Error " . $httpCode ." ". $request->Message;
			echo $error_message;
		}
	}

	function pay3(){
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Pay Duitku PopUp</title>
			<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
			<script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
		</head>
		<body>
			<button id="pay-button">Pay</button>

			<script>
				var payButton = document.getElementById('pay-button');
				payButton.addEventListener('click', function() {
					checkout.process("D9174WEJCDDCOS7EYW43", {
					defaultLanguage: "id", //opsional pengaturan bahasa
					successEvent: function(result){
					// tambahkan fungsi sesuai kebutuhan anda
						console.log('success');
						console.log(result);
						alert('Payment Success');
					},
					pendingEvent: function(result){
					// tambahkan fungsi sesuai kebutuhan anda
						console.log('pending');
						console.log(result);
						alert('Payment Pending');
					},
					errorEvent: function(result){
					// tambahkan fungsi sesuai kebutuhan anda
						console.log('error');
						console.log(result);
						alert('Payment Error');
					},
					closeEvent: function(result){
					// tambahkan fungsi sesuai kebutuhan anda
						console.log('customer closed the popup without finishing the payment');
						console.log(result);
						alert('customer closed the popup without finishing the payment');
					}
				}); 
				});
			</script>
		</body>
		</html>
		<?php
	}

	//callback response from duitku
	function callback(){
		$apiKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // API key anda
		$merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
		$amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
		$merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
		$productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
		$additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
		$paymentCode = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
		$resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
		$merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
		$reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
		$signature = isset($_POST['signature']) ? $_POST['signature'] : null; 

		//log callback untuk debug 
		// file_put_contents('callback.txt', "* Callback *\r\n", FILE_APPEND | LOCK_EX);

		if(!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature))
		{
			$params = $merchantCode . $amount . $merchantOrderId . $apiKey;
			$calcSignature = md5($params);

			if($signature == $calcSignature)
			{
				//Callback tervalidasi
				//Silahkan rubah status transaksi anda disini
				$this->check($merchantOrderId);
				echo "SUCCESS";
			}
			else
			{
				echo 'Bad Signature';
			}
		}
		else
		{
			echo 'Bad Parameter';
		}
		
	}

	function check($merchantOrderId){
        $merchantCode = 'D9174';
        $merchantKey = '11fca2d38ac9a876a5ad337006aa8aa3'; 

        $signature = md5($merchantCode . $merchantOrderId . $merchantKey);

        $params = array(
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature' => $signature
        );

        $params_string = json_encode($params);
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus';
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

        if($httpCode == 200){
            $result = json_decode($request, true);
			// echo "<pre>";print_r($result); echo "</pre>";
            $data = [
                "reference"     => $result['reference'] ,
                "amount"        => $result['amount'] ,
                "fee"           => $result['fee'] ,
                "statusCode"    => $result['statusCode'] ,
                "statusMessage" => $result['statusMessage']
            ];
            $this->db->where('merchantOrderId', $result['merchantOrderId'])->update('duitku', $data);
            $get = $this->db->get_where('duitku', ['merchantOrderId' => $result['merchantOrderId']])->row();
            
            // $tagihan = $result['amount'] + $result['fee'];
            // // $dataUpdate = [
            // //     'status_bayar'      => 'LUNAS',
            // //     'tagihan'           => rupiah($tagihan),
            // //     'noujian'           => $noujian,
            // //     'adm_pendaftaran'   => $adm,
            // //     'datepay'           => date('Y-m-d H:i:s')
            // // ];
            // // $this->db->where('id', $idmutasi)->update('mutasi_csantri', $dataUpdate);
			// $data = [
			// 	'merchantCode' => $result['merchantCode'],
			// 	'amount' => $result['amount'],
			// 	// 'merchantOrderId' => $result['merchantOrderId'],
			// 	// 'productDetail' => $result['productDetail'],
			// 	'additionalParam' => $result['additionalParam'],
			// 	'paymentMethod' => $result['paymentMethod'],
			// 	'resultCode' => $result['resultCode'],
			// 	// 'merchantUserId' => $result['merchantUserId'],
			// 	'reference' => $result['reference'],
			// 	// 'signature' => $result['signature'],
			// 	"statusMessage" => 'Success',
			// 	// 'data' => json_encode($data)
			// ];
			// $this->db->update('duitku', $data,['merchantOrderId' => $merchantOrderId]);
            
        }else
            echo $httpCode;
    }

	function cekpembayaran(){
		echo "cekpembayaran";
	}
}
