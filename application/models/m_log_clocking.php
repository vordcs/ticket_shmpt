<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_log_clocking extends CI_Model {

    function check_log_clocking_today($EID) {
        $this->db->from("employee_log_clocking");
        $this->db->where("EID", $EID);
        $this->db->like('clock_in_date', $this->m_datetime->getDateToday());
        $query = $this->db->get();
        return $query->result_array();
    }

    function insert_clock_in($EID) {
        $data = array(
            'EID' => $EID,
            'clock_in_date' => $this->m_datetime->getDatetimeNow(),
        );
        if ($this->db->insert('employee_log_clocking', $data))
            return TRUE;
        else
            return FALSE;
    }

    function insert_clock_out($EID) {
        $date = $this->m_datetime->getDatetimeNow();
        $data = array(
            'EID' => $EID,
            'clock_in_date' => $date,
            'clock_out_date' => $date,
        );
        if ($this->db->insert('employee_log_clocking', $data))
            return TRUE;
        else
            return FALSE;
    }

    function update_clock_out($EID) {
        $data = array(
            'clock_out_date' => $this->m_datetime->getDatetimeNow(),
        );
        $this->db->where('EID', $EID);
        $this->db->update('employee_log_clocking', $data);
        if ($this->db->affected_rows() == 1)
            return TRUE;
        else
            return FALSE;
    }

}
