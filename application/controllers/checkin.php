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
        $this->load->library('form_validation');

//Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {

        $data = array(
            'page_title' => 'ลงเวลา : ',
            'page_title_small' => '',
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
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ลงเวลา');
        $this->m_template->set_Content('checkin/checkin', $data);
        $this->m_template->showTemplate();
    }
    
    public function time_live() {        
       
    }

}
