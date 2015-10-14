<?php

class OrchestrationType extends BasicEnum
{
    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    const AUTOSPELLCHECKER = 'autoSpellchecker';
    const FALLBACKTOOPTIONAL = 'fallbackToOptional';
}