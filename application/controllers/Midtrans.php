<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Midtrans extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	function getTokenSnap(){
		//curl post to midtrans server url = https://app.sandbox.midtrans.com/snap/v1/transactions 
		//in field post 
		// "transaction_details": {
		// 	"order_id": "ORDER-101-1660039878",
		// 	"gross_amount": 10000
		//   }, 
		//   "credit_card": {
		// 	"secure": true
		//   }
		$url = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic '. base64_encode('SB-Mid-server-1lSFxCyowgxYOff57S0uSXoq')
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
			'transaction_details' => [
				'order_id' => 'ORDER-101-'.time(),
				'gross_amount' => 10000
			],
			'credit_card' => [
				'secure' => true
			]
		]));
		$result = curl_exec($ch);
		curl_close($ch);
		//echo set content type json
		header('Content-Type: application/json');
		$data = json_decode($result);
		$data_array = [
			'token' => $data->token,
			'redirect_url' => $data->redirect_url
		];
		echo json_encode($data_array);
	}

	//get create invoice page from duitku
	function pay(){
		$token = $this->input->get('token');
		?>
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<script type="text/javascript"
					src="https://app.sandbox.midtrans.com/snap/snap.js"
					data-client-key="SB-Mid-client-OETvDMBW_0chH017"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		</head>
		<body>
			<h4>Sepatu</h4>
			<p>Harga : Rp 10000</p>
			<input type="hidden" id="base_url" value="<?= base_url(); ?>">
			<button id="pay-button">Pay! </button>
			<script type="text/javascript">
			var base_url = document.getElementById('base_url').value;
			var payButton = document.getElementById('pay-button');
			payButton.addEventListener('click', function () {
				//ajax getSnapToken url = http://localhost:8001/midtrans/getTokenSnap
				$.ajax({
					url: base_url +'midtrans/getTokenSnap',
					type: 'GET',
					success: function (data) {
						console.log(data.token);
						snap.pay(data.token, {
						onSuccess: function(result){
							console.log(result);
							alert('success');
						},
						onPending: function(result){
							console.log(result);
							alert('pending');
						},
						onError: function(result){
							console.log(result);
							alert('error');
						}
					});
					}
				});
			});
			</script>
		</body>
		</html>
		<?php
	}

	function pay2(){
		$token = $this->input->get('token');
		?>
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<script type="text/javascript"
					src="https://app.sandbox.midtrans.com/snap/snap.js"
					data-client-key="SB-Mid-client-OETvDMBW_0chH017"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		</head>
		<body>
			<h4>Sepatu</h4>
			<p>Harga : Rp 10000</p>
			<input type="hidden" id="base_url" value="<?= base_url(); ?>">
			<button id="pay-button">Pay! </button>
			<script type="text/javascript">
			// var base_url = document.getElementById('base_url').value;
			var payButton = document.getElementById('pay-button');
			payButton.addEventListener('click', function() {
				//ajax getSnapToken url = http://localhost:8001/midtrans/getTokenSnap
			
						snap.pay(data.token, {
						onSuccess: function(result){
							console.log(result);
							alert('success');
						},
						onPending: function(result){
							console.log(result);
							alert('pending');
						},
						onError: function(result){
							console.log(result);
							alert('error');
						}
					});
				}
					
			</script>
		</body>
		</html>
		<?php
	}

	function success(){
		echo 'success';
		echo $this->input->get('order_id');
		echo $this->input->get('status_code');
		echo $this->input->get('transaction_status');
	}

	function error(){
		echo 'Error';
		echo $this->input->get('order_id');
		echo $this->input->get('status_code');
		echo $this->input->get('transaction_status');
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

	function cekpembayaran(){
		echo "cekpembayaran";
	}
}
