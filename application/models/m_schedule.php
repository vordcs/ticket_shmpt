<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class m_schedule extends CI_Model {

//    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($date = NULL, $rid = NULL, $tsid = NULL) {
        $this->db->join('vehicles_has_schedules', 't_schedules_day.TSID = vehicles_has_schedules.TSID', 'left');
//        $this->db->join('vehicles', 'vehicles.VID = vehicles_has_schedules.VID', 'left');
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day.TSID', $tsid);
        }
        if ($date != NULL) {
            $this->db->where('Date', $date);
        }
        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        }
        $this->db->where('ScheduleStatus', 1);
        $query_schedule = $this->db->get("t_schedules_day");
        return $query_schedule->result_array();
    }

    public function check_schedule($date, $rid, $time_depart) {

        $this->db->where('RID', $rid);
        $this->db->where('Date', $date);
        $this->db->where('TimeDepart', $time_depart);
        $query_schedule = $this->db->get("t_schedules_day");

        return $query_schedule->result_array();
    }

}
