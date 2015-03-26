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
        $this->load->model('m_cost');
        $this->load->model('m_report');
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
        $data = array(
            'page_title' => 'รายงานส่งเงิน',
            'page_title_small' => " : วันที่ $date_th",
            'previous_page' => "",
            'next_page' => "",
            'reports' => $this->m_report->set_form_view(),
        );

        $data_debug = array(
//            'reports' => $data['reports'],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('รายงาน');
        $this->m_template->set_Content('report/report', $data);
        $this->m_template->showTemplate();
    }

    public function send($rcode, $vtid, $sid) {

        if ($rcode == NULL || $vtid == NULL || $sid == NULL) {
            redirect('report');
        }

        $date = $this->m_datetime->getDateToday();
        $date_th = $this->m_datetime->DateThaiToDay();

        $schedules = $this->m_schedule->get_schedule($date, $rcode, $vtid);

        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้มูลรอบเวลา วันที่ $date_th";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }

        $routes = $this->m_route->get_route_by_seller($rcode, $vtid);
        $routes_detail = $this->m_route->get_route_detail_by_seller($rcode, $vtid);

        $stations = $this->m_station->get_stations($rcode, $vtid);

        if (count($routes) <= 0 || count($routes_detail) <= 0 || count($stations) <= 0) {
            $alert['alert_message'] = "ไม่พบข้อมูล กรุณาติดต่อผู้ดูเเลระบบ";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }


        $vt_name = $routes[0]['VTDescription'];
        $source = $routes[0]['RSource'];
        $detination = $routes[0]['RDestination'];
        $route_name = "$vt_name เส้นทาง $rcode $source - $detination";

        $form_data = '';
        $ReportID = '';

        if ($this->m_report->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_report->get_post_form_send();
            $ReportID = $this->m_report->insert_report($form_data);
            if ($form_data != FALSE && $ReportID != NULL) {
                $alert['alert_message'] = "ส่งรายงาน $route_name วันที่ $date_th";
                $alert['alert_mode'] = "success";
                $this->session->set_flashdata('alert', $alert);
            } else {
                $alert['alert_message'] = "ไม่พบข้อมูลรายงาน : $route_name วันที่ $date_th ณ เวลา " . date('H:s');
                $alert['alert_mode'] = "warning";
                $this->session->set_flashdata('alert', $alert);
            }
            $this->session->set_flashdata('RCode', $rcode);
            $this->session->set_flashdata('VTID', $vtid);

            redirect("report/print_report/$rcode/$vtid/$sid/$ReportID");
        }

        $data = array(
            'page_title' => "ส่งรายงาน : $route_name",
            'page_title_small' => "วันที่ $date_th",
            'previous_page' => "",
            'next_page' => "",
            'data' => $this->m_report->set_form_send($rcode, $vtid, $sid),
        );

        $data_debug = array(
//            'form_data' => $form_data,
//            'rs' => $rs,
//            'get_report' => $this->m_report->get_report($this->m_datetime->getDateToday(), $rcode, $vtid, $sid),
//            'data' => $data['data'],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ส่งเงิน');
        $this->m_template->set_Content('report/frm_report', $data);
        $this->m_template->showTemplate();
    }

    public function print_report($RCode, $VTID, $SID, $ReportID = NULL) {

        if ($RCode == NULL || $VTID == NULL || $SID == NULL) {
            redirect('report/');
        }
        $data = array(
            'page_title' => "พิมพ์รายงานส่งเงิน",
            'page_title_small' => "",
            'previous_page' => "",
            'next_page' => "",
            'data_reports' => $this->m_report->set_data_print($RCode, $VTID, $ReportID),
        );
        $data_debug = array(
//            'data_reports' => $data['data_reports'],
        );

        $this->session->set_flashdata('RCode', $RCode);
        $this->session->set_flashdata('VTID', $VTID);

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์ใบส่งเงิน');
        $this->m_template->set_Content('report/frm_report_print', $data);
        $this->m_template->showTemplate();
    }

}
