<?php

defined('BASEPATH') or exit('No direct script access allowed');

#[\AllowDynamicProperties]
class MY_Form_validation extends CI_Form_validation {

    public function __construct() {
        parent::__construct();
    }

    /**
     * XSS Clean - delegate to Security library
     * This fixes CI 3.1.6 missing xss_clean rule for PHP 8.x
     */
    public function xss_clean($str)
    {
        return $this->CI->security->xss_clean($str);
    }

}
