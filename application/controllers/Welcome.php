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

	function list(){
		$data = $this->db->get('note_app')->result();

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($data));
	}

	function add(){
		//insert data field title, content, and date
		$this->output->set_content_type('application/json');
		$data = [
			'title' 	=> $this->input->post('title'),
			'content' 	=> $this->input->post('content'),
			'date' 		=> date('Y-m-d H:i:s')
		];
		$result = $this->db->insert('note_app', $data);

		// if insert affected row is 1, then return the data
		if($result){
			$this->output->set_output(json_encode([
				'success' => true,
				'data' => $data,
				'message' => 'Data inserted successfully'
			]));
		}else{
			$this->output->set_output(json_encode([
				'success' => false,
				'message' => 'Data not inserted'
			]));
		}
	}

	function update(){
		$id = $this->input->post('id');
		$data = [
			'title' 	=> $this->input->post('title'),
			'content' 	=> $this->input->post('content'),
		];
		$this->db->where('id', $id);
		$result = $this->db->update('note_app', $data);
		if($result){
			$this->output->set_output(json_encode([
				'success' => true,
				'data' => $data,
				'message' => 'Data updated successfully'
			]));
		}else{
			$this->output->set_output(json_encode([
				'success' => false,
				'message' => 'failed to update data'
			]));
		}
	}

	function delete(){
		$id = $this->input->post('id');
		$this->db->where('id', $id);
		$result = $this->db->delete('note_app');
		if($result){
			$this->output->set_output(json_encode([
				'success' => true,
				'message' => 'Data deleted successfully'
			]));
		}else{
			$this->output->set_output(json_encode([
				'success' => false,
				'message' => 'failed to delete data'
			]));
		}
	}

	function get_data(){
		$id = $this->input->get('id');
		$this->db->where('id', $id);
		$result = $this->db->get('note_app')->row();
		if($result){
			$this->output->set_output(json_encode([
				'success' => true,
				'data' => $result,
				'message' => 'Data retrieved successfully'
			]));
		}else{
			$this->output->set_output(json_encode([
				'success' => false,
				'message' => 'failed to get data'
			]));
		}
	}

}
