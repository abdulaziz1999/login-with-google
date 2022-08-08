<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Midtrans extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	//get create invoice page from duitku
	function pay(){
		?>
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<script type="text/javascript"
					src="https://app.sandbox.midtrans.com/snap/snap.js"
					data-client-key="SB-Mid-client-OETvDMBW_0chH017"></script>
		</head>
		<body>
			<button id="pay-button">Pay!</button>
			<script type="text/javascript">
			var payButton = document.getElementById('pay-button');
			payButton.addEventListener('click', function () {
				snap.pay('3d35df63-551e-451a-8450-7022745c862d');
			});
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
