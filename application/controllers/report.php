<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->library('form_validation');

        //Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {
        $data = array(
//            'from_search' => $this->m_schedule->set_form_search_route(),
            'vehicle_types' => $this->m_route->get_vehicle_types(),
            'routes' => $this->m_route->get_route(),
            'routes_detail' => $this->m_route->get_route_detail(),
            'page_title' => 'รายงาน',
            'page_title_small' => ': สถานนีที่ขายตั๋ว ',
            'previous_page' => "",
            'next_page' => "",
        );
        $data['stations'] = $this->m_station->get_station_sale_ticket();
        $data['schedules'] = $this->m_schedule->get_schedule($this->m_datetime->getDateToday());
        $data['schedule_master'] = $this->m_route->get_schedule_manual();
        $data_debug = array(
//            'from_search' => $data['from_search'],
//    'route'=>$data['route'],
//            'stations' => $data['stations'],
//    ''=>$data[''],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('รายงาน');
        $this->m_template->set_Content('report/report', $data);
        $this->m_template->showTemplate();
    }

}
