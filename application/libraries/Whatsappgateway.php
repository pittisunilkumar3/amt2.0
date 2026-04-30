<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * WhatsApp Cloud API Gateway
 * Integrates with Meta WhatsApp Business Cloud API
 * Uses approved message templates to send notifications
 */
class Whatsappgateway
{
    private $CI;
    private $api_base_url = 'https://graph.facebook.com';

    public function __construct()
    {
        $this->CI = &get_instance();

        // Only load models if the database tables exist
        if ($this->CI->db->table_exists('whatsapp_config')) {
            $this->CI->load->model('Whatsappconfig_model');
        }
        if ($this->CI->db->table_exists('whatsapp_messages')) {
            $this->CI->load->model('Whatsappmessage_model');
        }
    }

    /**
     * Get active provider configuration
     */
    private function getConfig()
    {
        $config = $this->CI->Whatsappconfig_model->get();
        if (!$config) {
            return null;
        }

        // Determine active provider
        $provider = 'meta';
        if (isset($config->twilio_is_active) && $config->twilio_is_active == 1) {
            $provider = 'twilio';
        }

        $config->_active_provider = $provider;
        return $config;
    }

    /**
     * Send a WhatsApp message using a template
     * Automatically routes to Meta or Twilio based on active provider
     */
    public function sendTemplateMessage($phone, $template, $components = array(), $event_type = null, $recipient_name = null, $language = 'en_US')
    {
        $config = $this->getConfig();

        if (!$config) {
            return array('success' => false, 'error' => 'WhatsApp is not configured');
        }

        // Route to appropriate provider
        if ($config->_active_provider === 'twilio') {
            return $this->sendTwilioTemplateMessage($config, $phone, $template, $components, $event_type, $recipient_name, $language);
        } else {
            return $this->sendMetaTemplateMessage($config, $phone, $template, $components, $event_type, $recipient_name, $language);
        }
    }

    /**
     * Send via Meta WhatsApp Cloud API
     */
    private function sendMetaTemplateMessage($config, $phone, $template, $components = array(), $event_type = null, $recipient_name = null, $language = 'en_US')
    {
        if ($config->is_active != 1) {
            return array('success' => false, 'error' => 'Meta WhatsApp is not active');
        }

        if (empty($config->phone_number_id) || empty($config->access_token)) {
            return array('success' => false, 'error' => 'WhatsApp Phone Number ID or Access Token is missing');
        }

        // Ensure phone format: no +, no spaces, no dashes
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10) {
            return array('success' => false, 'error' => 'Invalid phone number');
        }

        $api_version = !empty($config->api_version) ? $config->api_version : 'v21.0';
        $url = $this->api_base_url . '/' . $api_version . '/' . $config->phone_number_id . '/messages';

        // Build template components
        $template_components = array();

        // Body parameters (the variable replacements)
        if (!empty($components)) {
            $body_params = array();
            foreach ($components as $param) {
                $body_params[] = array(
                    'type' => 'text',
                    'text' => (string)$param
                );
            }

            // Only add body component if there are parameters
            if (!empty($body_params)) {
                $template_components[] = array(
                    'type' => 'body',
                    'parameters' => $body_params
                );
            }
        }

        // Build the request payload
        $payload = array(
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => array(
                'name' => $template,
                'language' => array(
                    'code' => $language
                )
            )
        );

        if (!empty($template_components)) {
            $payload['template']['components'] = $template_components;
        }

        $payload_json = json_encode($payload);

        // Log the outgoing message
        $log_data = array(
            'message_type' => 'outgoing',
            'event_type' => $event_type,
            'recipient_phone' => $phone,
            'recipient_name' => $recipient_name,
            'template_name' => $template,
            'template_body' => json_encode($components),
            'template_language' => $language,
            'message_json' => $payload_json,
            'status' => 'pending',
            'provider' => 'meta',
            'sent_by' => $this->CI->customlib->getStaffID() ? $this->CI->customlib->getStaffID() : null,
        );
        $log_id = $this->CI->Whatsappmessage_model->add($log_data);

        // Make the API call
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload_json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $config->access_token,
                'Content-Type: application/json'
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'status' => 'failed',
                'error_message' => $curl_error
            ));
            return array('success' => false, 'error' => 'cURL Error: ' . $curl_error);
        }

        $response_data = json_decode($response, true);

        if ($http_code == 200 && isset($response_data['messages'][0]['id'])) {
            $message_id = $response_data['messages'][0]['id'];
            $conversation_id = isset($response_data['messages'][0]['conversation_id']) ? $response_data['messages'][0]['conversation_id'] : null;

            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'whatsapp_message_id' => $message_id,
                'whatsapp_conversation_id' => $conversation_id,
                'status' => 'sent'
            ));

            return array('success' => true, 'message_id' => $message_id, 'conversation_id' => $conversation_id);
        } else {
            $error_msg = 'Unknown error';
            if (isset($response_data['error']['message'])) {
                $error_msg = $response_data['error']['message'];
            } elseif (isset($response_data['error']['title'])) {
                $error_msg = $response_data['error']['title'];
            }

            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'status' => 'failed',
                'error_message' => $error_msg
            ));

            return array('success' => false, 'error' => $error_msg, 'response_code' => $http_code);
        }
    }

    /**
     * Send via Twilio WhatsApp API
     * Twilio WhatsApp uses the same endpoint as Twilio SMS but with whatsapp: prefix
     */
    private function sendTwilioTemplateMessage($config, $phone, $template, $components = array(), $event_type = null, $recipient_name = null, $language = 'en_US')
    {
        if (empty($config->twilio_account_sid) || empty($config->twilio_auth_token)) {
            return array('success' => false, 'error' => 'Twilio Account SID or Auth Token is missing');
        }

        if (empty($config->twilio_phone_number)) {
            return array('success' => false, 'error' => 'Twilio Phone Number is missing');
        }

        // Format phone for Twilio: must have country code, prepend whatsapp:
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) < 10) {
            return array('success' => false, 'error' => 'Invalid phone number');
        }

        $from_number = $config->twilio_phone_number;
        // Ensure from has whatsapp: prefix
        if (strpos($from_number, 'whatsapp:') !== 0) {
            $from_number = 'whatsapp:' . $from_number;
        }

        // Build the message body - for Twilio we send the template content as a text message
        // Twilio templates are handled differently (via Twilio Content API or Twilio-approved templates)
        $body_text = '[' . $template . ']';

        if (!empty($components)) {
            $body_text .= "\n" . implode(' | ', $components);
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $config->twilio_account_sid . '/Messages.json';

        $post_data = array(
            'From' => $from_number,
            'To'   => 'whatsapp:' . $phone,
            'Body' => $body_text,
        );

        $payload_json = json_encode(array(
            'from' => $from_number,
            'to' => 'whatsapp:' . $phone,
            'template' => $template,
            'components' => $components,
        ));

        // Log the outgoing message
        $log_data = array(
            'message_type' => 'outgoing',
            'event_type' => $event_type,
            'recipient_phone' => $phone,
            'recipient_name' => $recipient_name,
            'template_name' => $template,
            'template_body' => json_encode($components),
            'template_language' => $language,
            'message_json' => $payload_json,
            'status' => 'pending',
            'provider' => 'twilio',
            'sent_by' => $this->CI->customlib->getStaffID() ? $this->CI->customlib->getStaffID() : null,
        );
        $log_id = $this->CI->Whatsappmessage_model->add($log_data);

        // Make the Twilio API call
        $ch = curl_init($url);
        $encoded = http_build_query($post_data);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $encoded,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD  => $config->twilio_account_sid . ':' . $config->twilio_auth_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'status' => 'failed',
                'error_message' => $curl_error
            ));
            return array('success' => false, 'error' => 'cURL Error: ' . $curl_error);
        }

        $response_data = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300 && isset($response_data['sid'])) {
            $message_id = $response_data['sid'];
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'whatsapp_message_id' => $message_id,
                'status' => 'sent'
            ));
            return array('success' => true, 'message_id' => $message_id);
        } else {
            $error_msg = isset($response_data['message']) ? $response_data['message'] : 'HTTP ' . $http_code;
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'status' => 'failed',
                'error_message' => $error_msg
            ));
            return array('success' => false, 'error' => $error_msg, 'response_code' => $http_code);
        }
    }

    /**
     * Replace {{variables}} in template text and extract parameters for API
     *
     * @param string $template_text  Template with {{variable}} placeholders
     * @param array  $data           Associative array of variable => value
     * @return array  ['clean_text' => string, 'parameters' => array]
     */
    public function parseTemplate($template_text, $data)
    {
        $parameters = array();

        foreach ($data as $key => $value) {
            if (strpos($template_text, '{{' . $key . '}}') !== false) {
                $template_text = str_replace('{{' . $key . '}}', $value, $template_text);
                $parameters[] = $value;
            }
        }

        return array(
            'clean_text' => $template_text,
            'parameters' => $parameters
        );
    }

    // ========== EVENT-SPECIFIC SENDER METHODS ==========
    // Each method mirrors the Smart School pattern:
    // 1. Parse template variables from sender_details
    // 2. Send via WhatsApp Cloud API template

    public function sentRegisterWhatsapp($student_id, $contact, $template, $whatsapp_template_id)
    {
        $student = $this->CI->student_model->get($student_id);
        $sch_setting = $this->CI->setting_model->getSetting();

        $data = array(
            'student_name' => $this->CI->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname),
            'class' => $student['class'],
            'section' => $student['section'],
            'admission_no' => $student['admission_no'],
            'roll_no' => $student['roll_no'],
            'mobileno' => $student['mobileno'],
            'email' => $student['email'],
            'school_name' => $sch_setting->name,
        );

        // Also add session-based vars if available
        if (!empty($student['session'])) {
            $data['current_session_name'] = $student['session'];
        }

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'student_admission', $data['student_name']);
    }

    public function sentAddFeeWhatsapp($sender_details, $contact, $template, $whatsapp_template_id)
    {
        $data = array(
            'student_name' => isset($sender_details->student_name) ? $sender_details->student_name : '',
            'class' => isset($sender_details->class) ? $sender_details->class : '',
            'section' => isset($sender_details->section) ? $sender_details->section : '',
            'fee_amount' => isset($sender_details->amount) ? $sender_details->amount : '',
            'fee_group_name' => isset($sender_details->fee_group_name) ? $sender_details->fee_group_name : '',
            'invoice_id' => isset($sender_details->invoice_id) ? $sender_details->invoice_id : '',
            'due_date' => isset($sender_details->due_date) ? $sender_details->due_date : '',
            'type' => isset($sender_details->type) ? $sender_details->type : '',
            'code' => isset($sender_details->code) ? $sender_details->code : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'fee_submission', $data['student_name']);
    }

    public function sentAddGroupFeeWhatsapp($sender_details, $contact, $template, $whatsapp_template_id)
    {
        return $this->sentAddFeeWhatsapp($sender_details, $contact, $template, $whatsapp_template_id);
    }

    public function sentFeeProcessingNotification($sender_details, $contact, $template, $whatsapp_template_id)
    {
        $data = array(
            'student_name' => isset($sender_details->student_name) ? $sender_details->student_name : '',
            'class' => isset($sender_details->class) ? $sender_details->class : '',
            'section' => isset($sender_details->section) ? $sender_details->section : '',
            'fee_amount' => isset($sender_details->fee_amount) ? $sender_details->fee_amount : '',
            'transaction_id' => isset($sender_details->transaction_id) ? $sender_details->transaction_id : '',
            'email' => isset($sender_details->email) ? $sender_details->email : '',
            'contact_no' => isset($sender_details->contact_no) ? $sender_details->contact_no : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'fee_processing', $data['student_name']);
    }

    public function sentfeesreminderNotification($sender_details, $contact, $template, $whatsapp_template_id)
    {
        $data = array(
            'student_name' => isset($sender_details->student_name) ? $sender_details->student_name : '',
            'fee_type' => isset($sender_details->fee_type) ? $sender_details->fee_type : '',
            'fee_code' => isset($sender_details->fee_code) ? $sender_details->fee_code : '',
            'due_date' => isset($sender_details->due_date) ? $sender_details->due_date : '',
            'due_amount' => isset($sender_details->due_amount) ? $sender_details->due_amount : '',
            'school_name' => isset($sender_details->school_name) ? $sender_details->school_name : '',
            'fee_amount' => isset($sender_details->fee_amount) ? $sender_details->fee_amount : '',
            'deposit_amount' => isset($sender_details->deposit_amount) ? $sender_details->deposit_amount : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'fees_reminder', $data['student_name']);
    }

    public function sentExamResultWhatsapp($detail, $template, $whatsapp_template_id)
    {
        $data = array(
            'student_name' => isset($detail['student_name']) ? $detail['student_name'] : '',
            'exam_roll_no' => isset($detail['exam_roll_no']) ? $detail['exam_roll_no'] : '',
            'exam' => isset($detail['exam']) ? $detail['exam'] : '',
            'admission_no' => isset($detail['admission_no']) ? $detail['admission_no'] : '',
        );

        if (isset($detail['contact_numbers']) && !empty($detail['contact_numbers'])) {
            $results = array();
            foreach ($detail['contact_numbers'] as $contact) {
                $parsed = $this->parseTemplate($template, $data);
                $results[] = $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'exam_result', $data['student_name']);
            }
            return $results;
        }
        return array('success' => false, 'error' => 'No contact numbers');
    }

    public function sendPresentAttendancenotification($detail, $template, $whatsapp_template_id, $contact)
    {
        $data = array(
            'student_name' => isset($detail['student_name']) ? $detail['student_name'] : '',
            'admission_no' => isset($detail['admission_no']) ? $detail['admission_no'] : '',
            'date' => isset($detail['date']) ? $detail['date'] : '',
            'in_time' => isset($detail['in_time']) ? $detail['in_time'] : '',
            'subject_name' => isset($detail['subject_name']) ? $detail['subject_name'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'student_present_attendence', $data['student_name']);
    }

    public function sendAbsentAttendancenotification($detail, $template, $whatsapp_template_id, $contact)
    {
        $data = array(
            'student_name' => isset($detail['student_name']) ? $detail['student_name'] : '',
            'date' => isset($detail['date']) ? $detail['date'] : '',
            'subject_name' => isset($detail['subject_name']) ? $detail['subject_name'] : '',
            'mobileno' => isset($detail['mobileno']) ? $detail['mobileno'] : '',
            'email' => isset($detail['email']) ? $detail['email'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'student_absent_attendence', $data['student_name']);
    }

    public function sentPresentStaffWhatsapp($detail, $template, $whatsapp_template_id)
    {
        $contact = isset($detail['contact_no']) ? $detail['contact_no'] : '';
        if (empty($contact)) {
            return array('success' => false, 'error' => 'No contact number');
        }

        $data = array(
            'staff_name' => isset($detail['staff_name']) ? $detail['staff_name'] : '',
            'employee_id' => isset($detail['employee_id']) ? $detail['employee_id'] : '',
            'date' => isset($detail['date']) ? $detail['date'] : '',
            'in_time' => isset($detail['in_time']) ? $detail['in_time'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'staff_present_attendence', $data['staff_name']);
    }

    public function sentAbsentStaffWhatsapp($detail, $template, $whatsapp_template_id)
    {
        $contact = isset($detail['contact_no']) ? $detail['contact_no'] : '';
        if (empty($contact)) {
            return array('success' => false, 'error' => 'No contact number');
        }

        $data = array(
            'staff_name' => isset($detail['staff_name']) ? $detail['staff_name'] : '',
            'employee_id' => isset($detail['employee_id']) ? $detail['employee_id'] : '',
            'date' => isset($detail['date']) ? $detail['date'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'staff_absent_attendence', $data['staff_name']);
    }

    public function sendstudentlhomework($student_sms_list, $template, $whatsapp_template_id)
    {
        if (empty($student_sms_list)) {
            return array('success' => false, 'error' => 'No recipients');
        }

        $results = array();
        foreach ($student_sms_list as $contact => $detail) {
            $data = array(
                'student_name' => isset($detail['student_name']) ? $detail['student_name'] : '',
                'homework_date' => isset($detail['homework_date']) ? $detail['homework_date'] : '',
                'submit_date' => isset($detail['submit_date']) ? $detail['submit_date'] : '',
                'class' => isset($detail['class']) ? $detail['class'] : '',
                'section' => isset($detail['section']) ? $detail['section'] : '',
                'subject' => isset($detail['subject']) ? $detail['subject'] : '',
            );

            $parsed = $this->parseTemplate($template, $data);
            $results[] = $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'homework', $data['student_name']);
        }
        return $results;
    }

    public function sentOnlineexamStudentWhatsapp($student_sms_list, $template, $whatsapp_template_id)
    {
        if (empty($student_sms_list)) {
            return array('success' => false, 'error' => 'No recipients');
        }

        $results = array();
        foreach ($student_sms_list as $contact => $detail) {
            $data = array(
                'exam_title' => isset($detail['exam_title']) ? $detail['exam_title'] : '',
                'exam_from' => isset($detail['exam_from']) ? $detail['exam_from'] : '',
                'exam_to' => isset($detail['exam_to']) ? $detail['exam_to'] : '',
                'time_duration' => isset($detail['time_duration']) ? $detail['time_duration'] : '',
                'attempt' => isset($detail['attempt']) ? $detail['attempt'] : '',
                'passing_percentage' => isset($detail['passing_percentage']) ? $detail['passing_percentage'] : '',
            );

            $parsed = $this->parseTemplate($template, $data);
            $results[] = $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'online_examination_publish_exam', '');
        }
        return $results;
    }

    public function sendOnlineadmissionformsubmit($student_details, $template, $contact, $whatsapp_template_id)
    {
        $data = array(
            'firstname' => isset($student_details['firstname']) ? $student_details['firstname'] : '',
            'lastname' => isset($student_details['lastname']) ? $student_details['lastname'] : '',
            'date' => isset($student_details['date']) ? $student_details['date'] : '',
            'reference_no' => isset($student_details['reference_no']) ? $student_details['reference_no'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'online_admission_form_submission', $data['firstname'] . ' ' . $data['lastname']);
    }

    public function sentstudentOnlineadmissionFeessubmissionWhatsapp($student_details, $template, $contact, $whatsapp_template_id)
    {
        $data = array(
            'firstname' => isset($student_details['firstname']) ? $student_details['firstname'] : '',
            'lastname' => isset($student_details['lastname']) ? $student_details['lastname'] : '',
            'date' => isset($student_details['date']) ? $student_details['date'] : '',
            'paid_amount' => isset($student_details['paid_amount']) ? $student_details['paid_amount'] : '',
            'reference_no' => isset($student_details['reference_no']) ? $student_details['reference_no'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'online_admission_fees_submission', $data['firstname'] . ' ' . $data['lastname']);
    }

    public function sendStudentLoginCredential($chk_mail_sms, $sender_details, $template, $whatsapp_template_id)
    {
        $contact = isset($sender_details['contact_no']) ? $sender_details['contact_no'] : '';
        if (empty($contact)) {
            return array('success' => false, 'error' => 'No contact number');
        }

        $data = array(
            'display_name' => isset($sender_details['display_name']) ? $sender_details['display_name'] : '',
            'username' => isset($sender_details['username']) ? $sender_details['username'] : '',
            'password' => isset($sender_details['password']) ? $sender_details['password'] : '',
            'admission_no' => isset($sender_details['admission_no']) ? $sender_details['admission_no'] : '',
            'url' => base_url(),
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'student_login_credential', $data['display_name']);
    }

    public function sendStaffLoginCredential($chk_mail_sms, $sender_details, $template, $whatsapp_template_id)
    {
        $contact = isset($sender_details['contact_no']) ? $sender_details['contact_no'] : '';
        if (empty($contact)) {
            return array('success' => false, 'error' => 'No contact number');
        }

        $data = array(
            'first_name' => isset($sender_details['first_name']) ? $sender_details['first_name'] : '',
            'last_name' => isset($sender_details['last_name']) ? $sender_details['last_name'] : '',
            'username' => isset($sender_details['username']) ? $sender_details['username'] : '',
            'password' => isset($sender_details['password']) ? $sender_details['password'] : '',
            'employee_id' => isset($sender_details['employee_id']) ? $sender_details['employee_id'] : '',
            'url' => base_url(),
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'staff_login_credential', $data['first_name'] . ' ' . $data['last_name']);
    }

    public function student_apply_leave($sender_details, $template, $whatsapp_template_id)
    {
        $contact = isset($sender_details['contact_no']) ? $sender_details['contact_no'] : '';
        if (empty($contact)) {
            return array('success' => false, 'error' => 'No contact number');
        }

        $data = array(
            'student_name' => isset($sender_details['student_name']) ? $sender_details['student_name'] : '',
            'class' => isset($sender_details['class']) ? $sender_details['class'] : '',
            'section' => isset($sender_details['section']) ? $sender_details['section'] : '',
            'apply_date' => isset($sender_details['apply_date']) ? $sender_details['apply_date'] : '',
            'from_date' => isset($sender_details['from_date']) ? $sender_details['from_date'] : '',
            'to_date' => isset($sender_details['to_date']) ? $sender_details['to_date'] : '',
            'message' => isset($sender_details['message']) ? $sender_details['message'] : '',
        );

        $parsed = $this->parseTemplate($template, $data);
        return $this->sendTemplateMessage($contact, $whatsapp_template_id, $parsed['parameters'], 'student_apply_leave', $data['student_name']);
    }

    // ========== UTILITY: Send free-form text message (not template-based) ==========
    public function sendTextMessage($phone, $message, $event_type = null, $recipient_name = null)
    {
        $config = $this->getConfig();

        if (!$config) {
            return array('success' => false, 'error' => 'WhatsApp is not configured or inactive');
        }

        // Route to Twilio if active
        if ($config->_active_provider === 'twilio') {
            return $this->sendTwilioTextMessage($config, $phone, $message, $event_type, $recipient_name);
        }

        // Meta path
        if ($config->is_active != 1) {
            return array('success' => false, 'error' => 'Meta WhatsApp is not active');
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        $api_version = !empty($config->api_version) ? $config->api_version : 'v21.0';
        $url = $this->api_base_url . '/' . $api_version . '/' . $config->phone_number_id . '/messages';

        $payload = array(
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => array(
                'preview_url' => false,
                'body' => $message
            )
        );

        $payload_json = json_encode($payload);

        $log_data = array(
            'message_type' => 'outgoing',
            'event_type' => $event_type,
            'recipient_phone' => $phone,
            'recipient_name' => $recipient_name,
            'template_name' => 'text_message',
            'template_body' => $message,
            'message_json' => $payload_json,
            'status' => 'pending',
            'provider' => 'meta',
            'sent_by' => $this->CI->customlib->getStaffID() ? $this->CI->customlib->getStaffID() : null,
        );
        $log_id = $this->CI->Whatsappmessage_model->add($log_data);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload_json,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $config->access_token,
                'Content-Type: application/json'
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array('status' => 'failed', 'error_message' => $curl_error));
            return array('success' => false, 'error' => $curl_error);
        }

        $response_data = json_decode($response, true);

        if ($http_code == 200 && isset($response_data['messages'][0]['id'])) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'whatsapp_message_id' => $response_data['messages'][0]['id'],
                'status' => 'sent'
            ));
            return array('success' => true, 'message_id' => $response_data['messages'][0]['id']);
        } else {
            $error_msg = isset($response_data['error']['message']) ? $response_data['error']['message'] : 'Unknown error';
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array('status' => 'failed', 'error_message' => $error_msg));
            return array('success' => false, 'error' => $error_msg);
        }
    }

    /**
     * Send free-form text via Twilio WhatsApp
     */
    private function sendTwilioTextMessage($config, $phone, $message, $event_type = null, $recipient_name = null)
    {
        if (empty($config->twilio_account_sid) || empty($config->twilio_auth_token)) {
            return array('success' => false, 'error' => 'Twilio credentials missing');
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        $from_number = $config->twilio_phone_number;
        if (strpos($from_number, 'whatsapp:') !== 0) {
            $from_number = 'whatsapp:' . $from_number;
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $config->twilio_account_sid . '/Messages.json';

        $post_data = array(
            'From' => $from_number,
            'To'   => 'whatsapp:' . $phone,
            'Body' => $message,
        );

        $log_data = array(
            'message_type' => 'outgoing',
            'event_type' => $event_type,
            'recipient_phone' => $phone,
            'recipient_name' => $recipient_name,
            'template_name' => 'text_message',
            'template_body' => $message,
            'message_json' => json_encode($post_data),
            'status' => 'pending',
            'provider' => 'twilio',
            'sent_by' => $this->CI->customlib->getStaffID() ? $this->CI->customlib->getStaffID() : null,
        );
        $log_id = $this->CI->Whatsappmessage_model->add($log_data);

        $ch = curl_init($url);
        $encoded = http_build_query($post_data);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $encoded,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD  => $config->twilio_account_sid . ':' . $config->twilio_auth_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array('status' => 'failed', 'error_message' => $curl_error));
            return array('success' => false, 'error' => $curl_error);
        }

        $response_data = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300 && isset($response_data['sid'])) {
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array(
                'whatsapp_message_id' => $response_data['sid'],
                'status' => 'sent'
            ));
            return array('success' => true, 'message_id' => $response_data['sid']);
        } else {
            $error_msg = isset($response_data['message']) ? $response_data['message'] : 'HTTP ' . $http_code;
            $this->CI->Whatsappmessage_model->updateStatus($log_id, array('status' => 'failed', 'error_message' => $error_msg));
            return array('success' => false, 'error' => $error_msg);
        }
    }

    // ========== WEBHOOK HANDLER ==========

    /**
     * Verify webhook from Meta
     */
    public function verifyWebhook($mode, $token, $challenge)
    {
        $config = $this->CI->Whatsappconfig_model->get();
        if (!$config) {
            return false;
        }

        if ($mode === 'subscribe' && $token === $config->verify_token) {
            return $challenge;
        }

        return false;
    }

    /**
     * Process incoming webhook data
     */
    public function processWebhook($payload)
    {
        if (!isset($payload['entry'])) {
            return false;
        }

        foreach ($payload['entry'] as $entry) {
            if (!isset($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if (!isset($change['value']['messages'])) {
                    continue;
                }

                foreach ($change['value']['messages'] as $message) {
                    $phone = isset($message['from']) ? $message['from'] : '';
                    $whatsapp_msg_id = isset($message['id']) ? $message['id'] : '';

                    $log_data = array(
                        'message_type' => 'incoming',
                        'recipient_phone' => $phone,
                        'whatsapp_message_id' => $whatsapp_msg_id,
                        'message_json' => json_encode($message),
                        'status' => 'received',
                    );

                    // Check if message is a text message
                    if (isset($message['text']['body'])) {
                        $log_data['template_body'] = $message['text']['body'];
                    }

                    $this->CI->Whatsappmessage_model->add($log_data);
                }

                // Process status updates
                if (isset($change['value']['statuses'])) {
                    foreach ($change['value']['statuses'] as $status_update) {
                        $msg_id = isset($status_update['id']) ? $status_update['id'] : '';
                        $status = isset($status_update['status']) ? $status_update['status'] : '';

                        if (!empty($msg_id)) {
                            $this->CI->Whatsappmessage_model->updateByWhatsAppMessageId($msg_id, array(
                                'status' => $status
                            ));
                        }
                    }
                }
            }
        }

        return true;
    }
}
