<?php

class Add extends MY_Controller
{
	public function addCar()
	{
        //get data form body of query
        $data = fopen("php://input", "r");
        $params = json_decode(fgets($data), 1);

        // check data in body
        if (is_array($params)) {
            $is_query_success = $this->database->add($params);

            if ($is_query_success) {
                $result = json_encode(array('status' => 201, 'description' => 'created'));
                echo $result;
                exit;
            }

            $this->show_error_uncorrected_data();
            exit;
        }

        $this->show_error_uncorrected_data();
	}
}