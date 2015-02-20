<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class m_schedule_hr extends CI_Model {

    public function get_schedule($date = NULL, $rcode = NULL, $vtid = NULL, $rid = NULL) {
        $this->db->select('*,t_schedules_day.RID as RID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');

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

        $this->db->where('t_schedules_day.ScheduleStatus', '1');

        $this->db->order_by('TimeDepart,SeqNo', 'asc');
        $query_schedule = $this->db->get("t_schedules_day");
        return $query_schedule->result_array();
    }

    public function insert_schedule($data) {
        $rs = array();
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $tsid = $data[$i]['TSID'];
                $rid = $data[$i]['RID'];
                $date = $data[$i]['Date'];
                $time_depart = $data[$i]['TimeDepart'];

                if ($this->IsExitSchedule($date, $rid, $time_depart)) {
                    $rs[$i] = "Date $date UPDATE -> TSID = " . $tsid;
                } else {
                    $this->db->insert('t_schedules_day', $data[$i]);
                    $rs[$i] = "Date $date INSERT -> TSID = " . $tsid;
                }
            }
        }
        return $rs;
    }

    public function IsExitSchedule($date, $rid, $time_depart) {

        $this->db->where('RID', $rid);
        $this->db->where('Date', $date);
        $this->db->where('TimeDepart', $time_depart);
        $query = $this->db->get("t_schedules_day");

        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
//        $rs = $query_schedule->num_row();
//        return $rs;
    }

    public function get_vehicle($rcode = NULL, $vtid = NULL) {
        $this->db->join('t_routes_has_vehicles', 'vehicles.VID = t_routes_has_vehicles.VID', 'left');
        if ($rcode != NULL) {
            $this->db->where('t_routes_has_vehicles.RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('vehicles.VTID', $vtid);
        }
        $query = $this->db->get('vehicles');
        return $query->result_array();
    }

    public function get_stations($rcode = null, $vtid = null, $sid = NULL, $seq = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }
        if ($seq != NULL) {
            $this->db->where('Seq', $seq);
        }
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_schedule_master($rid = NULL, $smid = NULL) {
        $this->db->join('t_routes_has_schedules_manual', 't_routes_has_schedules_manual.SMID = t_schedules_manual.SMID', 'left');
        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        }
        $this->db->order_by('SeqNo');
        $query = $this->db->get('t_schedules_manual');

        return $query->result_array();
    }

    public function get_route($rcode = NULL, $vtid = NULL, $rid = NULL) {

        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');

        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }

        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        } else {
            $this->db->group_by(array('RCode', 't_routes.VTID'));
            $this->db->where('StartPoint', 'S');
        }

        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function get_route_detail($rcode = NULL, $vtid = NULL, $RID = NULL) {
        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        if ($RID != NULL) {
            $this->db->where('t_routes.RID', $RID);
        }
        $this->db->order_by('StartPoint', 'DESC');
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function set_form_search($rcode = NULL, $vtid = NULL) {
        $i_RCode = array(
            'name' => 'RCode',
            'value' => set_value('RID'),
            'placeholder' => 'รหัสเส้นทาง',
            'class' => 'form-control');

        $i_VTID[0] = 'เลือกประเภทรถ';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }
        $i_RSource = array(
            'name' => 'RSource',
            'value' => set_value('RSource'),
            'placeholder' => 'ต้นทาง',
            'class' => 'form-control');

        $i_RDestination = array(
            'name' => 'RDestination',
            'value' => set_value('RDestination'),
            'placeholder' => 'ปลายทาง',
            'class' => 'form-control');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';



        $form_search_route = array(
            'form' => form_open('schedule/', array('id' => 'form_search_schedule')),
            'RCode' => form_input($i_RCode),
            'VTID' => form_dropdown('VTID', $i_VTID, set_value('VTID'), $dropdown),
            'RSource' => form_input($i_RSource),
            'RDestination' => form_input($i_RDestination),
        );
        return $form_search_route;
    }

    public function set_form_add($rcode, $vtid, $rid) {

        $route_detail = $this->m_route->get_route($rcode, $vtid, $rid);

        $vt_name = $route_detail[0]['VTDescription'];
        $source = $route_detail[0]['RSource'];
        $desination = $route_detail[0]['RDestination'];
        $route_name = $vt_name . ' เส้นทาง ' . $route_detail[0]['RCode'] . ' ' . ' ' . $source . ' - ' . $desination;

        $vid = '';
        $vcode = '';


        $i_route_name = array(
            'type' => 'text',
            'name' => 'route_name',
            'value' => $route_name,
            'placeholder' => 'ชื่อเส้นทาง',
            'class' => 'form-control text-center'
        );

        $i_date_thai = array(
            'type' => 'type',
            'name' => 'date_thai',
            'value' => $this->m_datetime->getDateThaiString(),
            'placeholder' => 'วันที่',
            'class' => 'form-control text-center'
        );

        $i_RID = array(
            'type' => 'hidden',
            'name' => 'RID',
            'value' => $rid,
            'placeholder' => 'รหัสเส้นทาง',
            'class' => 'form-control'
        );

        $i_VID = array(
            'type' => 'hidden',
            'name' => 'VID',
            'value' => $vid,
            'placeholder' => 'รหัสรถ',
            'class' => 'form-control'
        );
        $i_VCode = array(
            'type' => 'text',
            'name' => 'VCode',
            'value' => $vcode,
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control'
        );
        $i_Date = array(
            'type' => 'hidden',
            'name' => 'Date',
            'value' => $this->m_datetime->getDateToday(),
            'placeholder' => 'วันที่',
            'class' => 'form-control'
        );

        $i_ScheduleNote = array(
            'type' => 'text',
            'name' => 'ScheduleNote',
            'rows' => '3',
            'placeholder' => 'หมายเหตุ',
            'class' => 'form-control'
        );

        $time = 10 * 60; //10 minutes
        $time_start = ceil((time() + $time) / 300) * 300;
        $time_end = '23:50';
        $temp_time = date('H:i', strtotime("+0 minutes", $time_start));
        $temp = 0;
        $i_TimeDepart[0] = "เลือกเวลา";
        while($temp_time<$time_end){
            $t = date('H:i', strtotime("+$temp minutes", $time_start));
            $i_TimeDepart[$t] = $t;
            $temp +=5;
            $temp_time = $t;
        }
        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form_search_add = array(
            'form' => form_open("schedule/add/$rcode/$vtid/$rid", array('id' => 'form_add_schedule')),
            'route_name' => form_input($i_route_name),
            'RID' => form_input($i_RID),
            'VID' => form_input($i_VID),
            'VCode' => form_input($i_VCode),
            'TimeDepart' => form_dropdown('TimeDepart', $i_TimeDepart, set_value('TimeDepart'), $dropdown),
            'date_thai' => form_input($i_date_thai),
            'Date' => form_input($i_Date),
            'ScheduleNote' => form_textarea($i_ScheduleNote),
        );

        return $form_search_add;
    }

    public function get_post_form_search() {
        $form_data = array(
            'RCode' => $this->input->post('RCode'),
            'VTID' => $this->input->post('VTID'),
            'RSource' => $this->input->post('RSource'),
            'RDestination' => $this->input->post('RDestination'),
        );
        return $form_data;
    }

    public function get_post_form_add() {
        $rid = $this->input->post('RID');
        $time_depart = $this->input->post('TimeDepart');
        $date = $this->input->post('Date');
        //ข้อมูลเส้นทาง
        $route = $this->get_route(NULL, NULL, $rid)[0];
        $rcode = $route['RCode'];


        //สร้างรหัสตารางเวลา
        $tsid = $this->generate_tsid($date, $rid);

        //ค้นหารถ
//        $this->get_stations($rcode, $vtid);
        $str = 'ไม่พบ';
        if ($this->IsExitSchedule($date, $rid, $time_depart)) {
            $str = 'มีอยู่จริง';
        }

        $form_data = array(
            'TSID' => $tsid,
            'RID' => $rid,
            'RCode' => $rcode,
            'TimeDepart' => $time_depart,
            'Date' => $date,
            'ScheduleNote' => $this->input->post('ScheduleNote'),
            'srt' => $str,
        );
        return $form_data;
    }

    public function validation_form_search() {
        $this->form_validation->set_rules('RCode', 'รหัสเส้นทาง', 'trim|xss_clean');
        $this->form_validation->set_rules('VTID', 'ประเภทรถ', 'trim|xss_clean');
        $this->form_validation->set_rules('RSource', 'ต้นทาง', 'trim|xss_clean');
        $this->form_validation->set_rules('RDestination', 'ปลายทาง', 'trim|xss_clean');
        return TRUE;
    }

    public function validation_form_add() {
        $this->form_validation->set_rules('RID', 'รหัสเส้นทาง', 'trim|xss_clean');
        $this->form_validation->set_rules('TimeDepart', 'เวลา', 'trim|xss_clean|callback_check_schedule|callback_check_dropdown');
        return TRUE;
    }

    public function validation_form_change() {
        $this->form_validation->set_rules('VID', 'รหัสรถ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ScheduleNote', 'โน๊ต', 'trim|xss_clean');
        return TRUE;
    }

    public function get_vehicle_types($id = NULL) {
        if ($id != NULL) {
            $this->db->where('VTID', $id);
        }
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
    }

    public function generate_tsid($date, $rid) {
        $tsid = '';
        $query = $this->db->get_where('t_schedules_day', array('RID' => $rid, 'Date' => $date));
        $seq = $query->num_rows() + 1;
//        วันที่
        $d = new DateTime();
        $tsid .=$d->format("Ymd");
//        เส้นทาง
        $tsid .= str_pad($rid, 2, '0', STR_PAD_LEFT);
//        เที่ยวที่
        $tsid .=str_pad($seq, 3, '0', STR_PAD_LEFT);

        return $tsid;
    }

    public function get_vehicle_current_stations($rcode, $vtid, $station_id = NULL, $seq = NULL) {
        $this->db->join('vehicles', 'vehicles_current_stations.VID = vehicles.VID ', 'right');
        $this->db->join('t_routes_has_vehicles', 'vehicles.VID = t_routes_has_vehicles.VID', 'right');

        $this->db->where('t_routes_has_vehicles.RCode', $rcode);
        $this->db->where('vehicles.VTID', $vtid);

        if ($station_id != NULL) {
            $this->db->where('vehicles_current_stations.CurrentStationID', $station_id);
        }

        if ($seq != NULL) {
            $this->db->where('vehicles_current_stations.CurrentStatonSeq', $seq);
        }

        $this->db->order_by('CurrentTime,CurrentDate,vehicles.VID', 'asc');
        $query = $this->db->get('vehicles_current_stations');

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
        return FALSE;
    }

    public function shift_schedule($rid = NULL, $time = NULL) {
        $rs = array();
        $date = $this->m_datetime->getDateToday();
        $schedules = $this->get_schedule($date, NULL, NULL, $rid);
    }

    public function insert_new_t_schedules_day($data) {
        $data['CreateDate'] = $this->m_datetime->getDatetimeNow();
        if ($this->db->insert('t_schedules_day', $data))
            return $data['TSID'];
        else
            return FALSE;
    }

    public function get_schedule_to_sort($RID, $DATE) {
        $this->db->select('*,tsd.TSID as TSID,tsd.RID as RID,vhs.VID as VID');
        $this->db->from('t_schedules_day as tsd');
        $this->db->join('vehicles_has_schedules as vhs', ' vhs.TSID = tsd.TSID', 'left');
        $this->db->where('tsd.RID', $RID);
        $this->db->where('tsd.Date', $DATE);
        $this->db->where('tsd.ScheduleStatus', '1');
        $this->db->order_by('tsd.TimeDepart', 'asc');
        $query_schedule = $this->db->get();
        return $query_schedule->result_array();
    }

    /*
     * ตรวจสอบรถคันต่อไปที่จะสามารถนำมาเพิ่มเข้าไปในคิวได้
     */

    public function get_next_vehicle($RCode, $VTID, $RID) {
        $StartPoint = $this->get_route_detail($RCode, $VTID, $RID)[0]['StartPoint'];
        if ($StartPoint == 'D') {
            $temp = end($this->get_stations($RCode, $VTID));
            $SID = $temp['SID'];
        } else {
            $temp = $this->get_stations($RCode, $VTID)[0];
            $SID = $temp['SID'];
        }
        $this->db->from('vehicles_current_stations as vcs');
        $this->db->join('t_routes_has_vehicles as trhv', ' trhv.VID = vcs.VID', 'left');
        $this->db->join('vehicles as vh', ' vh.VID = vcs.VID', 'left');
        $this->db->where('trhv.RCode', $RCode);
        $this->db->where('vh.VTID', $VTID);
        $this->db->where('vcs.CurrentStationID', $SID);
        $this->db->order_by('vcs.CurrentTime', 'asc');
        $query_schedule = $this->db->get();
        $temp2 = $query_schedule->result_array();

        return $temp2;
    }

    public function get_list_destination_vehicle($RCode, $VTID, $RID) {
        $StartPoint = $this->get_route_detail($RCode, $VTID, $RID)[0]['StartPoint'];
        if ($StartPoint == 'S') {
            $temp = end($this->get_stations($RCode, $VTID));
            $SID = $temp['SID'];
        } else {
            $temp = $this->get_stations($RCode, $VTID)[0];
            $SID = $temp['SID'];
        }
        $this->db->from('vehicles_current_stations as vcs');
        $this->db->join('t_routes_has_vehicles as trhv', ' trhv.VID = vcs.VID', 'left');
        $this->db->join('vehicles as vh', ' vh.VID = vcs.VID', 'left');
        $this->db->where('trhv.RCode', $RCode);
        $this->db->where('vh.VTID', $VTID);
        $this->db->where('vcs.CurrentStationID', $SID);
        $this->db->order_by('vcs.CurrentTime', 'asc');
        $query_schedule = $this->db->get();
        $temp2 = $query_schedule->result_array();

        return $temp2;
    }

    /*
     * อัพเดทตำแหน่งของรถ
     */

    public function update_vehicle_curent_stations($vid, $next_station_id = NULL, $time_arrive) {
        if ($next_station_id == NULL) {
            $data_update = array(
                'CurrentTime' => $time_arrive,
                'CurrentDate' => $this->m_datetime->getDateToday(),
                'UpdateDate' => $this->m_datetime->getDatetimeNow(),
            );
        } else {
            $data_update = array(
                'CurrentTime' => $time_arrive,
                'CurrentDate' => $this->m_datetime->getDateToday(),
                'CurrentStationID' => $next_station_id,
                'UpdateDate' => $this->m_datetime->getDatetimeNow(),
            );
        }

        $this->db->where('VID', $vid);
        $this->db->update('vehicles_current_stations', $data_update);
        if ($this->db->affected_rows() == 1)
            return TRUE;
        else
            return FALSE;
    }

    function update_vehicles_has_schedules_with_new_data($data) {
        $pre_data = array();
        $count = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['VID'] != NULL) {
                $pre_data[$i]['VID'] = $data[$i]['VID'];
                $pre_data[$i]['TSID'] = $data[$i]['TSID'];
                $query = $this->db->get_where('vehicles_has_schedules', array('TSID' => $data[$i]['TSID']));
                if ($query->num_rows() == 0) {
                    if ($this->db->insert('vehicles_has_schedules', $pre_data[$i]))
                        $count++;
                } else {
                    $this->db->where('TSID', $data[$i]['TSID']);
                    if ($this->db->update('vehicles_has_schedules', $pre_data[$i]))
                        $count++;
                }
            }
        }
        if (count($pre_data) == $count)
            return TRUE;
        else
            return FALSE;
    }

    function update_t_schedules_day_status($TSID, $RID, $ScheduleStatus = 2) {
        $pre_data['ScheduleStatus'] = $ScheduleStatus;
        $this->db->where('TSID', $TSID);
        $this->db->where('RID', $RID);
        if ($this->db->update('t_schedules_day', $pre_data)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function delete_vehicles_has_schedules($TSID, $VID) {
        if ($this->db->delete('vehicles_has_schedules', array('TSID' => $TSID, 'VID' => $VID))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

//    function get_first_vehicles_current_stations($CurrentStationID) {
//        $this->db->from('vehicles_current_stations');
//        $this->db->where('CurrentStationID', $CurrentStationID);
//        $this->db->order_by('CurrentTime', 'asc');
//        $query_schedule = $this->db->get();
//        $temp = $query_schedule->result_array();
//        return $temp[0];
//    }

    function get_route_detail_by_TSID($TSID) {
        $this->db->from('t_schedules_day as tsd');
        $this->db->join('t_routes as tr', ' tr.RID = tsd.RID', 'left');
        $this->db->join('vehicles_type as vt', ' vt.VTID = tr.VTID', 'left');
        $this->db->join('vehicles_has_schedules as vhs', ' vhs.TSID = tsd.TSID', 'left');
        $this->db->join('vehicles as ve', ' ve.VID = vhs.VID', 'left');


        $this->db->where('tsd.TSID', $TSID);
        $query_schedule = $this->db->get();
        $temp = $query_schedule->result_array();
        return $temp;
    }

    public function set_form_change($rcode, $vtid, $vid = "") {
        $temp_vehicle = $this->m_schedule_hr->get_vehicle($rcode, $vtid);
        $option = array();
        foreach ($temp_vehicle as $row) {
            if ($row['VID'] != $vid)
                $option[$row['VID']] = $row['VCode'];
        }
        $i_ScheduleNote = array(
            'type' => 'text',
            'name' => 'ScheduleNote',
            'rows' => '3',
            'placeholder' => 'หมายเหตุ',
            'class' => 'form-control',
            'value' => set_value('ScheduleNote'),
        );

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form = array(
            'VID' => form_dropdown('VID', $option, set_value('VID'), $dropdown),
            'ScheduleNote' => form_textarea($i_ScheduleNote),
        );
        return $form;
    }

    public function update_change_vehicle($data) {
        $flag_vhs = FALSE;
        $pre_vhs = array(
            'VID' => $data['VID']
        );
        $this->db->where('TSID', $data['TSID']);
        if ($this->db->update('vehicles_has_schedules', $pre_vhs))
            $flag_vhs = TRUE;

        $flag_tsd = FALSE;
        $pre_tsd = array(
            'ScheduleNote' => $data['ScheduleNote']
        );
        $this->db->where('TSID', $data['TSID']);
        if ($this->db->update('t_schedules_day', $pre_tsd))
            $flag_tsd = TRUE;

        if ($flag_vhs && $flag_tsd)
            return TRUE;
        else
            return FALSE;
    }

}
