<?php

/*
 * ลงเวลาการของรถแต่ละคัน
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class checkin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_sale');
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_checkin');
        $this->load->library('form_validation');

//Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {

        $data = array(
            'page_title' => 'ลงเวลา : ',
            'page_title_small' => '',
            'previous_page' => '',
            'next_page' => '',
            'routes_detail' => $this->m_route->get_route_detail(),
        );
        $date = $this->m_datetime->getDateToday();
        $vehicle_types = $this->m_route->get_vehicle_types();
        $routes = $this->m_route->get_route();
        $schedules = $this->m_schedule->get_schedule($date);
        $stations = $this->m_station->get_stations();
        $stations_sale_ticket = $this->m_station->get_station_sale_ticket();


        if (count($schedules) <= 0) {
            redirect("checkin/");
        }

        $data['vehicle_types'] = $vehicle_types;
        $data['routes'] = $routes;
        $data['schedules'] = $schedules;
        $data['stations'] = $stations;
        $data['stations_sale_ticket'] = $stations_sale_ticket;

        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'routes' => $data['routes'],
//            'schedules' => $data['schedules'],
//            'stations'=>$data['stations'], 
//            'station_sale_ticket' => $data['station_sale_ticket'],
                //    ''=>$data[''],           
        );

        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ลงเวลา');
        $this->m_template->set_Content('checkin/checkin', $data);
        $this->m_template->showTemplate();
    }

    public function add($vtid = NULL) {

        if ($vtid == NULL) {
            redirect('checkin/');
        }
        $data = array(
            'page_title' => 'ลงเวลา : ',
            'page_title_small' => '',
            'previous_page' => '',
            'next_page' => '',
        );
        $schedules = $this->m_schedule->get_schedule();
        $stations = $this->m_station->get_stations();
        $station_sale_ticket = $this->m_station->get_station_sale_ticket();

        if (count($schedules) <= 0) {
            redirect("checkin/");
        }
        $form_data = array();
        if ($this->m_checkin->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_checkin->get_post_form_add();
        }

        $data['schedules'] = $schedules;
        $data['stations'] = $stations;
        $data['station_sale_ticket'] = $station_sale_ticket;
        $rcode = $this->input->post('RCode');
        if (isset($rcode) && $rcode != 0) {
            $data['form'] = $this->m_checkin->set_form_add($rcode, $vtid);
        } else {
            $data['form'] = $this->m_checkin->set_form_add(NULL, $vtid);
        }

        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'routes' => $data['routes'],
//            'schedules' => $data['schedules'],
//            'stations'=>$data['stations'], 
//            'station_sale_ticket' => $data['station_sale_ticket'],
                //    ''=>$data[''],           
        );


        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ลงเวลา');
        $this->m_template->set_Content('checkin/frm_checkin', $data);
        $this->m_template->showTemplate();
    }

    //    ตรวจสอบค่าใน dropdown
    public function check_dropdown($str) {
        if ($str === '0') {
            $this->form_validation->set_message('check_dropdown', 'เลือก %s');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
