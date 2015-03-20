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
        $this->load->model('m_home');
        $this->load->library('form_validation');

        //Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {
        if ($this->m_home->set_validation() && $this->form_validation->run()) {
            $input = $this->input->post();
            $EID = $this->m_home->get_user_id();
            $new_pass = $input['new_pass'];
            $have = $this->m_home->check_pass($EID, $input['old_pass']);
            if ($have) {
                if ($this->m_home->update_user($EID, $new_pass)) {
                    //Alert success and redirect to candidate
                    $alert['alert_message'] = "เปลี่ยนรหัสผ่านสำเร็จ";
                    $alert['alert_mode'] = "success";
                    $this->session->set_flashdata('alert', $alert);
                    redirect('home');
                } else {
                    //Alert success and redirect to candidate
                    $alert['alert_message'] = "เปลี่ยนรหัสผ่านไม่สำเร็จ";
                    $alert['alert_mode'] = "danger";
                    $this->session->set_flashdata('alert', $alert);
                    redirect('home');
                }
            } else {
                //Alert success and redirect to candidate
                $alert['alert_message'] = "คุณกรอกรหัสผ่านเก่าไม่ถูกต้อง";
                $alert['alert_mode'] = "danger";
                $this->session->set_flashdata('alert', $alert);
                redirect('home');
            }
        } else {
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                //Alert success and redirect to candidate
                $alert['alert_message'] = "กรุณากรอกข้อมูลให้ครบ";
                $alert['alert_mode'] = "danger";
                $this->session->set_flashdata('alert', $alert);
                redirect('home');
            }
        }

        $data = array(
            'from_search' => $this->m_route->set_form_search_route(),
            'vehicle_types' => $this->m_route->get_vehicle_types(),
            'routes' => $this->m_route->get_route(),
            'routes_detail' => $this->m_route->get_route_detail(),
        );
        $data['stations'] = $this->m_station->get_stations();
        $data['schedules'] = $this->m_schedule->get_schedule($this->m_datetime->getDateToday());
        $data['schedule_master'] = $this->m_route->get_schedule_manual();
        $data['detail'] = $this->m_home->get_seller_detail($this->m_home->get_user_id())[0];
        $data['seller_detail'] = $this->m_user->get_saller_station();
        $data['all_sid'] = array();
        foreach ($data['seller_detail'] as $row) {
            array_push($data['all_sid'], $row['SID']);
        }
//        $data['timeline'] = $this->m_home->get_timeline(NULL, $data['all_sid']);
        $data_debug = array(
//            'from_search' => $data['from_search'],
//    'route'=>$data['route'],
//    ''=>$data[''],
//    ''=>$data[''],
//            'detail' => $data['detail'],
//            'input' => (isset($input)) ? $input : NULL,
//            'timeline' => $data['timeline'],
//            'seller_detail' => $data['seller_detail'],
//            'all_sid' => $data['all_sid'],
//            'test' => $data['timeline'],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ระบบขายตั๋วหน้าเค้ฆาเตอร์');
        $this->m_template->set_Content('home/main', $data);
        $this->m_template->showTemplate();
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */