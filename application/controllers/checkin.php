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

        $date = $this->m_datetime->getDateToday();
        $date_th = $this->m_datetime->DateThaiToDay();
        $schedules = $this->m_schedule->get_schedule($date);

        if (count($schedules) <= 0) {
            redirect("checkin/");
        }

        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้มูลรอบเวลา วันที่ $date_th";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }
        $data_form_checkin = $this->m_checkin->set_form_check_in();
        $data = array(
            'page_title' => 'ลงเวลา : ',
            'page_title_small' => '',
            'previous_page' => '',
            'next_page' => '',
            'data' => $data_form_checkin,
        );

        $data_debug = array(
//            'data' => $data['data'],
        );

        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ลงเวลา');
        $this->m_template->set_Content('checkin/checkin', $data);
        $this->m_template->showTemplate();
    }

    public function add($RCode = NULL, $VTID = NULL, $tsid = NULL, $sid = NULL) {
        if ($RCode == NULL || $VTID == NULL || $tsid == NULL || $sid == NULL) {
            $alert['alert_message'] = "กรุณาเลือกรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('checkin/');
        }
        $data_insert = $this->m_checkin->get_post_form_add($tsid, $sid);


        $rs = $this->m_checkin->insert_checkin($data_insert);
        if ($rs != '' || $rs != NULL) {
            $alert['alert_message'] = "ลงเวลาสำเร็จ";
            $alert['alert_mode'] = "success";
            $this->session->set_flashdata('alert', $alert);
        } else {
            $alert['alert_message'] = "ลงเวลาไม่สำเร็จ";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);
        }

        $this->session->set_flashdata('RCode', $RCode);
        $this->session->set_flashdata('VTID', $VTID);

        redirect('checkin/');
    }

    public function edit($RCode = NULL, $VTID = NULL, $CheckInID = NULL) {
        if ($RCode == NULL || $VTID == NULL || $CheckInID == NULL) {
            $alert['alert_message'] = "กรุณาเลือกรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('checkin/');
        }

        $data_update = $this->m_checkin->get_post_form_edit();
        $rs = $this->m_checkin->update_checkin($CheckInID, $data_update);

        if ($rs != '' || $rs != NULL) {
            $alert['alert_message'] = "แก้ไข เวลา สำเร็จ";
            $alert['alert_mode'] = "success";
            $this->session->set_flashdata('alert', $alert);
        } else {
            $alert['alert_message'] = "แก้ไขข้อมูลเวลาออกไม่สำเร็จ";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);
        }
        
        $this->session->set_flashdata('RCode', $RCode);
        $this->session->set_flashdata('VTID', $VTID);
        
        redirect('checkin/');
    }

}
