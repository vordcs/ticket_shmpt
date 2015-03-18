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
            'data' => $this->m_sale->set_form_search(),
        );

        $data_debug = array(
//            'from_search' => $data['from_search'],
//            'routes' => $data['routes'],
//            'routes_detail' => $data['routes_detail'],
//            'stations' => $data['stations'],
//            'eid' => $this->session->userdata('EID'),
//            'data' => $data['data'],
        );
        $this->m_template->set_Debug($data_debug);

        $this->m_template->set_Title('ระบบขายตั๋วหน้าเคาน์เตอร์ ');
        $this->m_template->set_Content('sale/frm_search', $data);
        $this->m_template->showTemplate();
    }

    public function booking($rid = NULL, $source_id = NULL, $destination_id = NULL, $schedules_id = NULL) {
        $date = $this->m_datetime->getDateToday();

        $this->m_ticket->check_ticket($schedules_id);

        if ($rid == NULL || $source_id == NULL) {
            $alert['alert_message'] = "เลือกข้อมูลรอบเวลา";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('sale/');
        }

        $schedules = $this->m_schedule->get_schedule($date, NULL, NULL, $rid, $schedules_id);

        if (count($schedules) <= 0) {
            redirect("sale/");
        }

        $set_data = $this->m_sale->set_form_booking($rid, $source_id, $destination_id, $schedules_id);

        $data = array(
            'form' => $set_data['form'],
            'RID' => $rid,
            'SourceID' => $source_id,
            'DestinationID' => $destination_id,
            'TSID' => $schedules_id,
            'routes_seller' => $set_data['routes_seller'],
            'schedules' => $set_data['schedules'],
            'schedule_select' => $set_data['schedule_select'],
        );

        if ($this->m_sale->validate_form_sale() && $this->form_validation->run() == TRUE) {
            $tickets = $this->m_sale->get_post_form_booking();
//            $data_debug['data_form_sale'] = $tickets;
            $data_debug['update_resever_ticket'] = $this->m_ticket->update_resever_ticket($tickets);
            redirect("sale/print_ticket/$rid/$source_id/$destination_id/$schedules_id");
        }

        $data_debug = array(
//            'parameter' => "RID = $rid || Source = $source_id || Destination = $destination_id || TSID = $schedules_id",
//            'form' => $data['form'],
//            'routes_seller'=>$data['routes_seller'],
//            'schedules'=>$data['schedules'],
//            'schedule_select' => $data['schedule_select'],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ขายตั๋วโดยสาร ');
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
            'previous_page' => "sale/booking/$rid/$source_id/$destination_id/$tsid",
            'next_page' => '',
            'rid' => $route[0]['RID'],
            'source_id' => $source_id,
            'destination_id' => $destination_id,
            'tsid' => $tsid,
            'tickets' => $tickets,
            'route' => $route[0],
            'data' => $this->m_sale->set_form_print($date, $rid, $tsid),
        );
        $data_debug = array(
//            'tickets' => $data['tickets'],
//            'route' => $data['route'],
//            'data_post' => $this->input->post(),
//            'data' => $data['data'],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์บัตรโดยสาร');
        $this->m_template->set_Content('sale/frm_print', $data);
        $this->m_template->showSaleTemplate();
    }

    public function checkin($RID, $SourceID, $DestinationID, $TSID, $CheckInID = NULL) {

        if ($CheckInID == NULL) {
            $data_checkin_add = $this->m_checkin->get_post_form_add($TSID, $SourceID);
            $rs = $this->m_checkin->insert_checkin($data_checkin_add);
        } else {
            $data_checkin_edit = $this->m_checkin->get_post_form_edit();
            $rs = $this->m_checkin->update_checkin($CheckInID, $data_checkin_edit);
        }



        redirect("sale/booking/$RID/$SourceID/$DestinationID/$TSID");
    }

    public function print_log($RID, $SourceID, $DestinationID, $TSID) {

        $data = array(
            'page_title' => "พิมพ์ใบล็อก",
            'page_title_small' => "",
            'previous_page' => "sale/booking/$RID/$SourceID/$DestinationID/$TSID",
            'next_page' => '',
            'data' => $this->m_sale->set_form_print_log($TSID, $SourceID),
        );

        $data_debug = array(
//            'data' => $data['data'],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์ใบล๊อก');
        $this->m_template->set_Content('sale/frm_print_log', $data);
        $this->m_template->showSaleTemplate();
    }

    /*
     * for ajax
     */

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
        $TimeDepart = $this->input->post('TimeDepart');

        $this->m_ticket->check_ticket($tsid);

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
            'TimeDepart' => $TimeDepart,
            'DateSale' => $this->m_datetime->getDateToday(),
            'PriceSeat' => $price_seat,
            'IsDiscount' => $IsDiscount,
            'Seller' => $this->m_user->get_user_id(),
        );

        $check_ticket_id = $this->m_ticket->get_TicketID($tsid, $seat, $source_id);

        if ($check_ticket_id == NULL && $seat <= 99) {
            $ticket_id = $this->m_ticket->resever_ticket($ticket_data);
            echo json_encode(1);
        } else {

            $ticket_id = NULL;
            echo json_encode(0);
        }
    }

    public function cancle_seat() {
        $tsid = $this->input->post('TSID');
        $seat = $this->input->post("Seat");
        $source_id = $this->input->post("SourceID");
        $rs = $this->m_ticket->delete_ticket($tsid, $seat, $source_id);
        if ($rs) {
            echo json_encode(1);
        } else {
            echo json_encode(0);
        }
    }

    public function sale_seat() {
        $ticket_id = $this->input->post('TicketID');
        $rs = $this->m_ticket->sale_ticket($ticket_id);

        if ($rs) {
            echo json_encode(1);
        } else {
            echo json_encode(0);
        }
    }

    public function check_seat_plan() {
        $rs = FALSE;

        if ($rs) {
            echo json_encode('true');
        } else {
            echo json_encode('false');
        }
    }

}
