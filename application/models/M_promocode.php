<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_promocode extends CI_Model
{

    /**
     * Simpan Data Siswa
     */
    public function insert($data)
    {
        $simpan = $this->db->insert("promocode", $data);

        if ($simpan) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_by_code($code)
    {
        $query = $this->db->get_where('promocode', array('code' => $code));
        $row = $query->row();
        $totalRows = $query->num_rows();

        $result = array(
            'row' => $row,
            'totalRows' => $totalRows
        );

        return $result;
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('promocode', $data);
    }
}
