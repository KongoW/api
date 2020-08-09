<?php

class Search extends MY_Controller
{
	public function index()
	{
		echo 'neno';
	}

	public function getAll()
	{
		$response = $this->database->getAll();
		$json_response = json_encode($response);
		echo $json_response;
	}

	public function getCar($id)
    {
        $response = $this->database->getOne($id);
		$json_response = json_encode($response);
		echo $json_response;
    }

}