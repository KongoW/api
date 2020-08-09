<?php

class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');

		$this->load->library('Database');
	}

	protected function show_error_uncorrected_data()
    {
        $result = json_encode(array('status' => 400, 'description' => 'uncorrected data'));
        echo $result;
    }
}