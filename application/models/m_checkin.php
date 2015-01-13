<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_checkin extends CI_Model {

    public function set_form_add($rcode = NULL, $vtid = NULL) {

        //ข้อมูลประเภทรถ
        $i_VTID[0] = 'ทั้งหมด';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }

        //ข้อมูลเส้นทาง
        $i_RCode[0] = 'เลือกเส้นทาง';
        foreach ($this->get_route() as $value) {
            $i_RCode[$value['RCode']] = $value['VTDescription'] . '  ' . $value['RCode'] . ' ' . $value['RSource'] . ' - ' . $value['RDestination'];
        }

        //เบอร์รถ
        $i_VCode = array(
            'name' => 'VCode',
            'value' => set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control'
        );

        //จุดขายตั๋ว
//        $i_SID = array(
//            'name' => 'SID',
//            'value' => set_value('SID'),
//            'placeholder' => 'จุดขายตั๋ว',
//            'class' => 'form-control',
//            'readonly' => '',
//        );
        $i_SID[0] = 'เลือกเส้นทาง';
        if ($rcode != NULL) {
            foreach ($this->get_station_sale_ticket($rcode, $vtid) as $sale_point) {
                $i_SID[$sale_point['SID']] = $sale_point['StationName'];
            }
        }

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form_add = array(
            'form' => form_open("checkin/add/$vtid", array('id' => 'form_check_in', 'class' => 'form-horizontal')),
            'VTID' => form_dropdown('VTID', $i_VTID, $vtid, $dropdown),
            'RCode' => form_dropdown('RCode', $i_RCode, (set_value('RCode') == NULL) ? 0 : set_value('RCode'), $dropdown),
            'VCode' => form_input($i_VCode),
//            'SID' => form_input($i_SID),
            'SID' => form_dropdown('SID', $i_SID, set_value('SID'), $dropdown),
        );

        return $form_add;
    }

    public function validation_form_add() {
        $this->form_validation->set_rules('RCode', 'เส้นทาง', 'trim|required|xss_clean|callback_check_dropdown');
        return TRUE;
    }

    public function validation_form_edit() {
        
    }

    public function validation_form_search() {
        
    }

    public function get_post_form_add() {
        $vtid = $this->input->post('VTID');
        $rcode = $this->input->post('RCode');

        $data_form_add = array(
            'RCode' => $rcode,
            'VTID' => $vtid,
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
            'CreateBy' => $this->m_user->get_user_id(),
        );

        return $data_form_add;
    }

    /*
     * for view on check in  only
     */

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
