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
		$callbackUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/callback'; // url untuk callback
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
			header('location: '. $result['paymentUrl']);
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
						<div class="col-md-3 mb-5">
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
		$callbackUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/callback'; // url untuk callback
		$returnUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/cekpembayaran';//'http://example.com/return'; // url untuk redirect
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
			//header('location: '. $result['paymentUrl']);
			echo "paymentUrl :". $result['paymentUrl'] . "<br />";
			echo "merchantCode :". $result['merchantCode'] . "<br />";
			echo "reference :". $result['reference'] . "<br />";
			// echo "vaNumber :". $result['vaNumber'] . "<br />";
			// echo "amount :". $result['amount'] . "<br />";
			echo "statusCode :". $result['statusCode'] . "<br />";
			echo "statusMessage :". $result['statusMessage'] . "<br />";
		}
		else
		{
			$request = json_decode($request);
			$error_message = "Server Error " . $httpCode ." ". $request->Message;
			echo $error_message;
		}
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
				// file_put_contents('callback.txt', "* Berhasil *\r\n\r\n", FILE_APPEND | LOCK_EX);

			}
			else
			{
				// file_put_contents('callback.txt', "* Bad Signature *\r\n\r\n", FILE_APPEND | LOCK_EX);
				throw new Exception('Bad Signature');
			}
		}
		else
		{
			// file_put_contents('callback.txt', "* Bad Parameter *\r\n\r\n", FILE_APPEND | LOCK_EX);
			throw new Exception('Bad Parameter');
		}

	}

	function grab(){
		?>
			<!DOCTYPE html>
			<html>
			<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
			<title>Tekhnik Grabbing With Native PHP</title>
			<style>
			body{
				padding:10px;
			}
			</style>
			<link rel="stylesheet" href="https://sandbox.duitku.com/Styles/v2/bootstrap.min.css">
			<link href="https://sandbox.duitku.com/Styles/v2/bootstrap.min.css" rel="stylesheet" />
			<script src="https://sandbox.duitku.com/Scripts/jquery-3.4.1.min.js" type="d072d9a88405a0cd56043694-text/javascript"></script>
			<script src="https://sandbox.duitku.com/Scripts/v2/bootstrap.min.js" type="d072d9a88405a0cd56043694-text/javascript"></script>
			<link href="https://fonts.googleapis.com/css?family=Oxygen|PT+Sans|Quicksand" rel="stylesheet" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
			<link href="https://sandbox.duitku.com/Styles/v2/styleVA-20210517.css?v=25012022" rel="stylesheet" />
			<script src="https://sandbox.duitku.com/Scripts/v2/duitkuVA.js?v=22062021" type="d072d9a88405a0cd56043694-text/javascript"></script>
			</head>
			<body>
			<?php
			$konten = file_get_contents("https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=BCPBD7PU61YZOPPCT");
			// $pecah1 = explode("<table>",$konten);
			// $pecah2 = explode("</table>",$pecah1[1]);
			// echo $pecah2[0];
			echo $konten;
			?>
			<script >

				$(document).ready(function () {
					var today = new Date();
					var dd = String(today.getDate()).padStart(2, '0');
					var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
					var yyyy = today.getFullYear();

					today = dd + '-' + mm + '-' + yyyy;
					document.getElementById("thisday").innerHTML = today;
				});

				function cc_format(value) {
					var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
					var matches = v.match(/\d{4,16}/g);
					var match = matches && matches[0] || ''
					var parts = []
					for (i = 0, len = match.length; i < len; i += 4) {
						parts.push(match.substring(i, i + 4))
					}
					if (parts.length) {
						return parts.join(' ')
					} else {
						return v
					}
				}

				$(document).ready(function () {
					var codeCheck = document.getElementById("TextBoxKodeBank").value;
					var value = "";
					if (codeCheck !== "490")
					{
						value = cc_format(document.getElementById("TextBoxVANumber").value);
					} else
					{
						value = document.getElementById("TextBoxVANumber").value;
					}
					
					document.getElementById("TextBoxVANumber").value = value;
					document.getElementById("TextBoxVANumber").text = value;
				});

				//duitku.com new tab
				function newtab() {
					window.open("https://www.duitku.com/en/");
				};

				function permatanet() {
					window.open("https://new.permatanet.com");
				};

				function ibanksampoerna() {
					window.open("https://ibank.banksampoerna.co.id/");
				};

				//copy text to clipboard
				function copy() {
					/* Get the text field */
					var copyText = document.getElementById("TextBoxVANumber");
					var el = document.createElement('textarea');
					el.value = copyText.value.replace(/\s/g, '');
					el.setAttribute('readonly', '');
					el.style.position = 'absolute';
					el.style.left = '-9999px';
					document.body.appendChild(el);

					/* Select the text field */
					el.select();
					el.setSelectionRange(0, 99999); /*For mobile devices*/

					/* Copy the text inside the text field */
					document.execCommand("copy");
					document.body.removeChild(el);

					/* Alert the copied text */
					// alert("Copied the text: " + copyText.value);


					//snackbar notif
					// Get the snackbar DIV
					var x = document.getElementById("snackbar");

					// Add the "show" class to DIV
					x.className = "show";

					// After 3 seconds, remove the show class from DIV
					setTimeout(function () { x.className = x.className.replace("show", ""); }, 1000);
				}

				if (window.history.replaceState) {
					window.history.replaceState(null, null, window.location.href);
				}

				// PREVENT Double clik
				var submit = 0;
				function CheckDouble() {
					if (++submit > 1) {        
						return false;
					}
				}

			</script>
			<script >
				function startTimer() {
					var expiredDate = document.querySelector('#LabelexpiredDate').textContent;
					var duration = expiredDate;
					console.log(duration);
					var timer = duration, hours, minutes, seconds;
					var x = setInterval(function () {
						var hours = parseInt(timer / 3600, 10);
						var minutes = parseInt((timer / 60) % 60, 10);
						var seconds = parseInt(timer % 60, 10);

						hours = hours < 10 ? "0" + hours : hours;
						minutes = minutes < 10 ? "0" + minutes : minutes;
						seconds = seconds < 10 ? "0" + seconds : seconds;


						document.querySelector('#timer').textContent = hours + ":" + minutes + ":" + seconds;
						if (hours == 0) {
							document.querySelector('#timer').textContent = minutes + ":" + seconds;
						}

						if (--timer < 0) {
							var params = new URLSearchParams(window.location.search);
							var ref = params.get('ref');
							clearInterval(x);
							window.location = "/TopUp/v2/ExpiredPage.aspx?reference=" + ref;

						}
					}, 1000);
				}
				startTimer();
			</script>
			<script src="https://sandbox.duitku.com/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="d072d9a88405a0cd56043694-|49" defer=""></script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194" integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw==" data-cf-beacon='{"rayId":"73712a088a30496b","token":"835b6121832444e4a7aaca4aaf438a0d","version":"2022.6.0","si":100}' crossorigin="anonymous"></script>
			</body>
			</html>
		<?php
	}

	function grab_curl(){
		// inisialisasi CURL
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_USERAGENT => "spider",
			CURLOPT_AUTOREFERER => true,
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_SSL_VERIFYPEER => false
			);
			$ch = curl_init("https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=BCPBD7PU61YZOPPCT");
			curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err = curl_errno( $ch );
			$errmsg = curl_error( $ch );
			$header = curl_getinfo( $ch );
			curl_close( $ch );
			echo $content;
	}

	function grab_curl_2(){
		// inisialisasi CURL
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_USERAGENT => "spider",
			CURLOPT_AUTOREFERER => true,
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_SSL_VERIFYPEER => false
			);
			$ch = curl_init("https://sandbox.duitku.com/TopUp/v2/DuitkuNotification.aspx?reference=BCPBD7PU61YZOPPCT");
			curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err = curl_errno( $ch );
			$errmsg = curl_error( $ch );
			$header = curl_getinfo( $ch );
			curl_close( $ch );
			echo $content;
	}

	function cekpembayaran(){
		echo "cekpembayaran";
	}
}
