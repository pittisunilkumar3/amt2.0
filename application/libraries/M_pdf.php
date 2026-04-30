<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "third_party/omnipay/vendor/autoload.php";

use Mpdf\Mpdf;

class M_pdf
{
    public $pdf;

    public function __construct()
    {
        $CI = &get_instance();
        log_message('debug', 'mPDF class is loaded.');
    }

    public function load($param = null)
    {
        if ($param === null) {
            $param = [
                'mode'         => 'utf-8',
                'default_font' => 'dejavusans',
                'margin_left'   => 2,
                'margin_right'  => 2,
                'margin_top'    => 2,
                'margin_bottom' => 2,
                'format'        => 'Legal',
                'tempDir'       => APPPATH . 'third_party/omnipay/vendor/mpdf/mpdf/tmp/',
            ];
        } else {
            // Ensure tempDir is always set
            if (!isset($param['tempDir'])) {
                $param['tempDir'] = APPPATH . 'third_party/omnipay/vendor/mpdf/mpdf/tmp/';
            }
        }

        return new Mpdf($param);
    }
}
