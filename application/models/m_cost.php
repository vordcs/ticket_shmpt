<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_cost extends CI_Model {

    public function get_cost($cid = null, $ctid = NULL, $date = NULL, $tsid = NULL, $vid = NULL) {
        $this->db->select('*,cost.CreateBy AS CreateBy,cost.CreateDate as CreateDate');
        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID', 'left');
        $this->db->join('vehicles_has_cost', 'vehicles_has_cost.CostID = cost.CostID', 'left');
        $this->db->join('vehicles', 'vehicles.VID = vehicles_has_cost.VID', 'left');
        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');
        $this->db->join('t_stations', 't_stations.SID = cost.SID', 'left');
        if ($cid != NULL) {
            $this->db->where('cost.CostID', $cid);
        }
        if ($ctid != NULL) {
            $this->db->where('cost.CostTypeID', $ctid);
        }
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day_has_cost.TSID', $tsid);
        }
        if ($vid != NULL) {
            $this->db->where('vehicles_has_cost.VID', $vid);
        }
        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }

        $this->db->where('cost.CostDate', $date);
//        $this->db->where('cost.CreateBy', $this->m_user->get_user_id());
        $query = $this->db->get('cost');
        return $query->result_array();
    }

    public function get_cost_type($id = NULL) {

        if ($id != NULL) {
            $this->db->where('CostTypeID', $id);
        }
        $query = $this->db->get('cost_type');
        return $query->result_array();
    }

    public function get_cost_detail($id = NULL) {
        if ($id != NULL) {
            $this->db->where('CostTypeID', $id);
        }
        $query = $this->db->get('cost_detail');
        return $query->result_array();
    }

    public function get_vehicle($vcode = NULL, $vtid = NULL, $rcode = NULL) {
        $this->db->join('t_routes_has_vehicles', 'vehicles.VID = t_routes_has_vehicles.VID', 'left');
        $this->db->join('vehicles_type', 'vehicles.VTID = vehicles_type.VTID');
        if ($vcode != NULL) {
            $this->db->where('vehicles.VCode', $vcode);
        }
        if ($vtid != NULL) {

            $this->db->where('vehicles_type.VTID', $vtid);
        }
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }

        $query = $this->db->get('vehicles');
        return $query->result_array();
    }

    public function get_vehicle_types($id = NULL) {
        if ($id != NULL)
            $this->db->where('VTID', $id);
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
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
        }
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function search_cost($form = NULL, $to = NULL) {

        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID');
        $this->db->join('vehicles_has_cost', 'vehicles_has_cost.CostID = cost.CostID');
        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');

        if ($form != NULL && $to == NULL) {
            $this->db->where('cost.CostDate', $this->m_datetime->setDateFomat($form));
        }
        if ($form != NULL && $to != NULL) {
            $this->db->where('cost.CostDate >=', $this->m_datetime->setDateFomat($form));
            $this->db->where('cost.CostDate <=', $this->m_datetime->setDateFomat($to));
        }
        $query = $this->db->get('cost');
        return $query->result_array();
    }

    public function insert_cost($data) {

//        $this->db->truncate('cost');
//        $this->db->truncate('t_schedules_day_has_cost');
//        $this->db->truncate('vehicles_has_cost');
//      insert cost data  
        $this->db->insert('cost', $data['data_cost']);
        $cost_id = $this->db->insert_id();

//      insert schedule has cost 
        $schedule_has_cost = array(
            'TSID' => $data['TSID'],
            'CostID' => $cost_id
        );
        $this->db->insert('t_schedules_day_has_cost', $schedule_has_cost);

//      insert vehicles has cost
        $vehicle_has_cost = array(
            'VID' => $data['VID'],
            'CostID' => $cost_id,
        );

        $this->db->insert('vehicles_has_cost', $vehicle_has_cost);

        $rs = $this->get_cost($cost_id);

        return $rs;
    }

    public function update_cost($cid, $data) {
        $this->db->where('CostID', $cid);
        $this->db->update('cost', $data['data_cost']);
        if ($this->db->affected_rows() == 1) {
            $cost = $this->get_cost($cid)[0];
            return $cost;
        } else {
            return NULL;
        }
    }

    public function delete_cost($cost_id) {
        //delete vehicle has cost
        $this->db->where('CostID', $cost_id);
        $this->db->delete('vehicles_has_cost');

        //delete schedules_day has cost
        $this->db->where('CostID', $cost_id);
        $this->db->delete('t_schedules_day_has_cost');

        //delete cost
        $this->db->where('CostID', $cost_id);
        $this->db->delete('cost');

        return TRUE;
    }

    public function set_form_add($ctid, $tsid = NULL, $time_depart = NULL) {
        $date_th = $this->m_datetime->DateThaiToDay();

        $date = $this->m_datetime->getDateToday();

        $schedule = $this->get_schedule($date, $tsid)[0];

        $rid = $schedule['RID'];
        $rcode = $schedule['RCode'];
        $vtid = $schedule['VTID'];

        $vid = $schedule['VID'];
        $vcode = $schedule['VCode'];

        $route = $this->get_route($rcode, $vtid)[0];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $desination;

        /*
         * ข้อมูลจุดจอดของพนักงานขายตั๋ว
         */
        $seller_station = $this->m_user->get_saller_station($rcode, $vtid, $rid)[0];
        $SID = $seller_station['SID'];
        $i_RouteName = array(
            'type' => 'text',
            'name' => 'RouteName',
            'value' => $route_name,
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_RCode = array(
            'type' => 'hidden',
            'name' => 'RCode',
            'value' => $rcode,
            'class' => 'form-control',
        );
        $i_SID = array(
            'type' => 'hidden',
            'name' => 'SID',
            'id' => 'SID',
            'value' => $SID,
            'class' => 'form-control',
        );

        $i_CostDetailID[0] = 'เลือกรายการ';
        foreach ($this->get_cost_detail($ctid) as $value) {
            $i_CostDetailID[$value['CostDetailID']] = $value['CostDetail'];
        }

        $i_OtherDetail = array(
            'name' => 'OtherDetail',
            'id' => 'OtherDetail',
            'value' => set_value('OtherDetail'),
            'placeholder' => 'รายการอื่นๆ',
            'class' => 'form-control'
        );

        $i_DateTH = array(
            'type' => 'text',
            'name' => 'Date',
            'value' => $date_th,
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_TSID = array(
            'type' => 'hidden',
            'name' => 'TSID',
            'value' => $tsid,
            'class' => 'form-control'
        );
        $i_TimeDepart = array(
            'type' => 'text',
            'name' => 'TimeDepart',
            'value' => $time_depart,
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_CostDate = array(
            'type' => 'hidden',
            'name' => 'CostDate',
            'value' => (set_value('CostDate') == NULL) ? $this->m_datetime->getDateTodayTH() : set_value('CostDate'),
            'placeholder' => 'วันที่ทำรายการ',
            'class' => 'form-control datepicker');

        $i_VID = array(
            'type' => 'hidden',
            'name' => 'VID',
            'value' => $vid,
            'placeholder' => 'รหัสรถ',
            'class' => 'form-control'
        );

        $i_VCode = array(
            'name' => 'VCode',
            'value' => $vcode,
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_CostValue = array(
            'name' => 'CostValue',
            'value' => set_value('CostValue'),
            'placeholder' => 'จำนวนเงิน',
            'class' => 'form-control');
        $i_CostNote = array(
            'name' => 'CostNote',
            'value' => set_value('CostNote'),
            'placeholder' => 'หมายเหตุ',
            'rows' => '3',
            'class' => 'form-control');
        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form_add = array(
            'form' => form_open("cost/add/$ctid/$tsid/", array('class' => 'form-horizontal', 'id' => 'form_cost', 'name' => 'form_cost')),
            'TSID' => form_input($i_TSID),
            'TimeDepart' => form_input($i_TimeDepart),
            'RouteName' => form_input($i_RouteName),
            'SID' => form_input($i_SID),
            'RCode' => form_input($i_RCode),
            'DateTH' => form_input($i_DateTH),
            'CostDate' => form_input($i_CostDate),
            'CostDetailID' => form_dropdown('CostDetailID', $i_CostDetailID, set_value('CostDetailID'), $dropdown . 'id = "CostDetailID" '),
            'OtherDetail' => form_input($i_OtherDetail),
            'VID' => form_input($i_VID),
            'VCode' => form_input($i_VCode),
            'CostValue' => form_input($i_CostValue),
            'CostNote' => form_textarea($i_CostNote),
        );
        return $form_add;
    }

    public function set_form_edit($route, $cost) {
        $date_th = $this->m_datetime->DateThaiToDay();

        $date = $this->m_datetime->getDateToday();

        /*
         * ข้อมูลเส้นทาง
         */

        $RID = $route['RID'];
        $RCode = $route['RCode'];
        $VTID = $route['VTID'];
        $StartPoint = $route['StartPoint'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name เส้นทาง " . $RCode . ' ' . ' ' . $source . ' - ' . $desination;

        /*
         * ข้อมูลค่าใช้จ่าย
         */

        $CostID = $cost['CostID'];
        $CostTypeID = $cost['CostTypeID'];
        $CostDetailID = $cost['CostDetailID'];
        $CostValue = $cost['CostValue'];
        $OtherCostDetail = $cost['OtherCostDetail'];
        $CostNote = $cost['CostNote'];
        $date = $cost['CostDate'];

        /*
         * ข้อมูลรถ
         */

        $VID = $cost['VID'];
        $VCode = $cost['VCode'];

        /*
         * ข้อมูลจุดจอด
         */
        $SID = $cost['SID'];

        /*
         * ข้อมูลเวลาเดินรถ
         */
        $TSID = $cost['TSID'];
        $schedule = $this->get_schedule($date, $TSID)[0];

        /*
         * ข้อมูลเวลามาถึงสถานี
         */
        $stations_in_route = array();
        $stations = $this->get_stations($RCode, $VTID);
        $num_station = count($stations);
        if ($StartPoint == "S") {
            $n = 0;
            foreach ($stations as $station) {
                $stations_in_route[$n] = $station;
                $n++;
            }
        }
        if ($StartPoint == "D") {
            $n = 0;
            for ($i = $num_station; $i >= 0; $i--) {
                foreach ($stations as $station) {
                    if ($station['Seq'] == $i) {
                        $stations_in_route[$n] = $station;
                        $n++;
                    }
                }
            }
        }
        $start_time = $schedule['TimeDepart'];
        $TimeDepart = '';
        $temp = 0;
        foreach ($stations_in_route as $s) {
            if ($s['IsSaleTicket'] == '1') {
                $travel_time = $s['TravelTime'];
                if ($s['Seq'] == '1' || $s['Seq'] == $num_station) {
                    $time = strtotime($start_time);
                } else {
                    $temp+=$travel_time;
                    $time = strtotime("+$temp minutes", strtotime($start_time));
                }
                if ($SID == $s['SID']) {
                    $TimeDepart = date('H:i', $time);
                    break;
                }
            }
        }
        $i_RouteName = array(
            'type' => 'text',
            'name' => 'RouteName',
            'value' => (set_value('RouteName') == NULL) ? $route_name : set_value('RouteName'),
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_RCode = array(
            'type' => 'hidden',
            'name' => 'RCode',
            'value' => (set_value('RCode') == NULL) ? $RCode : set_value('RCode'),
            'class' => 'form-control',
        );

        $i_CostDetailID[0] = 'เลือกรายการ';
        foreach ($this->get_cost_detail($CostTypeID) as $value) {
            $i_CostDetailID[$value['CostDetailID']] = $value['CostDetail'];
        }

        $i_OtherCostDetail = array(
            'name' => 'OtherCostDetail',
            'id' => 'OtherCostDetail',
            'value' => (set_value('OtherCostDetail') == NULL) ? $OtherCostDetail : set_value('OtherCostDetail'),
            'placeholder' => 'รายการอื่นๆ',
            'class' => 'form-control'
        );

        $i_DateTH = array(
            'type' => 'text',
            'name' => 'Date',
            'value' => $date_th,
            'class' => 'form-control',
            'readonly' => '',
        );
        $i_TSID = array(
            'type' => 'hidden',
            'name' => 'TSID',
            'value' => $TSID,
            'class' => 'form-control'
        );
        $i_SID = array(
            'type' => 'hidden',
            'name' => 'SID',
            'value' => $SID,
            'class' => 'form-control',
        );
        $i_TimeDepart = array(
            'type' => 'text',
            'name' => 'TimeDepart',
            'value' => (set_value('TimeDepart') == NULL) ? $TimeDepart : set_value('TimeDepart'),
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_CostDate = array(
            'type' => 'hidden',
            'name' => 'CostDate',
            'value' => (set_value('CostDate') == NULL) ? $date : set_value('CostDate'),
            'placeholder' => 'วันที่ทำรายการ',
            'class' => 'form-control datepicker');

        $i_VID = array(
            'type' => 'hidden',
            'name' => 'VID',
            'value' => (set_value('VID') == NULL) ? $VID : set_value('VID'),
            'placeholder' => 'รหัสรถ',
            'class' => 'form-control'
        );

        $i_VCode = array(
            'name' => 'VCode',
            'value' => (set_value('VCode') == NULL) ? $VCode : set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control',
            'readonly' => '',
        );

        $i_CostValue = array(
            'name' => 'CostValue',
            'value' => (set_value('CostValue') == NULL) ? $CostValue : set_value('CostValue'),
            'placeholder' => 'จำนวนเงิน',
            'class' => 'form-control');
        $i_CostNote = array(
            'name' => 'CostNote',
            'value' => (set_value('CostNote') == NULL) ? $CostNote : set_value('CostNote'),
            'placeholder' => 'หมายเหตุ',
            'rows' => '3',
            'class' => 'form-control');
        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form_add = array(
            'form' => form_open("cost/edit/$CostTypeID/$CostID/$RID/$TSID", array('class' => 'form-horizontal', 'id' => 'form_cost', 'name' => 'form_cost')),
            'TSID' => form_input($i_TSID),
            'TimeDepart' => form_input($i_TimeDepart),
            'RouteName' => form_input($i_RouteName),
            'SID' => form_input($i_SID),
            'RCode' => form_input($i_RCode),
            'DateTH' => form_input($i_DateTH),
            'CostDate' => form_input($i_CostDate),
            'CostDetailID' => form_dropdown('CostDetailID', $i_CostDetailID, (set_value('CostDetailID') == NULL) ? $CostDetailID : set_value('CostDetailID'), $dropdown . 'id = "CostDetailID" '),
            'OtherDetail' => form_input($i_OtherCostDetail),
            'VID' => form_input($i_VID),
            'VCode' => form_input($i_VCode),
            'CostValue' => form_input($i_CostValue),
            'CostNote' => form_textarea($i_CostNote),
        );
        return $form_add;
    }

    public function set_form_search($rcode = NULL, $vtid = NULL) {
        //ข้อมูลเส้นทาง
        $i_RCode[0] = 'เส้นทางทั้งหมด';
        foreach ($this->get_route() as $value) {
            $i_RCode[$value['RCode']] = $value['RCode'] . ' ' . $value['RSource'] . ' - ' . $value['RDestination'];
        }

        $i_VTID[0] = 'ประเภทรถทั้งหมด';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }

        $i_VCode = array(
            'name' => 'VCode',
            'value' => set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control');

        $i_DateForm = array(
            'name' => 'DateForm',
            'value' => set_value('DateForm'),
            'placeholder' => 'วันที่',
            'class' => 'form-control datepicker');

        $i_DateTo = array(
            'name' => 'DateTo',
            'value' => set_value('DateTo'),
            'placeholder' => 'ถึงวันที่',
            'class' => 'form-control datepicker');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';

        $v = '';
        if ($rcode != NULL && $vtid != NULL) {
            $v = "view/$rcode/$vtid";
        }

        $form_search = array(
            'form' => form_open("cost/$v", array('role=' => 'form', 'id' => 'form_search_cost')),
            'RCode' => form_dropdown('RCode', $i_RCode, set_value('RCode'), $dropdown),
            'VTID' => form_dropdown('VTID', $i_VTID, set_value('VTID'), 'class="selecter_3" '),
            'VCode' => form_input($i_VCode),
            'DateForm' => form_input($i_DateForm),
            'DateTo' => form_input($i_DateTo),
        );

        return $form_search;
    }

    public function validation_form_add() {

        $CostDetailID = $this->input->post('CostDetailID');
        if ($CostDetailID == '999') {
            $this->form_validation->set_rules('OtherDetail', 'รายการอื่นๆ', 'trim|required|xss_clean');
        } else {
            $this->form_validation->set_rules('OtherDetail', 'รายการอื่นๆ', 'trim|xss_clean');
        }

        $this->form_validation->set_rules('RCode', 'เส้นทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('CostDetailID', 'รายการ', 'trim|required|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('CostDate', 'วันที่ทำรายการ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VCode', 'เบอร์รถ', 'trim|required|xss_clean|callback_check_vcode');
        $this->form_validation->set_rules('CostValue', 'จำนวนเงิน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('CostNote', 'หมายเหตุ', 'trim|xss_clean');
        return TRUE;
    }

    public function validation_form_edit() {
        $this->form_validation->set_rules('RCode', 'เส้นทาง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('CostDetailID', 'รายการ', 'trim|required|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('CostDate', 'วันที่ทำรายการ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VCode', 'เบอร์รถ', 'trim|required|xss_clean|callback_check_vcode');
        $this->form_validation->set_rules('CostValue', 'จำนวนเงิน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('CostNote', 'หมายเหตุ', 'trim|xss_clean');
        return TRUE;
    }

    public function varlidation_form_search() {
        $this->form_validation->set_rules('RCode', 'เส้นทาง', 'trim|xss_clean');
        $this->form_validation->set_rules('VTID', 'ประเภทรถ', 'trim|xss_clean');
        $this->form_validation->set_rules('VCode', 'เบอร์รถ', 'trim|xss_clean');
        $this->form_validation->set_rules('DateForm', 'จากวันที่', 'trim|xss_clean');
        $this->form_validation->set_rules('DateTo', 'ถึงวันที่', 'trim|xss_clean');

        return TRUE;
    }

    public function get_post_form_add($ctid) {
        //ข้อมูลค่าใช้จ่าย        
        $data_cost = array(
            'CostTypeID' => $ctid,
            'CostDetailID' => $this->input->post('CostDetailID'),
            'CostDate' => $this->m_datetime->setDateFomat($this->input->post('CostDate')),
            'CostValue' => $this->input->post('CostValue'),
            'CostNote' => $this->input->post('CostNote'),
            'SID' => $this->input->post('SID'),
            'CreateBy' => $this->session->userdata('EID'),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );
        $OtherCost = $this->input->post('OtherDetail');
        if ($OtherCost != '' || $OtherCost != null) {
            $data_cost['OtherCostDetail'] = $OtherCost;
        }

        $form_data = array(
            'data_cost' => $data_cost,
            'TSID' => $this->input->post('TSID'),
            'VID' => $this->input->post('VID'),
        );

        return $form_data;
    }

    public function get_post_form_edit($cost_type_id) {
        //ข้อมูลค่าใช้จ่าย        
        $data_cost = array(
            'CostTypeID' => $cost_type_id,
            'CostDetailID' => $this->input->post('CostDetailID'),
            'CostDate' => $this->m_datetime->setDateFomat($this->input->post('CostDate')),
            'CostValue' => $this->input->post('CostValue'),
            'CostNote' => $this->input->post('CostNote'),
            'SID' => $this->input->post('SID'),
            'UpdateBy' => $this->session->userdata('EID'),
            'UpdateDate' => $this->m_datetime->getDatetimeNow(),
        );
        $OtherCost = $this->input->post('OtherDetail');
        if ($OtherCost != '' || $OtherCost != null) {
            $data_cost['OtherCostDetail'] = $OtherCost;
        }

        $form_data = array(
            'data_cost' => $data_cost,
        );

        return $form_data;
    }

    //    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($date = NULL, $tsid = NULL) {
        $this->db->select('*,t_schedules_day.RID as RID,t_schedules_day.TSID as TSID,vehicles.VID as VID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');

        if ($date != NULL) {
            $this->db->where('Date', $date);
        }
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day.TSID', $tsid);
        }
        $this->db->order_by('t_schedules_day.TimeDepart', 'asc');
        $query_schedule = $this->db->get("t_schedules_day");

        return $query_schedule->result_array();
    }

    //คืนค่าข้อมูลจุดจอด
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
        $this->db->order_by('Seq', 'asc');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

}
