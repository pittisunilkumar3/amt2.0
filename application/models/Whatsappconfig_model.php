<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsappconfig_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->db->select()->from('whatsapp_config');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function add($data)
    {
        $this->db->select()->from('whatsapp_config');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $result = $q->row();
            $this->db->where('id', $result->id);
            $this->db->update('whatsapp_config', $data);
            return $result->id;
        } else {
            $this->db->insert('whatsapp_config', $data);
            return $this->db->insert_id();
        }
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('whatsapp_config', $data);
        return $this->db->affected_rows();
    }

    public function isActive()
    {
        $this->db->select('is_active')->from('whatsapp_config');
        $this->db->where('is_active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
}
