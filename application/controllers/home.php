<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

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
        $this->set_user();
        $data = array(
            'from_search' => $this->m_route->set_form_search_route(),
            'vehicle_types' => $this->m_route->get_vehicle_types(),
            'routes' => $this->m_route->get_route(),
            'routes_detail' => $this->m_route->get_route_detail(),
        );
        $data['stations'] = $this->m_station->get_stations();
         $data['schedules'] = $this->m_schedule->get_schedule($this->m_datetime->getDateToday());
        $data['schedule_master'] = $this->m_route->get_schedule_manual();
        $data_debug = array(
//            'from_search' => $data['from_search'],
//    'route'=>$data['route'],
//    ''=>$data[''],
//    ''=>$data[''],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ระบบขายตั๋วหน้าเค้ฆาเตอร์');
        $this->m_template->set_Content('home/main', $data);
        $this->m_template->showTemplate();
    }

    public function sale_tickit() {


        $this->m_template->set_Title('ระบบขายตั๋วหน้าเค้าเตอร์');
        $this->m_template->set_Content('home/main', $data);
        $this->m_template->showTemplate();
    }

    public function test() {
        $this->load->view("home/frm_search_route");
    }

    public function set_user() {
        $user_data = array(
            'EID' => 'E123456789',
            'UserName' => 'admin',
            'sale_type' => '1',
        );
        $this->session->set_userdata($user_data);
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */