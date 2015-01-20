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



        $cost_type = $this->m_cost->get_cost_type();
        $costs = $this->m_cost->get_cost();

        $vehicle_types = $this->m_route->get_vehicle_types();

        $routes = $this->m_route->get_route_by_seller();
        $routes_detail = $this->m_route->get_route_detail_by_seller();

        $seller_station_id = $routes[0]['SID'];

        $stations = $this->m_station->get_stations();

        $tickets = $this->m_ticket->sum_ticket_price($date, $seller_station_id);

        $data = array(
            'page_title' => 'รายงาน',
            'page_title_small' => " : วันที่ $date_th",
            'previous_page' => "",
            'next_page' => "",
            'vehicle_types' => $vehicle_types,
            'routes' => $routes,
            'routes_detail' => $routes_detail,
            'stations' => $stations,
            'schedules' => $schedules,
            'cost_types' => $cost_type,
            'costs' => $costs,
            'tickets' => $tickets
        );

        $data_debug = array(
//            'routes' => $data['routes'],
//            'stations' => $data['stations'],
//            'costs' => $data['costs'],
//            'tickets' => $data['tickets'],
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

        $vehicle_types = $this->m_route->get_vehicle_types();

        $routes = $this->m_route->get_route_by_seller($rcode, $vtid);
        $routes_detail = $this->m_route->get_route_detail_by_seller($rcode, $vtid);

        $stations = $this->m_station->get_stations($rcode, $vtid);

        if (count($routes) <= 0 || count($routes_detail) <= 0 || count($stations) <= 0) {
            $alert['alert_message'] = "ไม่พบข้อมูล กรุณาติดต่อผู้ดูเเลระบบ";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }

        $cost_type = $this->m_cost->get_cost_type();
        $costs = $this->m_cost->get_cost();

        $rcode = $routes[0]['RCode'];
        $vt_name = $routes[0]['VTDescription'];
        $source = $routes[0]['RSource'];
        $detination = $routes[0]['RDestination'];
        $route_name = "$vt_name เส้นทาง $rcode $source - $detination";

        $form_data = '';
        $rs = '';

        if ($this->m_report->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_report->get_post_form_send();
            $rs = $this->m_report->insert_report($form_data);
            if ($form_data != FALSE) {
                $alert['alert_message'] = "ส่งรายงาน $route_name วันที่ $date_th";
                $alert['alert_mode'] = "success";
                $this->session->set_flashdata('alert', $alert);
            } else {
                $alert['alert_message'] = "ไม่พบข้อมูลรายงาน : $route_name วันที่ $date_th ณ เวลา " . date('H:s');
                $alert['alert_mode'] = "warning";
                $this->session->set_flashdata('alert', $alert);
            }
            redirect("report");
        } else {
            $form_data = 'false';
        }
        $tickets = $this->m_ticket->sum_ticket_price($date);

        $data = array(
            'page_title' => "ส่งรายงาน : $route_name",
            'page_title_small' => "วันที่ $date_th",
            'previous_page' => "",
            'next_page' => "",
            'vehicle_types' => $vehicle_types,
            'routes' => $routes,
            'routes_detail' => $routes_detail,
            'stations' => $stations,
            'schedules' => $schedules,
            'cost_types' => $cost_type,
            'costs' => $costs,
            'tickets' => $tickets
        );

        $data_debug = array(
//            'vehicle_types'=>$data['vehicle_types'],
//            'routes' => $data['routes'],
//            'routes_detail' => $data['routes_detail'],
//            'stations' => $data['stations'],
//            'schedules' => $data['schedules'],  
//            'cost_types'=>$data['cost_types'],
//            'costs' => $data['costs'],
//            'tickets' => $data['tickets'],
//            'form_data' => $form_data,
//            'rs' => $rs,
//            'get_report' => $this->m_report->get_report(),
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ส่งรายงาน');
        $this->m_template->set_Content('report/frm_report', $data);
        $this->m_template->showTemplate();
    }

}
