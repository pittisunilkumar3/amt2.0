<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Media_storage
{

    private $_CI;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->library('customlib');

    }

    private function _getFolderPath()
    {
        $folder_path = $this->_CI->customlib->getFolderPath();
        if (empty($folder_path) || !is_dir($folder_path)) {
            return FCPATH;
        }
        return $folder_path;
    }

    private function _getBaseUrl()
    {
        $stored_url = $this->_CI->customlib->getBaseUrl();
        $ci_base    = base_url();

        if (empty($stored_url)) {
            return $ci_base;
        }

        // If the stored URL host doesn't match the current server, use CI's base_url
        $stored_host = parse_url($stored_url, PHP_URL_HOST);
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : parse_url($ci_base, PHP_URL_HOST);

        if ($stored_host && $current_host && strtolower($stored_host) !== strtolower($current_host)) {
            return $ci_base;
        }

        return $stored_url;
    }

    public function fileupload($media_name, $upload_path = "")
    {
        if (isset($_FILES[$media_name]) && file_exists($_FILES[$media_name]['tmp_name']) && $_FILES[$media_name]['error'] != UPLOAD_ERR_NO_FILE) {

            $name        = $_FILES[$media_name]['name'];
            $file_name   = time() . "-" . uniqid(rand()) . "!" . $name;
            $folder_path = $this->_getFolderPath();
            $destination = $folder_path . $upload_path . $file_name;

            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            if (move_uploaded_file($_FILES[$media_name]["tmp_name"], $destination)) {

                return $file_name;
            }

        }

        return null;
    }

    public function filedownload($file_name, $download_path = "")
    {

        $folder_path = $this->_getFolderPath();
        $file_url           = $folder_path . $download_path . "/" . $file_name;
        $download_file_name = substr($file_name, (strpos($file_name, '!') + 1));
        $this->_CI->load->helper('download');
        $data = file_get_contents($file_url);
        force_download($download_file_name, $data);

    }

    public function fileview($file_name)
    {
        if (!IsNullOrEmptyString($file_name)) {

            $download_file_name = substr($file_name, (strpos($file_name, '!') + 1));
            return $download_file_name;
        }
        return null;

    }

    public function getImageURL($file_name)
    {
        if (!IsNullOrEmptyString($file_name)) {

            $base_url = $this->_getBaseUrl();
            $download_file_name = $base_url . $file_name . img_time();
            return $download_file_name;
        }
        return null;

    }

    public function filedelete($file_name, $path = "")
    {
        if (!IsNullOrEmptyString($file_name)) {

            $folder_path = $this->_getFolderPath();
            $url = $folder_path . $path . "/" . $file_name;

            if (file_exists($url)) {

                if (unlink($url)) {
                    return true;
                }

            }
        }

        return false;
    }
    
    
    public function applicationfileupload($media_name, $upload_path = "")
    {
        if (isset($_FILES[$media_name]) && file_exists($_FILES[$media_name]['tmp_name']) && $_FILES[$media_name]['error'] != UPLOAD_ERR_NO_FILE) {

            $folder_path = $this->_getFolderPath();
            $destination = $folder_path . $upload_path;

            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            if (move_uploaded_file($_FILES[$media_name]["tmp_name"], $destination)) {
                return $upload_path;
            }
        }

        return null;
    }
    
    
    
    public function applicationfileviewpath($download_path = "")
    {
        $folder_path = $this->_getFolderPath();
        $file_url = $folder_path . $download_path;

        if (file_exists($file_url)) {
            return $file_url;
        } else {
            show_error('The requested file does not exist.', 404);
        }

    }
    
    public function applicationfiledownload($file_name, $download_path = "")
    {
        $folder_path = $this->_getFolderPath();
        $file_url = $folder_path . $download_path;

        if (file_exists($file_url)) {
            $this->_CI->load->helper('download');
            $data = file_get_contents($file_url);
            force_download($file_name, $data);
        } else {
            show_error('The requested file does not exist.', 404);
        }

    }


}
