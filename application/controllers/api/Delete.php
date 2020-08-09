<?php


class Delete extends MY_Controller
{
    public function deleteCar($id)
    {
        $is_query_success = $this->database->delete($id);

        if ($is_query_success) {
            $result = json_encode(array('status' => 200, 'description' => 'deleted'));
            echo $result;
            exit;
        }

        $this->show_error_uncorrected_data();
    }
}