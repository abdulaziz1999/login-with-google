<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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

	function test()
	{
		//example json data
		$data = [
			'name' => 'John Doe',
			'age' => '35',
			'address' => '123 Main St.',
			'city' => 'Anytown',
			'state' => 'CA',
			'zip' => '90210'
		];
		//set the response format to json
		$this->output->set_content_type('application/json');
		//output the data in json format
		$this->output->set_output(json_encode($data));

	}

}
