<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    public function index()
	{
		include_once APPPATH . "../vendor/autoload.php";
		  $google_client = new Google_Client();
		  $google_client->setClientId('780737249117-3qhemvbnfc1c291svnvdv8hfapphpc7r.apps.googleusercontent.com'); //masukkan ClientID anda 
		  $google_client->setClientSecret('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'); //masukkan Client Secret Key anda
		  $google_client->setRedirectUri('http://localhost/google/login'); //Masukkan Redirect Uri anda
		  $google_client->addScope('email');
		  $google_client->addScope('profile');

			if(isset($_GET["code"])){
			$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
			if(!isset($token["error"])){
					$google_client->setAccessToken($token['access_token']);
					$this->session->set_userdata('access_token', $token['access_token']);
					$google_service = new Google_Service_Oauth2($google_client);
					$data = $google_service->userinfo->get();
					$current_datetime = date('Y-m-d H:i:s');
					$user_data = [
						'first_name' 		=> $data['given_name'],
						'last_name'  		=> $data['family_name'],
						'email_address' 	=> $data['email'],
						'profile_picture'	=> $data['picture'],
						'updated_at' 		=> $current_datetime
					];
					$this->session->set_userdata('user_data', $user_data);
				}									
			}

			$login_button = '';
			if(!$this->session->userdata('access_token')){
				$login_button = '<a href="'.$google_client->createAuthUrl().'" ><img src="https://1.bp.blogspot.com/-gvncBD5VwqU/YEnYxS5Ht7I/AAAAAAAAAXU/fsSRah1rL9s3MXM1xv8V471cVOsQRJQlQCLcBGAsYHQ/s320/google_logo.png" /></a>';
				// $login_button = '<a href="#" onclick="loginGoogle()"><img src="https://1.bp.blogspot.com/-gvncBD5VwqU/YEnYxS5Ht7I/AAAAAAAAAXU/fsSRah1rL9s3MXM1xv8V471cVOsQRJQlQCLcBGAsYHQ/s320/google_logo.png" /></a>';
				$data['login_button'] = $login_button;
				$data['url'] = $google_client->createAuthUrl();
				$this->load->view('google_login', $data);
			}else{
				// echo "Login success";
				$this->load->view('google_login');
			}
	}

	public function logout()
	 {
	  $this->session->unset_userdata('access_token');
	  $this->session->unset_userdata('user_data');
	  redirect('login');
	//   echo "Logout berhasil";
	 }

	 public function tes()
	 {
		echo '<script>window.open("https://www.wappalyzer.com/", "_blank", "width=500,height=600");</script>';
		// https://accounts.google.com/o/oauth2/v2/auth/oauthchooseaccount?gsiwebsdk=3&client_id=549970890748-4c56itq8mna6l1pdgvalflaic4lddotg.apps.googleusercontent.com&scope=profile%20email&redirect_uri=storagerelay%3A%2F%2Fhttps%2Fshopee.co.id%3Fid%3Dauth494370&prompt=consent&access_type=offline&response_type=code&include_granted_scopes=true&enable_serial_consent=true&service=lso&o2v=2&flowName=GeneralOAuthFlow
		// https://accounts.google.com/signin/oauth/consent?authuser=0&part=AJi8hAMhg3bmhMnZaCkSh88J64GbtxfGQJR0ruwvk8jTrSjefT_8jrtqIxySZ0w6xOUbOwMQRz-94ji68Axkw-y4naWRWd98wCgQ_DZYFa6cN1ynvb4t93RzT3gotuEQdIVA-INku0StQm_M0RXyTq-n0-yadYFZOCOtZMp8GAJBMD3-yk9PVDDFBg7E9a0jMt-rKm7czwz3-Wf1NQJuWXHJTswzauKnm5JWUS4mxgk-YwsLT8MItdWi2XoiTT0qQYlTqsDzt5-MOZnfm1zH2fe-KPCDhWeY45yanrOzFkQRRRcp7sPDQcu3bqTkFaWnfA2qSG_nmgOgtmSJup-AnTEuRrSN5rCa0K4AZynEKQIxIJ1YSvdGKrioBnyLtp2wksCnwp_3pzDdxM3MnGZhg4p4DFqnIki5F_dk6Z8tl4jXKg4z0zi-ZQ01au9Nn_zdrjqiJrS5jtb9zQ2nTcJPnzbx60XddKEM4g&as=S1242123647%3A1684386347061309&client_id=549970890748-4c56itq8mna6l1pdgvalflaic4lddotg.apps.googleusercontent.com&pli=1&rapt=AEjHL4P49wJAo5UdrCljZo8EmIbA_FS2CGtL9fMjLSReQ06peeQgRpdu7aOwFwF6WvNL9XPztvMKg9KHSget-aUq6OB8HlMfVA#
		// https://accounts.google.com/o/oauth2/v2/auth/oauthchooseaccount?response_type=code&access_type=online&client_id=780737249117-3qhemvbnfc1c291svnvdv8hfapphpc7r.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2Fgoogle%2Flogin&state&scope=email%20profile&approval_prompt=auto&service=lso&o2v=2&flowName=GeneralOAuthFlow
	 }

}