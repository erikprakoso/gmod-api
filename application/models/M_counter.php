<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_counter extends CI_Model
{

    /**
     * Simpan Data Siswa
     */
    public function insert($data)
    {
        $simpan = $this->db->insert("counter", $data);

        if ($simpan) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_by_code($code)
    {
        $row = $this->db->get_where('counter', array('code' => $code))->row();
        return $row;
    }
}
