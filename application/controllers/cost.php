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

        $data = array(
            'page_title' => 'ค่าใช้จ่าย',
            'page_title_small' => '',
            'cost_types' => $cost_type,
            'costs' => $costs,
            'costs_detail' => $costs_detail,
        );
        $data_debug = array(
//            'from_search' => $data['from_search'],           
//            'cost_types' => $data['cost_types'],
//            'costs' => $data['costs'],
//            'costs_detail' => $data['costs_detail'],
//    ''=>$data[''],               
                //    ''=>$data[''],             
                //    ''=>$data[''],              
                //    ''=>$data[''],           
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/cost', $data);
        $this->m_template->showTemplate();
    }
    public function add($ctid) {
        $page_title = 'เพิ่ม ' . $this->m_cost->get_cost_type($ctid)[0]['CostTypeName'] . ' ';        
        $data = array(
           'form' => $this->m_cost->set_form_add($ctid),
            'page_title' => $page_title,
            'page_title_small' => '',
//            'cost_types' => $cost_type,
//            'costs' => $costs,
//            'costs_detail' => $costs_detail,
        );
        
        $this->m_template->set_Title('จัดการค่าใช้จ่าย');
        $this->m_template->set_Content('cost/frm_cost', $data);
        $this->m_template->showTemplate();
        
    }
    

}
