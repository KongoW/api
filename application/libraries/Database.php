<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database
{
	private $db;
	private $data;

	// connect to db
	public function __construct()
	{
		$this->db = new PDO('mysql:host=localhost;dbname=api_test;charset=utf8', 'new_root', 'parol');
		$this->data = array('id','engine_volume', 'spaciousness', 'weight', 'color', 'brand', 'type');
	}

	// get all rows form the table `cars`
	public function getAll()
	{
		$output = $this->db->query('
			SELECT 
			`cars`.`id`, 
			`cars`.`engine_volume`, 
			`cars`.`spaciousness`, 
			`cars`.`weight`, 
			`cars`.`color`, 
			`brands`.`brand`, 
			`types`.`type` 
			FROM 
			`cars` 
			INNER JOIN `brands` ON `cars`.`brand_id` = `brands`.`id` 
			INNER JOIN `types` ON `cars`.`type_id` = `types`.`id`');

        $result = $output->fetchAll(PDO::FETCH_ASSOC);
        return $result;
	}

	public function getOne($id)
    {
        $query = '
			SELECT 
			`cars`.`id`, 
			`cars`.`engine_volume`, 
			`cars`.`spaciousness`, 
			`cars`.`weight`, 
			`cars`.`color`, 
			`brands`.`brand`, 
			`types`.`type` 
			FROM 
			`cars` 
			INNER JOIN `brands` ON `cars`.`brand_id` = `brands`.`id` 
			INNER JOIN `types` ON `cars`.`type_id` = `types`.`id`
			WHERE cars.id = ?';

        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


	// add a row to the table `cars`
	public function add(array $params) {

	    if (isset($params['id'])) {
	        return FALSE;
        }

        $valid = $this->validate_params($params, $this->data);

        if ( ! empty($valid)) {
            $params = $this->make_new_params($params);

            $query = $this->make_query_for_add($params);

            $exc_arr = $this->make_exc_array($params);

            return $this->execute_complex_query($query, $exc_arr); // true or false
        }

        return FALSE;
	}


	// update a row in the table `cars`
	public function update(array $params) {
	    $valid = $this->validate_params($params, $this->data);

	    if ( ! empty($valid)) {
	        $params = $this->make_new_params($params);

            $query = $this->make_query_for_update($params);

            $exc_arr = $this->make_exc_array($params);
                
            $exc = $this->db->prepare($query);
            return $exc->execute($exc_arr);
        }
	    return FALSE;
	}


	public function delete($id)
    {
        $query = 'SELECT id FROM cars WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( ! empty($result)) {
            $query = 'DELETE FROM cars WHERE id = ?';
            $stmt = $this->db->prepare($query);
            return $stmt->execute(array($id));
        }

        return FALSE;
    }


	# function - helpers ------------------------

	private function execute_complex_query($query, $exc_arr)
    {
        $exc = $this->db->prepare($query);
        $exc->execute($exc_arr);
        return $exc->fetchAll(PDO::FETCH_ASSOC);

    }


    private function make_new_params($params)
    {
        // change key 'type' to the 'type_id'
        if (isset($params['type'])) {
            $query_type = 'SELECT id FROM types WHERE `type` = :type';
            $exc_arr = array(':type' => $params['type']);
            $params['type_id'] = $this->execute_complex_query($query_type, $exc_arr)[0]['id'];
            unset($params['type']);
        }

        // change key 'brand' to the 'type_id'
        if (isset($params['brand'])) {
            $query_brand = 'SELECT id FROM brands WHERE `brand` = :brand';
            $exc_arr = array(':brand' => $params['brand']);
            $params['brand_id'] = $this->execute_complex_query($query_brand, $exc_arr)[0]['id'];
            unset($params['brand']);
        }

        return $params;
    }

	private function make_query_for_update(array $params)
    {
        $query = 'UPDATE cars SET';
        $first_param = TRUE;

        // make a main content of query
        foreach ($params as $key => $value) {
            if ($key != 'id') {
                if (!$first_param) {
                    $query .= ',';
                }

                $query .= " $key = :$key";
                $first_param = FALSE;
            }
        }

        $query .= ' WHERE id = :id';
        return $query;
    }

    private function make_query_for_add($params)
    {
        $query = 'INSERT INTO cars SET ';
        $first_param = TRUE;

        // make a main content of query
        foreach ($params as $key => $value) {
            if (!$first_param) {
                $query .= ',';
            }

            $query .= " $key = :$key";
            $first_param = FALSE;
        }

        return $query;
    }

    // make an array for execute
    private function make_exc_array($params) {
        foreach ($params as $key => $value) {
            $exc_arr[":$key"] = $value;
        }

        return $exc_arr;
    }


    // check validate of the keys and values before execute a query
    private function validate_params($pattern, $in_scan)
	{
        if ($this->validate_values($pattern) && $this->validate_keys($pattern, $in_scan)) {
            return TRUE;
        }
        return FALSE;
	}


    private function validate_keys($pattern, $scan)
    {
        foreach ($pattern as $key => $value) {
            if (!in_array($key, $scan)) {
				return FALSE;
            }
        }
        return TRUE;
    }


    private function validate_values($params)
    {
        foreach ($params as $key => $value) {
            $action = 'check_'.$key;
            $result = $this->$action($value);
            if ($result == FALSE) {
                echo $key;
                return FALSE;
            }
        }
        return TRUE;
    }

    private function check_id($value)
    {
        $query = 'SELECT id FROM cars WHERE id = :id';
        $result = $this->execute_complex_query($query, array(':id' => $value));
        if (empty($result)) {
            return FALSE;
        }
        return TRUE;

    }

    private function check_engine_volume($value)
    {
        if (preg_match('/^[.0-9]+$/', $value)) {
            if ($value > 1 && $value < 10) return TRUE;
        }
        return FALSE;
    }

    private function check_spaciousness($value)
    {
        if (preg_match('/^[0-9]+$/', $value)) {
            if (is_int(+$value) && $value > 1 && $value < 10) return TRUE;
        }
        return FALSE;
    }

    private function check_weight($value)
    {
        if (preg_match('/^[0-9]+$/', $value))
        {
            if (is_int(+$value) && $value > 600 && $value < 3000) return TRUE;
        }
        return FALSE;
    }

    private function check_color($value)
    {
        // проверка на hex
        if ( ! preg_match('/^[0-9A-fa-f]+$/', $value)) {
            return FALSE;
        }
        // check length of hex
        if (strlen($value) == 3 OR strlen($value) == 6) {
            return TRUE;
        }
        return FALSE;
    }

    private function check_brand($value)
    {
        $query = 'SELECT id FROM brands WHERE brand = :brand';
        $result = $this->execute_complex_query($query, array(':brand' => $value));
        if (empty($result)) {
            return FALSE;
        }
        return TRUE;
    }

    private function check_type($value)
    {
        $query = 'SELECT id FROM types WHERE `type` = :type';
        $result = $this->execute_complex_query($query, array(':type' => $value));
        if (empty($result)) {
            return FALSE;
        }
        return TRUE;
    }
}