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
        );
        $date = $this->m_datetime->getDateToday();
        $vehicle_types = $this->m_route->get_vehicle_types();
        $routes = $this->m_route->get_route_by_seller();
        $routes_detail = $this->m_route->get_route_detail_by_seller();
        $schedules = $this->m_checkin->get_schedule($date);
        $stations = $this->m_station->get_stations();
        $stations_sale_ticket = $this->m_station->get_station_sale_ticket();


        if (count($schedules) <= 0) {
            redirect("checkin/");
        }

        $data['vehicle_types'] = $vehicle_types;
        $data['routes'] = $routes;
        $data['routes_detail'] = $routes_detail;
        $data['schedules'] = $schedules;
        $data['stations'] = $stations;
        $data['stations_sale_ticket'] = $stations_sale_ticket;

        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//            'routes' => $data['routes'],
//            'routes_detail' => $data['routes_detail'],
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

    public function add($rid, $tsid, $vid, $sid) {
        if ($rid == NULL || $tsid == NULL || $vid == NULL || $sid == NULL) {
            redirect('');
        }

        $data = array(
            'page_title' => 'ลงเวลา : ',
            'page_title_small' => '',
            'previous_page' => '',
            'next_page' => '',
        );
        $date = $this->m_datetime->getDateToday();
        $vehicle_types = $this->m_route->get_vehicle_types();
        $routes = $this->m_route->get_route_by_seller();
        $routes_detail = $this->m_route->get_route_detail_by_seller();
        $schedules = $this->m_checkin->get_schedule($date);
        $stations = $this->m_station->get_stations();
        $stations_sale_ticket = $this->m_station->get_station_sale_ticket();


        if (count($schedules) <= 0) {
            redirect("checkin/");
        }

        $data['vehicle_types'] = $vehicle_types;
        $data['routes'] = $routes;
        $data['routes_detail'] = $routes_detail;
        $data['schedules'] = $schedules;
        $data['stations'] = $stations;
        $data['stations_sale_ticket'] = $stations_sale_ticket;
        $data_insert = $this->m_checkin->get_post_form_add($rid, $tsid, $vid, $sid);
        $data_debug = array(
            'form_data' => $data_insert,
            'rs' => $this->m_checkin->insert_checkin($data_insert),
        );

        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ลงเวลา');
        $this->m_template->set_Content('checkin/checkin', $data);
        $this->m_template->showTemplate();
    }

}
