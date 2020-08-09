<?php

class Update extends MY_Controller
{
    public function updateCar($id)
	{
	    //get data form body of query
		$data = fopen("php://input", "r");
		$params = json_decode(fgets($data), 1);

		// check data in body
		if (is_array($params)) {
		    $params['id'] = $id;
		    $is_query_success = $this->database->update($params);

		    if ($is_query_success) {
                $result = json_encode(array('status' => 200, 'description' => 'updated'));
                echo $result;
                exit;
            }

		    $this->show_error_uncorrected_data();
		    exit;
        }

		$this->show_error_uncorrected_data();
	}
}