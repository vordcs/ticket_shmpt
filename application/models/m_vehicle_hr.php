<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_vehicle_hr extends CI_Model {

    function get_vehicle($vid = NULL) {
        $this->db->select('*,vehicles.VID as VID');
        $this->db->from('vehicles');
        $this->db->join('vehicles_type', 'vehicles.VTID = vehicles_type.VTID');
        $this->db->join('vehicles_registration', 'vehicles.RegID =vehicles_registration.RegID');
        $this->db->join('vehicles_insurance_act', 'vehicles.ActID = vehicles_insurance_act.ActID');
        $this->db->join('t_routes_has_vehicles', 'vehicles.VID = t_routes_has_vehicles.VID');
        $this->db->join('vehicles_driver', 'vehicles_driver.VID = vehicles.VID', 'left');
        $this->db->join('employees', 'employees.EID = vehicles_driver.EID', 'left');

        if ($vid != NULL) {
            $this->db->where('vehicles.VID', $vid);
        }
        $this->db->order_by('vehicles.VID', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function search_vehicle() {
        $vcode = $this->input->post('VCode');
        $number_plate = $this->input->post('NumberPlate');

        if ($vcode != NULL) {
            $this->db->where('Vcode', $vcode);
        }

        if ($number_plate != NULL) {
            $this->db->where('NumberPlate', $number_plate);
        }

        $this->db->join('vehicles_type', 'vehicles.VTID = vehicles_type.VTID');
        $this->db->join('vehicles_registration', 'vehicles.RegID = vehicles_registration.RegID');
        $this->db->join('vehicles_insurance_act', 'vehicles.ActID = vehicles_insurance_act.ActID');
        $this->db->join('t_routes_has_vehicles', 'vehicles.VID = t_routes_has_vehicles.VID');
        $this->db->join('vehicles_driver', 'vehicles_driver.VID = vehicles.VID', 'left');
        $this->db->join('employees', 'employees.EID = vehicles_driver.EID', 'left');
        $query = $this->db->get('vehicles');
        return $query->result_array();
    }

    public function get_route($rcode = NULL, $vtid = NULL) {

        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');

        if ($rcode != NULL)
            $this->db->where('RCode', $rcode);
        if ($vtid != NULL)
            $this->db->where('t_routes.VTID', $vtid);

//        $this->db->where('StartPoint', 'S');
        $this->db->group_by(array('RCode', 't_routes.VTID'));
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    function get_vehicle_types($id = NULL) {
        if ($id != NULL)
            $this->db->where('VTID', $id);
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
    }

    function get_policy_type($id = NULL) {
        $this->db->where('MiscName', 'PolicyType');
        $query = $this->db->get('miscellaneous');
        return $query->result_array();
    }

    function insert_vehicle($data) {
//        insert vehicles registration data  
        $this->db->insert('vehicles_registration', $data['data_registered']);
        $reg_id = $this->db->insert_id();

//        insert vehicles_insurance_act data         
        $this->db->insert('vehicles_insurance_act', $data['data_act']);
        $act_id = $this->db->insert_id();

//        insert vehicle data  
        $data['data_vehicle']['RegID'] = $reg_id;
        $data['data_vehicle']['ActID'] = $act_id;
        $this->db->insert('vehicles', $data['data_vehicle']);
        $v_id = $this->db->insert_id();

//      insert vehicles has route
        $data_v_r = array(
            'RCode' => $data['RCode'],
            'VID' => $v_id,
        );
        $this->db->insert('t_routes_has_vehicles', $data_v_r);

//        insert vehicles_driver
        $data['data_driver']['VID'] = $v_id;
        $this->db->insert('vehicles_driver', $data['data_driver']);
        $vdid = $this->db->insert_id();

        return $v_id;
    }

    function update_vehicle($data) {

        $vid = $data['VID'];
        $reg_id = $data['data_vehicle']['RegID'];
        $act_id = $data['data_vehicle']['ActID'];

//        update vehicles registration data 
        $this->db->where('RegID', $reg_id);
        $this->db->update('vehicles_registration', $data['data_registered']);


//        update vehicles insurance act data         
        $this->db->where('ActID', $act_id);
        $this->db->update('vehicles_insurance_act', $data['data_act']);


//        update vehicle data  
        $this->db->where('VID', $vid);
        $this->db->update('vehicles', $data['data_vehicle']);

        //      insert vehicles has route
        $data_v_r = array(
            'RCode' => $data['RCode'],
            'VID' => $vid,
        );
        $this->db->where('VID', $vid);
        $this->db->update('t_routes_has_vehicles', $data_v_r);

        //update vehicles_driver
        $this->db->where('VID', $vid);
        $this->db->update('vehicles_driver', $data['data_driver']);

//        return $vid;
    }

    function delete_vehicle($VID, $RCode, $RegID, $ActID) {
        $flagVR = $this->db->delete('vehicles_registration', array('RegID' => $RegID));
        $flagVIA = $this->db->delete('vehicles_insurance_act', array('ActID' => $ActID));
        $flagV = $this->db->delete('vehicles', array('VID' => $VID));
        $flagRV = $this->db->delete('t_routes_has_vehicles', array('RCode' => $RCode, 'VID' => $VID));
        $flagVD = $this->db->delete('vehicles_driver', array('VID' => $VID));
        if ($flagVR && $flagVIA && $flagV && $flagRV && $flagVD)
            return TRUE;
        else
            return FALSE;
    }

    function set_form_add($rcode, $vtid) {
        //    ข้อมูลรถ
        $i_NumberPlate = array(
            'name' => 'NumberPlate',
            'value' => set_value('NumberPlate'),
            'placeholder' => 'ทะเบียนรถ',
            'class' => 'form-control');
        $i_VCode = array(
            'name' => 'VCode',
            'value' => set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control');
        $i_VColor = array(
            'name' => 'VColor',
            'value' => set_value('VColor'),
            'placeholder' => 'สีรถ',
            'class' => 'form-control');
        $i_VBrand = array(
            'name' => 'VBrand',
            'value' => set_value('VBrand'),
            'placeholder' => 'ยี่ห้อรถ',
            'class' => 'form-control');
        $i_VType = array('เลือกประเภทรถ', 'รถตู้', 'รถบัส(แอร์)', 'รถบัส(พัดลม)');
        $i_VSeat = array(
            'name' => 'VSeat',
            'value' => set_value('VSeat'),
            'type' => 'number',
            'placeholder' => '0',
            'class' => 'form-control');
        $i_VStatus = array('1' => 'ปกติพร้อมบริการ', '0' => 'ไม่ปกติไม่พร้อมบริการ');
        $i_VehicleNote = array(
            'name' => 'VehicleNote',
            'value' => set_value('VehicleNote'),
            'placeholder' => '',
            'rows' => '3',
            'class' => 'form-control');

        //ข้อมูลทะเบียน
        $i_DateRegistered = array(
            'name' => 'DateRegistered',
            'value' => set_value('DateRegistered'),
            'placeholder' => 'วันที่ต่อทะเบียน',
            'class' => 'form-control datepicker');
        $i_DateExpire = array(
            'name' => 'DateExpire',
            'value' => set_value('DateExpire'),
            'placeholder' => 'วันหมดอายุ',
            'class' => 'form-control datepicker');
        $i_VRNote = array(
            'name' => 'VRNote',
            'value' => set_value('VRNote'),
            'placeholder' => '',
            'rows' => '3',
            'class' => 'form-control');

//        ประกันและพรบ
        $i_InsuranceCompanyName = array(
            'name' => 'InsuranceCompanyName',
            'value' => set_value('InsuranceCompanyName'),
            'placeholder' => 'ชื่อบริษัทประกัน',
            'class' => 'form-control');

        $i_PolicyType[0] = 'เลือกประเภทกรมธรรม์';
        foreach ($this->get_policy_type() as $value) {
            $i_PolicyType[$value['StringValue']] = $value['StringValue'];
        }

        $i_PolicyStart = array(
            'name' => 'PolicyStart',
            'value' => set_value('PolicyStart'),
            'placeholder' => 'วันที่เริ่มกรมธรรม์',
            'class' => 'form-control datepicker');
        $i_PolicyEnd = array(
            'name' => 'PolicyEnd',
            'value' => set_value('PolicyEnd'),
            'placeholder' => 'วันสิ้นสุดกรมธรรม์',
            'class' => 'form-control datepicker');
        $i_PolicyNumber = array(
            'name' => 'PolicyNumber',
            'value' => set_value('PolicyNumber'),
            'placeholder' => 'เลขที่กรมธรรม์',
            'class' => 'form-control');
        $i_ActNote = array(
            'name' => 'ActNote',
            'rows' => '3',
            'value' => set_value('ActNote'),
            'placeholder' => '',
            'class' => 'form-control');

        // พนักงานขับรถ
        $i_EID = array(
            'name' => 'EID',
            'id' => 'EID',
            'value' => set_value('EID'),
            'type' => 'hidden',
            'class' => 'form-control');
        $i_Driverlicense = array(
            'name' => 'Driverlicense',
            'value' => set_value('Driverlicense'),
            'placeholder' => 'รหัสใบอนุญาติ',
            'class' => 'form-control');
        $i_ExpireDate = array(
            'name' => 'ExpireDate',
            'value' => set_value('ExpireDate'),
            'placeholder' => 'วันหยุดอายุ',
            'class' => 'form-control datepicker');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';

        $form_add = array(
            'form' => form_open_multipart('vehicle/add/' . $rcode . '/' . $vtid, array('class' => 'form-horizontal', 'id' => 'form_vehicle')),
            'NumberPlate' => form_input($i_NumberPlate),
            'VCode' => form_input($i_VCode),
            'VColor' => form_input($i_VColor),
            'VBrand' => form_input($i_VBrand),
            'VType' => form_dropdown('VType', $i_VType, set_value('VType'), $dropdown),
            'VSeat' => form_input($i_VSeat),
            'VStatus' => form_dropdown('VStatus', $i_VStatus, set_value('VStatus'), $dropdown),
            'VehicleNote' => form_textarea($i_VehicleNote),
            'DateRegistered' => form_input($i_DateRegistered),
            'DateExpire' => form_input($i_DateExpire),
            'VRNote' => form_textarea($i_VRNote),
            'InsuranceCompanyName' => form_input($i_InsuranceCompanyName),
            'PolicyType' => form_dropdown('PolicyType', $i_PolicyType, set_value('PolicyType'), $dropdown),
            'PolicyStart' => form_input($i_PolicyStart),
            'PolicyEnd' => form_input($i_PolicyEnd),
            'PolicyNumber' => form_input($i_PolicyNumber),
            'ActNote' => form_textarea($i_ActNote),
            'EID' => form_input($i_EID),
            'Driverlicense' => form_input($i_Driverlicense),
            'ExpireDate' => form_input($i_ExpireDate),
        );
        return $form_add;
    }

    function set_form_edit($rcode, $vtid, $data) {

        //ข้อมูลเส้นทาง
        $i_RCode[0] = 'เลือกเส้นทาง';
        foreach ($this->get_route() as $value) {
            $i_RCode[$value['RCode']] = $value['RCode'] . ' ' . $value['RSource'] . ' - ' . $value['RDestination'];
        }
        //    ข้อมูลรถ
        $i_VTID[0] = 'เลือกประเภทรถ';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }

        $i_NumberPlate = array(
            'name' => 'NumberPlate',
            'value' => (set_value('NumberPlate') == NULL) ? $data ['NumberPlate'] : set_value('NumberPlate'),
            'placeholder' => 'ทะเบียนรถ',
            'class' => 'form-control');
        $i_VCode = array(
            'name' => 'VCode',
            'value' => (set_value('VCode') == NULL) ? $data ['VCode'] : set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control');
        $i_VColor = array(
            'name' => 'VColor',
            'value' => (set_value('VColor') == NULL) ? $data ['VColor'] : set_value('VColor'),
            'placeholder' => 'สีรถ',
            'class' => 'form-control');
        $i_VBrand = array(
            'name' => 'VBrand',
            'value' => (set_value('VBrand') == NULL) ? $data ['VBrand'] : set_value('VBrand'),
            'placeholder' => 'ยี่ห้อรถ',
            'class' => 'form-control');

        $i_VSeat = array(
            'name' => 'VSeat',
            'value' => (set_value('VSeat') == NULL) ? $data ['VSeat'] : set_value('VSeat'),
            'type' => 'number',
            'placeholder' => '0',
            'class' => 'form-control');
        $i_VStatus = array('1' => 'ปกติพร้อมบริการ', '0' => 'ไม่ปกติไม่พร้อมบริการ');
        $i_VehicleNote = array(
            'name' => 'VehicleNote',
            'value' => (set_value('VehicleNote') == NULL) ? $data ['VehicleNote'] : set_value('VehicleNote'),
            'placeholder' => '',
            'rows' => '3',
            'class' => 'form-control');

        //ข้อมูลทะเบียน
        $date_registered = $this->m_datetime->setDBDateToTH($data ['DateRegistered']);
        $i_DateRegistered = array(
            'name' => 'DateRegistered',
            'value' => (set_value('DateRegistered') == NULL) ? $date_registered : set_value('DateRegistered'),
            'placeholder' => 'วันที่ต่อทะเบียน',
            'class' => 'form-control datepicker');
        $date_expire = $this->m_datetime->setDBDateToTH($data ['DateExpire']);
        $i_DateExpire = array(
            'name' => 'DateExpire',
            'value' => (set_value('DateExpire') == NULL) ? $date_expire : set_value('DateExpire'),
            'placeholder' => 'วันหมดอายุ',
            'class' => 'form-control datepicker');
        $i_VRNote = array(
            'name' => 'VRNote',
            'value' => (set_value('VRNote') == NULL) ? $data ['VRNote'] : set_value('VRNote'),
            'placeholder' => '',
            'rows' => '3',
            'class' => 'form-control');

//        ประกันและพรบ
        $i_InsuranceCompanyName = array(
            'name' => 'InsuranceCompanyName',
            'value' => (set_value('InsuranceCompanyName') == NULL) ? $data ['InsuranceCompanyName'] : set_value('InsuranceCompanyName'),
            'placeholder' => 'ชื่อบริษัทประกัน',
            'class' => 'form-control');

        $i_PolicyType[0] = 'เลือกประเภทกรมธรรม์';
        foreach ($this->get_policy_type() as $value) {
            $i_PolicyType[$value['StringValue']] = $value['StringValue'];
        }

        $policy_start = $this->m_datetime->setDBDateToTH($data ['PolicyStart']);
        $i_PolicyStart = array(
            'name' => 'PolicyStart',
            'value' => (set_value('PolicyStart') == NULL) ? $policy_start : set_value('PolicyStart'),
            'placeholder' => 'วันที่เริ่มกรมธรรม์',
            'class' => 'form-control datepicker');

        $policy_end = $this->m_datetime->setDBDateToTH($data ['PolicyEnd']);
        $i_PolicyEnd = array(
            'name' => 'PolicyEnd',
            'value' => (set_value('PolicyEnd') == NULL) ? $policy_end : set_value('PolicyEnd'),
            'placeholder' => 'วันสิ้นสุดกรมธรรม์',
            'class' => 'form-control datepicker');

        $i_PolicyNumber = array(
            'name' => 'PolicyNumber',
            'value' => (set_value('PolicyNumber') == NULL) ? $data ['PolicyNumber'] : set_value('PolicyNumber'),
            'placeholder' => 'เลขที่กรมธรรม์',
            'class' => 'form-control');
        $i_ActNote = array(
            'name' => 'ActNote',
            'rows' => '3',
            'value' => (set_value('ActNote') == NULL) ? $data ['ActNote'] : set_value('ActNote'),
            'placeholder' => '',
            'class' => 'form-control');

        // พนักงานขับรถ
        $i_EID = array(
            'name' => 'EID',
            'id' => 'EID',
            'value' => (set_value('EID') == NULL) ? $data ['EID'] : set_value('EID'),
            'type' => 'hidden',
            'class' => 'form-control');
        $i_Driverlicense = array(
            'name' => 'Driverlicense',
            'value' => (set_value('Driverlicense') == NULL) ? $data ['Driverlicense'] : set_value('Driverlicense'),
            'placeholder' => 'รหัสใบอนุญาติ',
            'class' => 'form-control');
        $date_ExpireDate = $this->m_datetime->setDBDateToTH($data ['ExpireDate']);
        $i_ExpireDate = array(
            'name' => 'ExpireDate',
            'value' => (set_value('ExpireDate') == NULL) ? $date_ExpireDate : set_value('ExpireDate'),
            'placeholder' => 'วันหยุดอายุ',
            'class' => 'form-control datepicker');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';
        $form_edit = array(
            'form' => form_open_multipart('vehicle/edit/' . $rcode . '/' . $vtid . '/' . $data['VID'], array('class' => 'form-horizontal', 'id' => 'form_vehicle')),
            'RCode' => form_dropdown('RCode', $i_RCode, (set_value('RCode') == NULL) ? $data ['RCode'] : set_value('RCode'), $dropdown),
            'VTID' => form_dropdown('VTID', $i_VTID, (set_value('VTID') == NULL) ? $data ['VTID'] : set_value('VTID'), $dropdown),
            'NumberPlate' => form_input($i_NumberPlate),
            'VCode' => form_input($i_VCode),
            'VColor' => form_input($i_VColor),
            'VBrand' => form_input($i_VBrand),
            'VSeat' => form_input($i_VSeat),
            'VStatus' => form_dropdown('VStatus', $i_VStatus, set_value('VStatus'), $dropdown),
            'VehicleNote' => form_textarea($i_VehicleNote),
            'DateRegistered' => form_input($i_DateRegistered),
            'DateExpire' => form_input($i_DateExpire),
            'VRNote' => form_textarea($i_VRNote),
            'InsuranceCompanyName' => form_input($i_InsuranceCompanyName),
            'PolicyType' => form_dropdown('PolicyType', $i_PolicyType, (set_value('PolicyType') == NULL) ? $data ['PolicyType'] : set_value('PolicyType'), $dropdown),
            'PolicyStart' => form_input($i_PolicyStart),
            'PolicyEnd' => form_input($i_PolicyEnd),
            'PolicyNumber' => form_input($i_PolicyNumber),
            'ActNote' => form_textarea($i_ActNote),
            'EID' => form_input($i_EID),
            'Driverlicense' => form_input($i_Driverlicense),
            'ExpireDate' => form_input($i_ExpireDate),
        );

//        'product_type_id' => form_dropdown('product_type_id', $i_type, (set_value('product_type_id') == NULL) ? $data ['product_type_id'] : set_value('product_type_id'), 'class="form-control"'),


        return $form_edit;
    }

    function set_form_search() {
        //ข้อมูลเส้นทาง
        $i_RCode[0] = 'เส้นทางทั้งหมด';
        foreach ($this->get_route() as $value) {
            $i_RCode[$value['RCode']] = $value['RCode'] . ' ' . $value['RSource'] . ' - ' . $value['RDestination'];
        }

        $i_VTID[0] = 'ประเภทรถทั้งหมด';
        foreach ($this->get_vehicle_types() as $value) {
            $i_VTID[$value['VTID']] = $value['VTDescription'];
        }
        $i_NumberPlate = array(
            'name' => 'NumberPlate',
            'value' => set_value('NumberPlate'),
            'placeholder' => 'ทะเบียนรถ',
            'class' => 'form-control');

        $i_VCode = array(
            'name' => 'VCode',
            'value' => set_value('VCode'),
            'placeholder' => 'เบอร์รถ',
            'class' => 'form-control');

        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';

        $form_search = array(
            'form' => form_open('vehicle/', array('class' => 'form-horizontal', 'id' => 'form_search_vehicle')),
            'RCode' => form_dropdown('RCode', $i_RCode, set_value('RCode'), $dropdown),
            'VTID' => form_dropdown('VTID', $i_VTID, set_value('VTID'), 'class="selecter_3" '),
            'NumberPlate' => form_input($i_NumberPlate),
            'VCode' => form_input($i_VCode),
        );

        return $form_search;
    }

    function validation_form_add() {
////       ข้อมูลรถ
        $this->form_validation->set_rules('NumberPlate', 'ทะเบียนรถ', 'trim|required|xss_clean|callback_check_numberplate');
        $this->form_validation->set_rules('VCode', 'เบอร์รถ', 'trim|required|callback_check_vcode');
        $this->form_validation->set_rules('VColor', 'สีรถ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VBrand', 'ยี่ห้อรถ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VSeat', 'จำนวนที่นั่ง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VStatus', 'สถานะ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VehicleNote', 'หมายเหตุ', 'trim|xss_clean');
///      ข้อมูลทะเบียน
        $this->form_validation->set_rules('DateRegistered', 'วันที่ต่อทะเบียน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('DateExpire', 'วันหมดอายุ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VRNote', 'หมายเหตุ', 'trim|xss_clean');
////       ประกันและพรบ
        $this->form_validation->set_rules('InsuranceCompanyName', 'ชื่อบริษัทประกัน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyType', 'ประเภทกรมธรรม์', 'trim|required|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('PolicyStart', 'วันที่เริ่มกรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyEnd', 'วันที่สิ้นสุดกรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyNumber', 'เลขที่กรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ActNote', 'หมายเหตุ', 'trim|xss_clean');
        //พนักงานขับรถ
        $this->form_validation->set_rules('EID', 'รหัสพนักงาน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('Driverlicense', 'ใบขับขี่', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ExpireDate', 'วันหมดอายุ', 'trim|required|xss_clean');
        return TRUE;
    }

    function validation_form_edit() {
//        ข้อมูลเส้นทาง
        $this->form_validation->set_rules('RCode', 'เส้นทาง', 'trim|required|xss_clean|callback_check_dropdown');

//       ข้อมูลรถ
        $this->form_validation->set_rules('VTID', 'ประเภทรถ', 'trim|required|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('NumberPlate', 'ทะเบียนรถ', 'trim|required|xss_clean|callback_check_numberplate');
        $this->form_validation->set_rules('VCode', 'เบอร์รถ', 'trim|required|xss_clean|callback_check_vcode');
        $this->form_validation->set_rules('VColor', 'สีรถ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VBrand', 'ยี่ห้อรถ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VSeat', 'จำนวนที่นั่ง', 'trim|required|xss_clean');
        $this->form_validation->set_rules('RegNote', 'หมายเหตุ', 'trim|xss_clean');

//      ข้อมูลทะเบียน
        $this->form_validation->set_rules('DateRegistered', 'วันที่ต่อทะเบียน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('DateExpire', 'วันหมดอายุ', 'trim|required|xss_clean');
        $this->form_validation->set_rules('VRNote', 'หมายเหตุ', 'trim|xss_clean');
//       ประกันและพรบ
        $this->form_validation->set_rules('InsuranceCompanyName', 'ชื่อบริษัทประกัน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyType', 'ประเภทกรมธรรม์', 'trim|required|xss_clean|callback_check_dropdown');
        $this->form_validation->set_rules('PolicyStart', 'วันที่เริ่มกรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyEnd', 'วันที่สิ้นสุดกรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PolicyNumber', 'เลขที่กรมธรรม์', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ActNote', 'หมายเหตุ', 'trim|xss_clean');
        //พนักงานขับรถ
        $this->form_validation->set_rules('EID', 'รหัสพนักงาน', 'trim|required|xss_clean');
        $this->form_validation->set_rules('Driverlicense', 'ใบขับขี่', 'trim|required|xss_clean');
        $this->form_validation->set_rules('ExpireDate', 'วันหมดอายุ', 'trim|required|xss_clean');
        return TRUE;
    }

    function get_post_form_add($rcode, $vtid) {
//       ข้อมูลรถ        
        $data_vehicle = array(
            'NumberPlate' => $this->input->post('NumberPlate'),
            'VTID' => $vtid,
            'VCode' => $this->input->post('VCode'),
            'VColor' => $this->input->post('VColor'),
            'VBrand' => $this->input->post('VBrand'),
            'VSeat' => $this->input->post('VSeat'),
            'VStatus' => $this->input->post('VStatus'),
            'VehicleNote' => $this->input->post('VehicleNote'),
            'CreateBy' => $this->session->userdata('username'),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );
//      ข้อมูลทะเบียน        
        $data_registered = array(
            'DateRegistered' => $this->m_datetime->setTHDateToDB($this->input->post('DateRegistered')),
            'DateExpire' => $this->m_datetime->setTHDateToDB($this->input->post('DateExpire')),
            'VRNote' => $this->input->post('VRNote'),
            'CreateBy' => $this->session->userdata('username'),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );
//       ประกันและพรบ    
        $data_act = array(
            'InsuranceCompanyName' => $this->input->post('InsuranceCompanyName'),
            'PolicyType' => $this->input->post('PolicyType'),
            'PolicyStart' => $this->m_datetime->setTHDateToDB($this->input->post('PolicyStart')),
            'PolicyEnd' => $this->m_datetime->setTHDateToDB($this->input->post('PolicyEnd')),
            'PolicyNumber' => $this->input->post('PolicyNumber'),
            'ActNote' => $this->input->post('ActNote'),
            'CreateBy' => $this->session->userdata('username'),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );
        // พนักงานขับรถ
        $data_driver = array(
            'EID' => $this->input->post('EID'),
            'Driverlicense' => $this->input->post('Driverlicense'),
            'ExpireDate' => $this->m_datetime->setTHDateToDB($this->input->post('ExpireDate')),
        );

        $form_data = array(
            'RCode' => $rcode,
            'data_vehicle' => $data_vehicle,
            'data_registered' => $data_registered,
            'data_act' => $data_act,
            'data_driver' => $data_driver,
        );

        return $form_data;
    }

    function get_post_form_edit($vid) {
        $detail = $this->m_vehicle->get_vehicle($vid);
        if ($detail[0] != NULL) {
//            $rid = $detail[0]['RID'];
//            $vtid = $detail[0]['VTID'];
            $regid = $detail[0]['RegID'];
            $actid = $detail[0]['ActID'];
        }
//       ข้อมูลรถ        
        $data_vehicle = array(
            'NumberPlate' => $this->input->post('NumberPlate'),
            'VTID' => $this->input->post('VTID'),
            'VCode' => $this->input->post('VCode'),
            'VColor' => $this->input->post('VColor'),
            'VBrand' => $this->input->post('VBrand'),
            'VSeat' => $this->input->post('VSeat'),
            'RegID' => $regid,
            'ActID' => $actid,
        );
//      ข้อมูลทะเบียน        
        $data_registered = array(
            'DateRegistered' => $this->m_datetime->setTHDateToDB($this->input->post('DateRegistered')),
            'DateExpire' => $this->m_datetime->setTHDateToDB($this->input->post('DateExpire')),
            'VRNote' => $this->input->post('VRNote'),
        );
//       ประกันและพรบ    
        $data_act = array(
            'InsuranceCompanyName' => $this->input->post('InsuranceCompanyName'),
            'PolicyType' => $this->input->post('PolicyType'),
            'PolicyStart' => $this->m_datetime->setTHDateToDB($this->input->post('PolicyStart')),
            'PolicyEnd' => $this->m_datetime->setTHDateToDB($this->input->post('PolicyEnd')),
            'PolicyNumber' => $this->input->post('PolicyNumber'),
            'ActNote' => $this->input->post('ActNote'),
        );
        // พนักงานขับรถ
        $data_driver = array(
            'EID' => $this->input->post('EID'),
            'Driverlicense' => $this->input->post('Driverlicense'),
            'ExpireDate' => $this->m_datetime->setTHDateToDB($this->input->post('ExpireDate')),
        );
        $form_data = array(
            'VID' => $vid,
            'RCode' => $this->input->post('RCode'),
            'data_vehicle' => $data_vehicle,
            'data_registered' => $data_registered,
            'data_act' => $data_act,
            'data_driver' => $data_driver,
        );
        return $form_data;
    }

    function get_free_driver_list($vid = NULL) {
        $query = $this->db->get_where('employees', array('PID' => '1'));
        $temp = $query->result_array();
        $own = NULL;
        for ($i = 0; $i < count($temp); $i++) {
            $EID = $temp[$i]['EID'];
            $query2 = $this->db->get_where('vehicles_driver', array('EID' => $EID));
            if ($query2->num_rows() > 0) {
                $temp2 = $query2->result_array();
                if ($temp2[0]['VID'] == $vid && $vid != NULL) {
                    $own = $temp[$i];
                }
                unset($temp[$i]);
            }
        }
        if ($own != NULL)
            array_unshift($temp, $own);

        return $temp;
    }

    function get_employee_driver($vid) {
        $this->db->from('vehicles_driver');
        $this->db->join('employees', 'employees.EID = vehicles_driver.EID');
        $this->db->where('vehicles_driver.VID', $vid);
        $query = $this->db->get();
        return $query->result_array();
    }

    function check_vehicle($VID) {
        $this->db->from('vehicles as ve');
        $this->db->join('t_routes_has_vehicles as rv', 'rv.VID = ve.VID');
        $this->db->where('ve.VID', $VID);
        $query = $this->db->get();
        return $query->result_array();
    }

}
