<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class sale extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_sale');
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');
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
            'SourceID' => '',
            'routes' => $this->m_route->get_route_by_seller(),
            'routes_detail' => $this->m_route->get_route_detail_by_seller(),
            'stations' => $this->m_station->get_stations(),
        );

        $data_debug = array(
//            'from_search' => $data['from_search'],
//            'routes' => $data['routes'],
//            'routes_detail' => $data['routes_detail'],
//            'stations' => $data['stations'],
//            'eid' => $this->session->userdata('EID'),
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ระบบขายตั๋วหน้าเคาน์เตอร์ ');
        $this->m_template->set_Content('sale/frm_search', $data);
        $this->m_template->showTemplate();
    }

    public function booking($rid = NULL, $source_id = NULL, $destination_id = NULL, $schedules_id = NULL) {
        $date = $this->m_datetime->getDateToday();

        if ($rid == NULL || $source_id == NULL || $destination_id == NULL) {
            $alert['alert_message'] = "เลือกข้อมูลรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('sale/');
        }
//        Check detail and sent to load form
        $detail = $this->m_route->get_route_by_seller(NULL, NULL, $rid);
        if (count($detail) > 0) {
            $rcode = $detail[0]['RCode'];
            $vtid = $detail[0]['VTID'];
            $vt_name = $detail[0]['VTDescription'];
            $route_name = " $vt_name เส้นทาง" . $detail[0]['RCode'] . ' ' . ' ' . $detail[0]['RSource'] . ' - ' . $detail[0]['RDestination'];
        }
        $stations = $this->m_station->get_stations($rcode, $vtid);
        $s_station = $this->m_station->get_stations($rcode, $vtid, $source_id)[0];
        $d_station = $this->m_station->get_stations($rcode, $vtid, $destination_id)[0];

        $schedules = $this->m_sale->get_schedule($schedules_id);
        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้อมูลตารางเวลาเดิน $route_name กรุณาติดต่อผู้ดูแลระบบ";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

//            redirect('sale/');
        }
        $schedule = $schedules[0];
        $schedules_detail = $this->m_schedule->get_schedule($date, $rcode, $vtid, $rid);

        if (count($s_station) <= 0) {
            redirect('sale/');
        }
        if (count($d_station) <= 0) {
            redirect('sale/');
        }

        if (count($schedule) <= 0 || count($schedules_detail) <= 0) {
            $alert['alert_message'] = "ไม่พบข้อมูลตารางเวลาเดิน $route_name กรุณาติดต่อผู้ดูแลระบบ";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);
            redirect('sale/');
        }

        $route = $this->m_route->get_route(NULL, NULL, $rid)[0];
        $fare = $this->m_fares->get_fares($rcode, $vtid, $source_id, $destination_id)[0];
        $tickets_by_seller = $this->m_ticket->get_ticket_by_saller($schedules_id);
        $tickets = $this->m_ticket->get_ticket($date, $schedules_id);
        $tickets_today = $this->m_ticket->get_ticket($date);

        $data = array(
            'form' => $this->m_sale->set_form_sale($route, $s_station, $d_station, $schedule, $fare),
            'date' => $this->m_datetime->setDateThai($date),
            'route' => $route,
            'routes_seller' => $this->m_route->get_route_by_seller(),
            'routes_detail' => $this->m_route->get_route_detail_by_seller(),
            'stations' => $stations,
            's_station' => $s_station,
            'd_station' => $d_station,
            'schedules_id' => $schedules_id,
            'schedule' => $schedule,
            'schedules_detail' => $schedules_detail,
            'fare' => $fare,
            'tickets_by_seller' => $tickets_by_seller,
            'tickets' => $tickets,
            'tickets_today' => $tickets_today,
        );
        $data['vehicles_types'] = $this->m_route->get_vehicle_types();

        $data_debug = array(
//            'form' => $data['form'],
//            'route' => $data['route'],
//            'route_detail' => $data['route_detail'],
//            'form_route' => $data['form_route'],
//            's_station' => $data['s_station'],
//            'd_station' => $data['d_station'],
//            'schedule' => $data['schedule'],
//            'schedules_detail' => $data['schedules_detail'],
//            'schedules_id' => $data['schedules_id'],
//            'fare' => $data['fare'],
//            'parameter' => "vtid = $vtid | source_id = $source_id |  destination_id = $destination_id | schedules_id = $schedules_id",
//            'post' => $this->input->post(),
//            'session' => $this->session->userdata('EID'),
//            'tickets_by_seller' => $data['tickets_by_seller'],
//            'tickets' => $data['tickets'],
//            'tickets_today' => $data['tickets_today'],
//            'paramiter' => "$date, $rcode, $vtid, $rid, $schedules_id",
        );

        if ($this->m_sale->validate_form_sale() && $this->form_validation->run() == TRUE) {
            $tickets = $this->m_sale->get_post_form_sale();
//            $data_debug['data_form_sale'] = $tickets;
            $data_debug['update_resever_ticket'] = $this->m_ticket->update_resever_ticket($tickets);
            redirect("sale/print_ticket/$rid/$source_id/$destination_id/$schedules_id");
        }

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ขายตั๋ว ' . $route_name);
        $this->m_template->set_Content('sale/frm_booking', $data);
        $this->m_template->showSaleTemplate();
    }

    public function print_ticket($rid = NULL, $source_id = NULL, $destination_id = NULL, $tsid = NULL) {
        //ตรวจสอบสถานะตั๋ว 
        $this->m_ticket->check_ticket();

        if ($rid == null || $source_id == NULL || $destination_id == NULL || $tsid == NULL) {
            $alert['alert_message'] = "เลือกข้อมูลรอบเวลา";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);

            redirect('sale/');
        }

        $date = $this->m_datetime->getDateToday();
        $eid = $this->m_user->get_user_id();

        $tickets = $this->m_ticket->get_ticket($date, $tsid, 2, $eid);


        if (count($tickets) <= 0) {
            $alert['alert_message'] = "หมดเวลาทำรายการ";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect("sale/booking/$rid/$source_id/$destination_id/$tsid");
        }
        
        $route = $this->m_route->get_route(NULL, NULL, $rid);
        $data = array(
            'tsid' => $tsid,
            'tickets' => $tickets,
            'route' => $route[0],
        );
        $data_debug = array(
//            'tickets' => $data['tickets'],
//            'route' => $data['route'],
//            'data_post' => $this->input->post(),
//            '' => $data[''],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์บัตรโดยสาร');
        $this->m_template->set_Content('sale/frm_print', $data);
        $this->m_template->showSaleTemplate();
    }

    public function booking_seat() {
        $tsid = $this->input->post('TSID');
        $seat = $this->input->post("Seat");
        $vcode = $this->input->post("VCode");
        $source_id = $this->input->post("SourceID");
        $source_name = $this->input->post("SourceName");
        $destination_id = $this->input->post("DestinationID");
        $destination_name = $this->input->post("DestinationName");
        $price_seat = $this->input->post("PriceSeat");
        $price_dicount = $this->input->post('PriceDicount');

        if ($price_seat == $price_dicount) {
            $IsDiscount = 1;
        } else {
            $IsDiscount = 0;
        }

        $ticket_data = array(
            'TSID' => $tsid,
            'Seat' => $seat,
            'VCode' => $vcode,
            'SourceID' => $source_id,
            'SourceName' => $source_name,
            'DestinationID' => $destination_id,
            'DestinationName' => $destination_name,
            'DateSale' => $this->m_datetime->getDateToday(),
            'PriceSeat' => $price_seat,
            'IsDiscount' => $IsDiscount,
        );
        $ticket_id = $this->m_ticket->resever_ticket($ticket_data);
        if ($ticket_id != NULL || $ticket_id != '') {
            $rs = true;
        } else {
            $rs = FALSE;
        }

        echo json_encode($ticket_id);
    }

    public function cancle_seat() {
        $tsid = $this->input->post('TSID');
        $seat = $this->input->post("Seat");
        $vcode = $this->input->post("VCode");
        $source_id = $this->input->post("SourceID");
        $destination_id = $this->input->post("DestinationID");
        $rs = $this->m_ticket->delete_ticket($tsid, $seat);
        if ($rs) {
            echo json_encode('true');
        } else {
            echo json_encode('false');
        }
    }

    public function sale_seat() {
        $ticket_id = $this->input->post('TicketID');
        $rs = $this->m_ticket->sale_ticket($ticket_id);

        if ($rs) {
            echo json_encode('true');
        } else {
            echo json_encode('false');
        }
    }

}
