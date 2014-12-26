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

        $cost_type = $this->m_cost->get_cost_type();
        $costs = $this->m_cost->get_cost();
        $costs_detail = $this->m_cost->get_cost_detail();

        $routes = $this->m_route->get_route();

        $date = $this->m_datetime->getDateToday();
        $schedules = $this->m_schedule->get_schedule($date);
        $stations = $this->m_station->get_stations(264);
        $data = array(
            'page_title' => 'ค่าใช้จ่าย : ',
            'page_title_small' => 'ขอนแก่น',
            'cost_types' => $cost_type,
            'costs' => $costs,
            'costs_detail' => $costs_detail,
            'routes' => $routes,
            'route_details' => $this->m_route->get_route_detail(),
            'schedules' => $schedules,
            'stations' => $stations
        );
        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'routes' => $data['routes'],
//            'schedules' => $data['schedules'],
            'stations' => $data['stations'],
                //    ''=>$data[''],           
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/cost', $data);
        $this->m_template->showTemplate();
    }

    public function add($ctid) {
        $form_data = '';
        $rs = '';
        if ($this->m_cost->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_cost->get_post_form_add($ctid);
            $rs = $this->m_cost->insert_cost($form_data);
            redirect('cost/');
        }
        $page_title = 'เพิ่ม ' . $this->m_cost->get_cost_type($ctid)[0]['CostTypeName'] . ' ';
        $data = array(
            'form' => $this->m_cost->set_form_add($ctid),
            'page_title' => $page_title,
            'page_title_small' => '',
//            'cost_types' => $cost_type,
//            'costs' => $costs,
//            'costs_detail' => $costs_detail,
        );

        $data_debug = array(
//            'form_data' => $form_data,
//            'data_insert_rs'=>$rs,
        );

//        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/frm_cost', $data);
        $this->m_template->showTemplate();
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
