<?php

function debug($var, $is_info = FALSE) {
	echo '<pre>';
	
	if ($is_info) {
		print_r($var);
	} else {
		var_dump($var);
	}
	echo '<pre>';
	exit;
}