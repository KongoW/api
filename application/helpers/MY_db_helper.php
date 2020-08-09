<?php

class MY_db_helper {

    public function __construct()
    {
        echo "it's construct function";
    }

	public function scan_params($pattern, $in_scan)
	{
		foreach ($patter as $key => $value) {
			if (!(array_key_exists($key, $in_scan))) {
				return FALSE;
			}
		}
		return TRUE;
	}
}