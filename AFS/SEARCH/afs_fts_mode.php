<?php

require_once 'COMMON/afs_tools.php';

class AfsFtsMode extends BasicEnum {

    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    const MANDATORY = 'mandatory';
    const OPTIONAL = 'optional';
} 