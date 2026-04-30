<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsappconfig extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->db->table_exists('whatsapp_config')) {
            $this->load->model('Whatsappconfig_model');
        }
        if ($this->db->table_exists('whatsapp_messages')) {
            $this->load->model('Whatsappmessage_model');
        }
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'whatsappconfig/index');

        $data['title'] = $this->lang->line('whatsapp_messaging');

        if (!$this->db->table_exists('whatsapp_config')) {
            $data['tables_missing'] = true;
        } else {
            $data['tables_missing'] = false;
            $data['config'] = $this->Whatsappconfig_model->get();
            if ($this->db->table_exists('whatsapp_messages')) {
                $data['recent_messages'] = $this->Whatsappmessage_model->getRecent(20);
            } else {
                $data['recent_messages'] = array();
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('setting/whatsappsettings', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('phone_number_id', 'Phone Number ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('business_account_id', 'Business Account ID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('access_token', 'Access Token', 'required|trim|xss_clean');
        $this->form_validation->set_rules('verify_token', 'Verify Token', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $errors = array();
            $errors['phone_number_id']     = form_error('phone_number_id');
            $errors['business_account_id'] = form_error('business_account_id');
            $errors['access_token']        = form_error('access_token');
            $errors['verify_token']        = form_error('verify_token');
            echo json_encode(array('status' => 'fail', 'error' => $errors));
            return;
        }

        $data = array(
            'phone_number_id'     => $this->input->post('phone_number_id'),
            'business_account_id' => $this->input->post('business_account_id'),
            'access_token'        => $this->input->post('access_token'),
            'verify_token'        => $this->input->post('verify_token'),
            'api_version'         => $this->input->post('api_version') ? $this->input->post('api_version') : 'v21.0',
            'is_active'           => $this->input->post('is_active') ? 1 : 0,
            'webhook_url'         => $this->input->post('webhook_url'),
            'provider'            => 'meta',
        );

        $this->Whatsappconfig_model->add($data);
        echo json_encode(array('status' => 'success', 'message' => $this->lang->line('success_message')));
    }

    public function savetwilio()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('twilio_account_sid', 'Twilio Account SID', 'required|trim|xss_clean');
        $this->form_validation->set_rules('twilio_auth_token', 'Authentication Token', 'required|trim|xss_clean');
        $this->form_validation->set_rules('twilio_phone_number', 'Registered Phone Number', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $errors = array();
            $errors['twilio_account_sid']  = form_error('twilio_account_sid');
            $errors['twilio_auth_token']   = form_error('twilio_auth_token');
            $errors['twilio_phone_number'] = form_error('twilio_phone_number');
            echo json_encode(array('status' => 'fail', 'error' => $errors));
            return;
        }

        $data = array(
            'twilio_account_sid'  => $this->input->post('twilio_account_sid'),
            'twilio_auth_token'   => $this->input->post('twilio_auth_token'),
            'twilio_phone_number' => $this->input->post('twilio_phone_number'),
            'twilio_is_active'    => $this->input->post('twilio_is_active') ? 1 : 0,
            'provider'            => 'twilio',
        );

        $this->Whatsappconfig_model->add($data);
        echo json_encode(array('status' => 'success', 'message' => $this->lang->line('success_message')));
    }

    public function testconnection()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_edit')) {
            access_denied();
        }

        $config = $this->Whatsappconfig_model->get();

        if (!$config || empty($config->access_token)) {
            echo json_encode(array('success' => false, 'message' => 'WhatsApp is not configured'));
            return;
        }

        $api_version = !empty($config->api_version) ? $config->api_version : 'v21.0';
        $url = 'https://graph.facebook.com/' . $api_version . '/' . $config->phone_number_id;

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $config->access_token
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            echo json_encode(array('success' => false, 'message' => 'Connection failed: ' . $curl_error));
        } elseif ($http_code == 200) {
            $data = json_decode($response, true);
            $display_name = isset($data['verified_name']) ? $data['verified_name'] : $config->phone_number_id;
            echo json_encode(array('success' => true, 'message' => 'Connected successfully! Verified Name: ' . $display_name));
        } else {
            $data = json_decode($response, true);
            $error = isset($data['error']['message']) ? $data['error']['message'] : 'HTTP ' . $http_code;
            echo json_encode(array('success' => false, 'message' => 'Error: ' . $error));
        }
    }

    public function testtwilio()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_edit')) {
            access_denied();
        }

        $config = $this->Whatsappconfig_model->get();

        if (!$config || empty($config->twilio_account_sid)) {
            echo json_encode(array('success' => false, 'message' => 'Twilio is not configured'));
            return;
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $config->twilio_account_sid . '.json';

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD  => $config->twilio_account_sid . ':' . $config->twilio_auth_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            echo json_encode(array('success' => false, 'message' => 'Connection failed: ' . $curl_error));
        } elseif ($http_code == 200) {
            $data = json_decode($response, true);
            $friendly_name = isset($data['friendly_name']) ? $data['friendly_name'] : $config->twilio_account_sid;
            echo json_encode(array('success' => true, 'message' => 'Connected successfully! Account: ' . $friendly_name));
        } else {
            $data = json_decode($response, true);
            $error = isset($data['message']) ? $data['message'] : 'HTTP ' . $http_code;
            echo json_encode(array('success' => false, 'message' => 'Error: ' . $error));
        }
    }

    public function getStats()
    {
        if (!$this->db->table_exists('whatsapp_messages')) {
            echo json_encode(array('today' => 0, 'sent' => 0, 'pending' => 0, 'failed' => 0, 'delivered' => 0, 'read' => 0));
            return;
        }
        echo json_encode(array(
            'today'     => $this->Whatsappmessage_model->countToday(),
            'sent'      => $this->Whatsappmessage_model->countByStatus('sent'),
            'pending'   => $this->Whatsappmessage_model->countByStatus('pending'),
            'failed'    => $this->Whatsappmessage_model->countByStatus('failed'),
            'delivered' => $this->Whatsappmessage_model->countByStatus('delivered'),
            'read'      => $this->Whatsappmessage_model->countByStatus('read'),
        ));
    }

    public function messages()
    {
        if (!$this->rbac->hasPrivilege('whatsapp_setting', 'can_view')) {
            access_denied();
        }

        $data['title']    = 'WhatsApp Messages Log';
        $data['messages'] = $this->Whatsappmessage_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('setting/whatsapp_messages', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Webhook endpoint for Meta verification
     */
    public function webhook()
    {
        $this->load->library('whatsappgateway');
        $mode      = isset($_GET['hub_mode']) ? $_GET['hub_mode'] : '';
        $token     = isset($_GET['hub_verify_token']) ? $_GET['hub_verify_token'] : '';
        $challenge = isset($_GET['hub_challenge']) ? $_GET['hub_challenge'] : '';

        $result = $this->whatsappgateway->verifyWebhook($mode, $token, $challenge);

        if ($result !== false) {
            header('HTTP/1.1 200 OK');
            header('Content-Type: text/plain');
            echo $result;
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo 'Verification failed';
        }
    }

    /**
     * Webhook endpoint for receiving messages and status updates
     */
    public function webhook_receive()
    {
        $this->load->library('whatsappgateway');
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($payload) {
            $this->whatsappgateway->processWebhook($payload);
        }

        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'received'));
    }
}
