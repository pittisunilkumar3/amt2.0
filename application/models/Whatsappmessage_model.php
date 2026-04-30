<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsappmessage_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->insert('whatsapp_messages', $data);
        return $this->db->insert_id();
    }

    public function get($id = null)
    {
        $this->db->select()->from('whatsapp_messages');
        if ($id != null) {
            $this->db->where('id', $id);
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        }
        return $query->result();
    }

    public function getByEvent($event_type, $limit = 50, $offset = 0)
    {
        $this->db->select()->from('whatsapp_messages');
        $this->db->where('event_type', $event_type);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    public function getRecent($limit = 50, $offset = 0)
    {
        $this->db->select()->from('whatsapp_messages');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    public function getByWhatsAppMessageId($whatsapp_message_id)
    {
        $this->db->select()->from('whatsapp_messages');
        $this->db->where('whatsapp_message_id', $whatsapp_message_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function updateStatus($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('whatsapp_messages', $data);
    }

    public function updateByWhatsAppMessageId($whatsapp_message_id, $data)
    {
        $this->db->where('whatsapp_message_id', $whatsapp_message_id);
        $this->db->update('whatsapp_messages', $data);
    }

    public function countByStatus($status = null)
    {
        $this->db->from('whatsapp_messages');
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->count_all_results();
    }

    public function countToday()
    {
        $this->db->from('whatsapp_messages');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        return $this->db->count_all_results();
    }
}
