<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_route_hr extends CI_Model {

    public function insert_route($data) {


//        go to destination ,กำหนด StartPoint เป็น S 
        $dataD = array(
            'RCode' => $data['RCode'],
            'StartPoint' => 'S',
            'VTID' => $data['VTID'],
            'RSource' => $data['RSource'],
            'RDestination' => $data['RDestination'],
            'Time' => $data['Time'],
            'ScheduleType' => $schedule_type,
            'CreateDate' => $this->m_datetime->getDatetimeNowTH(),
        );
        $this->db->insert('t_routes', $dataD);

//        go to source ,กำหนด StartPoint เป็น D
        $dataS = array(
            'RCode' => $data['RCode'],
            'StartPoint' => 'D',
            'VTID' => $data['VTID'],
            'RSource' => $data['RDestination'],
            'RDestination' => $data['RSource'],
            'Time' => $data['Time'],
            'CreateDate' => $this->m_datetime->getDatetimeNowTH(),
        );
        $this->db->insert('t_routes', $dataS);

        return $data['RCode'];
    }

    public function update_route($rcode, $vtid, $data) {

        $rid = $this->get_route_detail($rcode, $vtid);

//        go to destination ,กำหนด StartPoint เป็น S 
        $dataD = array(
            'RCode' => $data['RCode'],
            'StartPoint' => 'S',
            'VTID' => $data['VTID'],
            'RSource' => $data['RSource'],
            'RDestination' => $data['RDestination'],
            'Time' => $data['Time'],
            'UpdateDate' => $this->m_datetime->getDatetimeNowTH(),
        );
        $this->db->where('RID', $rid[0]['RID']);
        $this->db->update('t_routes', $dataD);

//        go to source ,กำหนด StartPoint เป็น D
        $dataS = array(
            'RCode' => $data['RCode'],
            'StartPoint' => 'D',
            'VTID' => $data['VTID'],
            'RSource' => $data['RDestination'],
            'RDestination' => $data['RSource'],
            'Time' => $data['Time'],
            'UpdateDate' => $this->m_datetime->getDatetimeNowTH(),
        );
        $this->db->where('RID', $rid[1]['RID']);
        $this->db->update('t_routes', $dataS);

        return $data['RCode'];
    }

    public function update_route_time($data) {
        $schedule_type = "0";
        if ($this->input->post('ScheduleType') != NULL) {
            $schedule_type = $this->input->post('ScheduleType');
        }
        $rid_s = $data['rid'][0]['RID'];
        $rid_d = $data['rid'][1]['RID'];
        //update schedule type StartPoint S
        $this->db->where('RID', $rid_s);
        $this->db->update("t_routes", array('ScheduleType' => $schedule_type));
        //update schedule type StartPoint D
        $this->db->where('RID', $rid_d);
        $this->db->update("t_routes", array('ScheduleType' => $schedule_type));
        $rs = array();
        if ($schedule_type == "1") {
//            Start Point S
            if (count($data['timeS']) != count($this->is_exits_schedule_manual($rid_s))) {
                $k = 0;
                foreach ($this->is_exits_schedule_manual($rid_s) as $value) {
                    $this->db->where("SMID", $value["SMID"]);
                    $this->db->delete("t_schedules_manual");
                    $rs['Del_S'][$k] = "DELETE ->  " . $value["SMID"];
                    $k++;
                }
                $this->db->where('RID', $rid_s);
                $this->db->delete("t_routes_has_schedules_manual");
            }
            $j = 0;
            foreach ($data['timeS'] as $ts) {
                $schedule = $this->is_exits_schedule_manual($rid_s, $ts['SeqNo']);
                if (count($schedule) <= 0) {
                    $this->db->insert('t_schedules_manual', $ts);
                    $smid_s = $this->db->insert_id();

                    $route_has_schdule = array(
                        'RID' => $rid_s,
                        'SMID' => $smid_s
                    );
                    $this->db->insert('t_routes_has_schedules_manual', $route_has_schdule);
                    $rs['timeS'][$j] = "INSERT -> $smid_s ";
                } else {
                    $this->db->where('SMID', $schedule[0]['SMID']);
                    $this->db->update('t_schedules_manual', $ts);

                    $rs['timeS'][$j] = $ts;
                }
                $j++;
            }
//            Start Point D
            if (count($data['timeD']) != count($this->is_exits_schedule_manual($rid_d))) {
                $k = 0;
                foreach ($this->is_exits_schedule_manual($rid_d) as $value) {
                    $this->db->where("SMID", $value["SMID"]);
                    $this->db->delete("t_schedules_manual");
                    $rs['Del_D'][$k] = "DELETE ->  " . $value["SMID"];
                    $k++;
                }
                $this->db->where('RID', $rid_d);
                $this->db->delete("t_routes_has_schedules_manual");
            }
            $j = 0;
            foreach ($data['timeD'] as $td) {
                $schedule = $this->is_exits_schedule_manual($rid_d, $td['SeqNo']);
                if (count($schedule) <= 0) {
                    $this->db->insert('t_schedules_manual', $td);
                    $smid_d = $this->db->insert_id();

                    $route_has_schdule_d = array(
                        'RID' => $rid_d,
                        'SMID' => $smid_d
                    );
                    $this->db->insert('t_routes_has_schedules_manual', $route_has_schdule_d);
                    $rs['timeD'][$j] = "INSERT D -> $smid_d";
                } else {
                    $this->db->where('SMID', $schedule[0]['SMID']);
                    $this->db->update('t_schedules_manual', $td);

                    $rs['timeD'][$j] = $td;
                }
                $j++;
            }
            return $rs;
        } else {
            for ($i = 0; $i < count($data['rid']); $i++) {
                $this->db->where('RID', $data['rid'][$i]['RID']);
                $this->db->update('t_routes', $data['route_time'][$i]);
            }
            return $data['route_time'];
        }
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
            $this->db->where('t_routes.RID', $rid);
        } else {
            $this->db->group_by(array('RCode', 't_routes.VTID'));
            $this->db->where('StartPoint', 'S');
        }
        $this->db->order_by('StartPoint', 'desc');

        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function search_route($rcode = NULL, $source = NULL, $destination = NULL) {

        if ($rcode != NULL)
            $this->db->where('RCode', $rcode);
        if ($source != NULL)
            $this->db->where('RSource', $source);
        if ($destination != NULL)
            $this->db->where('RDestination', $destination);
        $this->db->where('StartPoint', 'S');
//        $this->db->group_by(array('RCode', 't_routes.VTID'));
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function get_route_detail($rcode = NULL, $vtid = NULL, $rid = NULL) {
        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        if ($rid != NULL) {
            $this->db->where('t_routes.RID', $rid);
        }

        $this->db->order_by('StartPoint', 'DESC');
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function get_vehicle_types($id = NULL) {
        if ($id != NULL) {
            $this->db->where('VTID', $id);
        }
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
    }

    public function get_vehicle_type_name($type_id) {
        $name = '';
        $query = $this->db->get_where('vehicles_type', array('VTID' => $type_id));
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $name = $row->VTDescription;
        }
        return $name;
    }

    public function get_schedule_manual($rid = NULL, $smid = NULL) {
        $this->db->join('t_routes_has_schedules_manual', 't_routes_has_schedules_manual.SMID = t_schedules_manual.SMID', 'left');
        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        }
        $this->db->order_by('SeqNo');
        $query = $this->db->get('t_schedules_manual');

        return $query->result_array();
    }

    function set_form_search_route() {
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
            'form' => form_open('route/', array('id' => 'form_search_route')),
            'RCode' => form_input($i_RCode),
            'VTID' => form_dropdown('VTID', $i_VTID, set_value('VTID'), $dropdown),
            'RSource' => form_input($i_RSource),
            'RDestination' => form_input($i_RDestination),
        );
        return $form_search_route;
    }

    function set_form_add_route($type_id) {
        $i_RCode = array(
            'name' => 'RCode',
            'value' => set_value('RCode'),
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

        $i_Time = array(
            'name' => 'Time',
            'value' => set_value('Time'),
            'placeholder' => 'เวลาที่ใช้',
            'class' => 'form-control');

        $dropdown = 'class="selecter_3" disabled="disabled" data-selecter-options = \'{"cover":"true"}\' ';

        $form_add_route = array(
            'form' => form_open('route/add/' . $type_id, array('class' => 'form-horizontal', 'id' => 'form_route')),
            'RCode' => form_input($i_RCode),
            'VTID' => form_dropdown('VTID', $i_VTID, (set_value('VTID') == NULL) ? $type_id : set_value('VTID'), $dropdown),
            'RSource' => form_input($i_RSource),
            'RDestination' => form_input($i_RDestination),
            'Time' => form_input($i_Time),
        );
        return $form_add_route;
    }

    function set_form_edit_route($data) {
        $i_RCode = array(
            'name' => 'RCode',
            'value' => (set_value('RCode') == NULL) ? $data ['RCode'] : set_value('RCode'),
            'placeholder' => 'รหัสเส้นทาง',
            'class' => 'form-control');

        $i_VTID[0] = 'เลือกประเภทรถ';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }
        $i_RSource = array(
            'name' => 'RSource',
            'value' => (set_value('RSource') == NULL) ? $data ['RSource'] : set_value('RSource'),
            'placeholder' => 'ต้นทาง',
            'class' => 'form-control');

        $i_RDestination = array(
            'name' => 'RDestination',
            'value' => (set_value('RDestination') == NULL) ? $data ['RDestination'] : set_value('RDestination'),
            'placeholder' => 'ปลายทาง',
            'class' => 'form-control');

        $i_Time = array(
            'name' => 'Time',
            'value' => (set_value('Time') == NULL) ? $data ['Time'] : set_value('Time'),
            'placeholder' => 'เวลาที่ใช้',
            'class' => 'form-control');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';

        $form_edit_route = array(
            'form' => form_open('route/edit/' . $data['RCode'] . '/' . $data['VTID'], array('class' => 'form-horizontal', 'id' => 'form_route')),
            'RCode' => form_input($i_RCode),
            'VTID' => form_dropdown('VTID', $i_VTID, (set_value('VTID') == NULL) ? $data['VTID'] : set_value('VTID'), $dropdown),
            'RSource' => form_input($i_RSource),
            'RDestination' => form_input($i_RDestination),
            'Time' => form_input($i_Time),
        );
        return $form_edit_route;
    }

    public function set_form_route_time($data) {
        $rcode = $data[0]['RCode'];
        $vtid = $data[0]['VTID'];
        $schedule_type = $data[0]['ScheduleType'];
        $arr_start_time = array();
        $arr_interval = array();
        $arr_around = array();
        $rid_s = $data[0]['RID'];
        $rid_d = $data[1]['RID'];

        $check = FALSE;
        if ($schedule_type == "1" || $this->input->post('ScheduleType') == '1') {
            $check = TRUE;
        }
        $i_ScheduleType = array(
            'name' => 'ScheduleType',
            'id' => 'ScheduleType',
            'value' => '1',
            'checked' => $check,
        );
        //schedule auto -> ScheduleType = 0
        for ($i = 0; $i < count($data); $i++) {
            $start_point = $data[$i]['StartPoint'];
            $i_StartTime = array(
                'id' => 'StartTime' . $start_point,
                'name' => "StartTime[]",
                'class' => 'form-control timepicker',
                'value' => (set_value("StartTime[$i]") == NULL) ? $data[$i] ["StartTime"] : set_value("StartTime[$i]"),
            );
            $i_IntervalEachAround = array(
                'id' => "IntervalEachAround" . $start_point,
                'name' => "IntervalEachAround[]",
                'type' => "number",
                'class' => "form-control",
                'min' => "0",
                'step' => "5",
                'value' => (set_value("IntervalEachAround[$i]") == NULL) ? $data[$i] ["IntervalEachAround"] : set_value("IntervalEachAround[$i]"),
            );

            $i_AroundNumber = array(
                'id' => "AroundNumber" . $start_point,
                'name' => "AroundNumber[]",
                'type' => "number",
                'class' => "form-control",
                'min' => "1",
                'step' => "1",
                'value' => (set_value("AroundNumber[$i]") == NULL) ? $data[$i] ["AroundNumber"] : set_value("AroundNumber[$i]"),
            );

            $arr_start_time[$i] = $i_StartTime;
            $arr_interval[$i] = $i_IntervalEachAround;
            $arr_around[$i] = $i_AroundNumber;
        }

        //schedule manual -> ScheduleType = 1
        $i_time_s = array();
        $time_s = array();
        if ($this->input->post('timeS') != NULL) {
            $time_s = $this->input->post('timeS');
        } else {
            $time_s = $this->get_schedule_manual($rid_s);
        }

        if (count($time_s) > 0) {
            for ($i = 0; $i < count($time_s); $i++) {
                if ($this->input->post('timeS') != NULL) {
                    $t = strtotime($time_s[$i]);
                } else {
                    $t = strtotime($time_s[$i]['Time']);
                }
                $i_time = array(
                    'id' => 'time',
                    'name' => "timeS[]",
                    'class' => 'form-control text-center',
                    'readonly' => TRUE,
                    'value' => (set_value("timeS[$i]") == NULL) ? date('H:i', $t) : set_value("timeS[$i]"),
                );
                array_push($i_time_s, form_input($i_time));
            }
        }
        $i_time_d = array();
        $time_d = array();

        if (($this->input->post('timeD') != NULL)) {
            $time_d = $this->input->post('timeD');
        } else {
            $time_d = $this->get_schedule_manual($rid_d);
        }
        if (count($time_s) > 0) {
            for ($i = 0; $i < count($time_d); $i++) {
                if (($this->input->post('timeD') != NULL)) {
                    $t = strtotime($time_d[$i]);
                } else {
                    $t = strtotime($time_d[$i]['Time']);
                }
                $i_time = array(
                    'id' => 'time',
                    'name' => "timeD[]",
                    'class' => 'form-control text-center',
                    'readonly' => TRUE,
                    'value' => (set_value("timeD[$i]") == NULL) ? date('H:i', $t) : set_value("timeD[$i]"),
                );
                array_push($i_time_d, form_input($i_time));
            }
        }

        $form_route_time = array(
            'form' => form_open('route/time/' . $rcode . '/' . $vtid, array('class' => 'form-horizontal', 'id' => 'form_route_time')),
            'rcode' => $rcode,
            'ScheduleType' => form_checkbox($i_ScheduleType),
            'StartTime' => $arr_start_time,
            'IntervalEachAround' => $arr_interval,
            'AroundNumber' => $arr_around,
            'timeS' => $i_time_s,
            'timeD' => $i_time_d,
        );

        return $form_route_time;
    }

    public function validation_form_add_route() {
        $this->form_validation->set_rules('RCode', 'รหัสเส้นทาง', 'trim|required|xss_clean|callback_check_route_duplication');
        $this->form_validation->set_rules('VTID', 'ประเภทรถ', 'trim|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('RSource', 'ต้นทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('RDestination', 'ปลายทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('Time', 'เวลา', 'trim|required|xss_clean');
        return TRUE;
    }

    public function validation_form_edit_route() {
        $this->form_validation->set_rules('RCode', 'รหัสเส้นทาง', 'trim|required|xss_clean|callback_check_route');
        $this->form_validation->set_rules('VTID', 'ประเภทรถ', 'trim|xss_clean|callback_check_dropdown|callback_check_route');
        $this->form_validation->set_rules('RSource', 'ต้นทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('RDestination', 'ปลายทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('Time', 'เวลา', 'trim|required|xss_clean');
        return TRUE;
    }

    public function validation_form_route_time() {
        $start_time = $this->input->post('StartTime');
        $interval = $this->input->post('IntervalEachAround');
        $around = $this->input->post('AroundNumber');
        $schedule_type = $this->input->post('ScheduleType');

        if ($schedule_type == '1') {
            $this->form_validation->set_rules("timeS[]", "เวลา S", 'trim|required|xss_clean');
            $this->form_validation->set_rules("timeD[]", "เวลา D", 'trim|required|xss_clean');
        } else {
            if (!empty($start_time)) {
                for ($i = 0; $i < count($start_time); $i++) {
                    $this->form_validation->set_rules("StartTime[$i]", "เวลาเที่ยวเเรก", 'trim|required|xss_clean');
                }
            }
            if (!empty($interval)) {
                for ($i = 0; $i < count($interval); $i++) {
                    $this->form_validation->set_rules("IntervalEachAround[$i]", "เวลาห่างแต่ละเที่ยว", 'trim|required|numeric|xss_clean');
                }
            }
            if (!empty($around)) {
                for ($i = 0; $i < count($around); $i++) {
                    $this->form_validation->set_rules("AroundNumber[$i]", "จำนวนเที่ยวต่อวัน", 'trim|required|numeric|xss_clean');
                }
            }
        }
        return TRUE;
    }

    public function get_post_form_add_route($type_id) {
        $form_data = array(
            'RCode' => $this->input->post('RCode'),
            'VTID' => $type_id,
            'RSource' => $this->input->post('RSource'),
            'RDestination' => $this->input->post('RDestination'),
            'Time' => $this->input->post('Time'),
        );

        return $form_data;
    }

    public function get_post_form_edit_route() {
        $form_data = array(
            'RCode' => $this->input->post('RCode'),
            'VTID' => $this->input->post('VTID'),
            'RSource' => $this->input->post('RSource'),
            'RDestination' => $this->input->post('RDestination'),
            'Time' => $this->input->post('Time'),
        );

        return $form_data;
    }

    public function get_post_form_route_time($data) {
        $start_time = $this->input->post('StartTime');
        $interval = $this->input->post('IntervalEachAround');
        $around = $this->input->post('AroundNumber');
        $schedule_type = $this->input->post('ScheduleType');
        $rs = array();
        if ($schedule_type != 0) {
            for ($i = 0; $i < count($data); $i++) {
                $rs['rid'][$i] = array(
                    'RID' => $data[$i]['RID']
                );
                $source = $data[$i]['RSource'];
                $desination = $data[$i]['RDestination'];
                $vt_name = $data[$i]['VTDescription'];
                $route_name = $vt_name . ' สาย ' . $data[$i]['RCode'] . ' ' . ' ' . $source . ' - ' . $desination;

                $time_s = $this->input->post('timeS');
                for ($j = 0; $j < count($time_s); $j++) {
                    $n = $j + 1;
                    $rs['timeS'][$j] = array(
                        'SeqNo' => $n,
                        'Time' => $time_s[$j],
                        'SMNote' => "$route_name เที่ยวที่ $n",
                    );
                }
                $time_d = $this->input->post('timeD');
                for ($j = 0; $j < count($time_d); $j++) {
                    $n = $j + 1;
                    $rs['timeD'][$j] = array(
                        'SeqNo' => $n,
                        'Time' => $time_d[$j],
                        'SMNote' => "$route_name เที่ยวที่ $n",
                    );
                }
            }
        } else {
            for ($i = 0; $i < count($data); $i++) {
                $rs['rid'][$i] = array(
                    'RID' => $data[$i]['RID']);

                $rs['route_time'][$i] = array(
                    'StartTime' => $start_time[$i],
                    'IntervalEachAround' => $interval[$i],
                    'AroundNumber' => $around[$i],
                    'UpdateDate' => $this->m_datetime->getDatetimeNow(),
                );
            }
        }
        return $rs;
    }

    public function is_exits_schedule_manual($rid, $seqno = NULL, $time = NULL, $smid = NULL) {
        $this->db->join('t_routes_has_schedules_manual', 't_routes_has_schedules_manual.SMID = t_schedules_manual.SMID');
        $this->db->where('RID', $rid);
        if ($seqno != NULL) {
            $this->db->where('SeqNo', $seqno);
        }
        if ($time != NULL) {
            $this->db->where('Time', $time);
        }
        $query = $this->db->get('t_schedules_manual');

        return $query->result_array();
    }

}
