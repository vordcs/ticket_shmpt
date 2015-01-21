<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_checkin extends CI_Model {

    public function get_vihicles_check_in($tsid = NULL, $date = NULL) {

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }

        if ($tsid != NULL) {
            $this->db->where('TSID', $tsid);
        }
        $this->db->where('DateCheckIn', $date);
        $query = $this->db->get('vehicles_check_in');

        return $query->result_array();
    }

    public function insert_checkin($data) {
        $rs = '';
        $tsid = $data['TSID'];
        $sid = $data['SID'];
        $tsid_check_in = $this->get_vihicles_check_in($tsid);
        if (count($tsid_check_in) <= 0) {
            $this->db->insert('vehicles_check_in', $data);
            $rs = "INSERT -> $tsid";
        } else {
            $data = $this->update_checkin($tsid, $sid, $data);
            $rs = "UPDATE -> $tsid";
        }

        return $rs;
    }

    public function update_checkin($tsid, $sid, $data) {
        $this->db->where('SID', $sid);
        $this->db->where('TSID', $tsid);
        $this->db->update('vehicles_check_in', $data);
        if ($this->db->affected_rows() == 1){
            return $tsid;
        }  else {
            return NULL;
        }
    }

    public function get_post_form_add($rid, $tsid, $vid, $sid) {

        $data_form_add = array(
            'SID' => $sid,
            'TSID' => $tsid,
            'RID' => $rid,
            'VID' => $vid,
            'TimeCheckIn' => $this->m_datetime->getTimeNow(),
            'DateCheckIn' => $this->m_datetime->getDateToday(),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
            'CreateBy' => $this->m_user->get_user_id(),
        );

        return $data_form_add;
    }

    public function get_post_form_edit($tsid, $sid) {

        $data_form_edit = array(
            'TSID' => $tsid,
            'SID' => $sid,
            'TimeCheckIn' => $this->m_datetime->getTimeNow(),
            'DateCheckIn' => $this->m_datetime->getDateToday(),
            'UpdateDate' => $this->m_datetime->getDatetimeNow(),
            'UpdateBy' => $this->m_user->get_user_id(),
        );

        return $data_form_edit;
    }

    /*
     * for view on check in  only
     */

    //    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($date = NULL, $rcode = NULL, $vtid = NULL, $rid = NULL, $tsid = NULL) {
        $this->db->select('*,t_schedules_day.RID as RID,t_schedules_day.TSID as TSID,vehicles.VID as VID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');
        $this->db->join('vehicles_check_in', ' vehicles_check_in.TSID = t_schedules_day.TSID and vehicles_check_in.DateCheckIn = t_schedules_day.Date', 'left');

        if ($date != NULL) {
            $this->db->where('Date', $date);
        }
        if ($rcode != NULL) {
            $this->db->where('t_routes.RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        if ($rid != NULL) {
            $this->db->where('t_schedules_day.RID', $rid);
        }
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day.TSID', $tsid);
        }
        $this->db->where('t_schedules_day.ScheduleStatus', '1');
        
        $this->db->order_by('t_schedules_day.TimeDepart', 'asc');
        $query_schedule = $this->db->get("t_schedules_day");
        return $query_schedule->result_array();
    }

    public function get_route($rcode = NULL, $vtid = NULL, $rid = NULL) {

        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');

        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        } else {
            $this->db->where('StartPoint', 'S');
        }
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        $this->db->group_by(array('RCode', 't_routes.VTID'));

//        $this->db->order_by('StartPoint');
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    function get_vehicle_types($id = NULL) {
        if ($id != NULL)
            $this->db->where('VTID', $id);
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
    }

    public function get_station_sale_ticket($rcode = null, $vtid = null, $sid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }

        $this->db->where('IsSaleTicket', 1);

        $this->db->order_by('Seq', 'asc');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

}
