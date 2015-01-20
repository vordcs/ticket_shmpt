<?php

/*
 * สำหรับจัดการค่าใช้จ่านที่เกิดขึ้นในแต่ละจุดขายตั๋ว 
 * โดยผู้ขายตั๋วจะเห็นเฉพาะข้อมูลของตัวเองที่ได้ สร้างเท่านั่น 
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class cost extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_sale');
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');
        $this->load->library('form_validation');

//Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {
        $date = $this->m_datetime->getDateToday();
        $date_th = $this->m_datetime->DateThaiToDay();
        $schedules = $this->m_schedule->get_schedule($date);

        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้มูลรอบเวลา วันที่ $date_th";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }

        $cost_type = $this->m_cost->get_cost_type();
        $costs = $this->m_cost->get_cost();

        $vehicle_types = $this->m_route->get_vehicle_types();

        $routes = $this->m_route->get_route_by_seller();
        $routes_detail = $this->m_route->get_route_detail_by_seller();
        $seller_station_id = $routes[0]['SID'];

        $stations = $this->m_station->get_stations();

        $tickets = $this->m_ticket->sum_ticket_price($date, $seller_station_id);

        $data = array(
            'page_title' => 'ค่าใช้จ่าย : ',
            'page_title_small' => "วันที่ $date_th",
            'cost_types' => $cost_type,
            'costs' => $costs,
            'vehicle_types' => $vehicle_types,
            'routes' => $routes,
            'routes_detail' => $routes_detail,
            'schedules' => $schedules,
            'stations' => $stations,
            'tickets' => $tickets,
        );
        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'routes' => $data['routes'],
//            'schedules' => $data['schedules'],
//            'stations' => $data['stations'],
                //    ''=>$data[''],      
//            'saller_station' => $this->m_user->get_saller_station(),
//            'tickets' => $data['tickets'],
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/cost', $data);
        $this->m_template->showTemplate();
    }

    public function view($tsid = NULL) {
        if ($tsid == NULL) {
            $alert['alert_message'] = "กรุณาเลือกรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('cost/');
        }
        $date = $this->m_datetime->getDateToday();
        $schedule = $this->m_schedule->get_schedule($date, NULL, NULL, NULL, $tsid)[0];

        $rid = $schedule['RID'];
        $vcode = $schedule['VCode'];

        $route = $this->m_route->get_route_by_seller(NULL, NULL, $rid)[0];
        $seller_station_id = $route['SID'];
        $cost_type = $this->m_cost->get_cost_type();
        $costs = $this->m_cost->get_cost(NULL, NULL, $date, $tsid);

        $costs_detail = $this->m_cost->get_cost_detail();
        $date_th = $this->m_datetime->DateThaiToDay();



        $SID = $this->m_user->get_saller_station(NULL, NULL, $rid)[0]['SID'];

        $time_departs = $this->m_schedule->get_time_depart($date, $rid, $tsid, $SID)[0];
        $time_depart = $time_departs['TimeDepart'];

        $tickets = $this->m_ticket->sum_ticket_price($date, $seller_station_id, $tsid);

        $data = array(
            'page_title' => 'ค่าใช้จ่าย : ',
            'page_title_small' => "วันที่ $date_th",
            'previous_page' => '',
            'next_page' => '',
            'route' => $route,
            'cost_types' => $cost_type,
            'costs' => $costs,
            'costs_detail' => $costs_detail,
            'schedule' => $schedule,
            'TSID' => $tsid,
            'TimeDepart' => $time_depart,
            'VCode' => $vcode,
            'tickets' => $tickets,
        );

        $data_debug = array(
//            'route' => $data['route'],
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'TSID'=>$tsid,
//            'schedule' => $schedule,
//            'tickets' => $data['tickets'],
        );

        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/view_cost', $data);
        $this->m_template->showTemplate();
    }

    public function add($ctid, $tsid) {

        if ($ctid == null || $tsid == NULL) {
            $alert['alert_message'] = "กรุณาเลือกรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('cost/');
        }
        $date = $this->m_datetime->getDateToday();
        $schedules = $this->m_schedule->get_schedule($date, NULL, NULL, NULL, $tsid);
        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้มูลรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('cost/');
        }

        $RID = $schedules[0]['RID'];

        $SID = $this->m_user->get_saller_station(NULL, NULL, $RID)[0]['SID'];

        $time_departs = $this->m_schedule->get_time_depart($date, $RID, $tsid, $SID)[0];
        $time_depart = $time_departs['TimeDepart'];

        $form_data = '';
        $rs = '';
        if ($this->m_cost->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_cost->get_post_form_add($ctid);
            $rs = $this->m_cost->insert_cost($form_data);

            $alert['alert_message'] = "เพิ่มข้อมูลค่าใช้จ่ายสำเร็จ";
            $alert['alert_mode'] = "success";
            $this->session->set_flashdata('alert', $alert);

            redirect("cost/view/$tsid");
        }
        $page_title = 'เพิ่ม ' . $this->m_cost->get_cost_type($ctid)[0]['CostTypeName'] . ' ';
        $data = array(
            'form' => $this->m_cost->set_form_add($ctid, $tsid, $time_depart),
            'page_title' => $page_title,
            'page_title_small' => '',
            'previous_page' => '',
            'next_page' => '',
            'TimeDepart' => $time_depart,
            'route_name' => '',
        );

        $data_debug = array(
//            'form_data' => $form_data,
//            'data_insert_rs'=>$rs,
//            "schedule" => $this->m_cost->get_schedule($date),
//            'saller_station' => $this->m_user->get_saller_station(),
//            '$time_departs' => $time_departs,
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('เพิ่มค่าใช้จ่าย');
        $this->m_template->set_Content('cost/frm_cost', $data);
        $this->m_template->showTemplate();
    }

    public function edit($cost_type_id, $cost_id, $rid, $tsid) {

        $cost = $this->m_cost->get_cost($cost_id)[0];
        $CostTypeName = $cost['CostTypeName'];
        $date = date('Y-m-d', strtotime($cost['CostDate']));


        $route = $this->m_cost->get_route(NULL, NULL, $rid)[0];
        $RCode = $route['RCode'];
        $VTID = $route['VTID'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name เส้นทาง " . $RCode . ' ' . ' ' . $source . ' - ' . $desination;


        $SID = $this->m_user->get_saller_station(NULL, NULL, $rid)[0]['SID'];

        $time_departs = $this->m_schedule->get_time_depart($date, $rid, $tsid, $SID)[0];
        $time_depart = $time_departs['TimeDepart'];

        $form_data = '';
        $rs = '';
        if ($this->m_cost->validation_form_edit() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_cost->get_post_form_edit($cost_type_id);
            $rs = $this->m_cost->update_cost($cost_id, $form_data);
            $alert['alert_message'] = "แก้ไข ข้อมูลค่าใช้จ่าย";
            $alert['alert_mode'] = "success";
            $this->session->set_flashdata('alert', $alert);
            redirect("cost/view/$tsid");
        }
        $data = array(
            'page_title' => "แก้ไขข้อมูล :  $CostTypeName  ",
            'page_title_small' => "",
            'previous_page' => "cost/view/$tsid",
            'next_page' => '',
            'form' => $this->m_cost->set_form_edit($route, $cost),
            'TimeDepart' => $time_depart,
            'route_name' => $route_name,
        );

        $data_debug = array(
//            'form_data' => $form_data,
//            'data_upadte' => $rs,
//            'route' => $route,
//            'cost' => $cost,
//            "schedule" => $this->m_cost->get_schedule($date),
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('แก้ไขค่าใช้จ่าย');
        $this->m_template->set_Content('cost/frm_cost', $data);
        $this->m_template->showTemplate();
    }

    public function delete($cost_id, $tsid) {


        if ($cost_id == NULL || $tsid == NULL) {
            $alert['alert_message'] = "กรุณาเลือกข้อมูลค่าใช้จ่าย";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);
            redirect('cost/');
        }

        $rs = $this->m_cost->delete_cost($cost_id);
        if ($rs) {
            $alert['alert_message'] = "ลบข้อมูลค่าใช้จ่ายสำเร็จ";
            $alert['alert_mode'] = "info";
            $this->session->set_flashdata('alert', $alert);
        } else {
            $alert['alert_message'] = "ไม่พบข้อมูลค้าใช้จ่าย";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);
        }

        redirect("cost/view/$tsid");
    }

    //ตรวจสอบเบอร์รถ
    public function check_vcode($vcode) {
        $rcode = $this->input->post('RCode');
        $vehicle = $this->m_cost->get_vehicle($vcode, '', $rcode);
        if (count($vehicle) <= 0) {
            $this->form_validation->set_message('check_vcode', 'ไม่พบข้อมูล %s ในเส้นทาง');
            return FALSE;
        } else {
            return TRUE;
        }
    }

//    check dropdown
    public function check_dropdown($str) {
//        $this->input->post('PolicyType')
        if ($str === '0') {
            $this->form_validation->set_message('check_dropdown', 'เลือก %s');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
