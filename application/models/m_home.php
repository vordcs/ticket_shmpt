<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class m_home extends CI_Model {

    public function get_user_id() {
        $eid = $this->session->userdata('EID');
        return $eid;
    }

    public function get_seller_detail($EID) {
        $this->db->from('sellers AS se');
        $this->db->join('employees AS em', ' em.EID = se.EID ', 'left');
        $this->db->join('employee_positions AS ep', ' ep.PID = em.PID ', 'left');

        $this->db->where('se.EID', $EID);
        $query_schedule = $this->db->get();
        return $query_schedule->result_array();
    }

    public function set_validation() {
        $this->form_validation->set_rules('old_pass', 'Old pass', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', 'New pass', 'trim|required|xss_clean');
        return TRUE;
    }

    public function check_pass($user, $old_pass) {
        $where = array(
            'UserName' => $user,
            'Password' => md5($old_pass)
        );
        $query_schedule = $this->db->get_where('username', $where);
        return $query_schedule->result_array();
    }

    function update_user($EID, $PASS) {
        $data = array(
            'Password' => md5($PASS)
        );
        $this->db->where('UserName', $EID);
        $this->db->update('username', $data);
        if ($this->db->affected_rows() == 1)
            return TRUE;
        else
            return FALSE;
    }

    public function get_timeline($date = NULL, $sid = NULL) {
        $this->db->from('report_day AS rd');
        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        $this->db->where('rd.ReportDate', $date);
        $this->db->order_by('rd.ReportTime', 'asc');

        $query_schedule = $this->db->get();
        $ans = $query_schedule->result_array();

        if ($sid != NULL) {
            for ($i = 0; $i < count($ans); $i++) {
                $temp_sid = $ans[$i]['SID'];
                if (in_array($temp_sid, $sid) == FALSE) {
                    unset($ans[$i]);
                    $i--;
                }
            }
        }

        //Make detail
        for ($i = 0; $i < count($ans); $i++) {
            $this->db->from('t_schedules_day_has_report AS tsdhr');
            $this->db->join('t_schedules_day AS tsd', ' tsd.TSID = tsdhr.TSID ', 'left');
            $this->db->where('tsdhr.ReportID', $ans[$i]['ReportID']);
            $this->db->order_by('tsd.TimeDepart', 'asc');
            $query_temp = $this->db->get();
            $ans[$i]['detail'] = $query_temp->result_array();
        }

        return $ans;
    }

}
