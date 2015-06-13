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
        $this->load->model('m_cost');
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

        $schedule = reset($schedules);

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
            'data_parcel_post' => $set_data['data_parcel_post'],
            'data_parcel_post_in' => $set_data['data_parcel_post_in'],
        );


        $form_data = array();
        $rs = array();
        if ($this->m_sale->validate_form_sale() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_sale->get_post_booking();
            $rs = $this->m_ticket->update_price_tickes($form_data);
            redirect("sale/print_ticket/$rid/$source_id/$destination_id/$schedules_id");
        }

        $data_debug = array(
//            'parameter' => "RID = $rid || Source = $source_id || Destination = $destination_id || TSID = $schedules_id",
//            'form_data' => $form_data,
//            'rs' => $rs,
//            'all_post' => $this->input->post(),
//            'form' => $data['form'],
//            'routes_seller'=>$data['routes_seller'],
//            'schedules'=>$data['schedules'],
//            'schedule_select' => $data['schedule_select'],
//            'data_parcel_post' => $data['data_parcel_post'],
//            'data_parcel_post_in' => $data['data_parcel_post_in'],
//            'schedules'=>  reset($schedules),
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ขายตั๋วโดยสาร ');
        if ($schedule['VTID'] == 1) {
            $this->m_template->set_Content('sale/frm_booking_van', $data);
        } else {
            $this->m_template->set_Content('sale/frm_booking_bus', $data);
        }
        $this->m_template->showSaleTemplate();
    }

    public function print_ticket($rid = NULL, $source_id = NULL, $destination_id = NULL, $tsid = NULL) {

        if ($rid == null || $source_id == NULL || $destination_id == NULL || $tsid == NULL) {
            $alert['alert_message'] = "เลือกข้อมูลรอบเวลา";
            $alert['alert_mode'] = "danger";
            $this->session->set_flashdata('alert', $alert);
            redirect('sale/');
        }
        //ตรวจสอบสถานะตั๋ว 
        $num_ticket_delete = $this->m_ticket->check_ticket($tsid);

        $date = $this->m_datetime->getDateToday();
        $eid = $this->m_user->get_user_id();

        $tickets_booking = $this->m_ticket->get_ticket($date, $tsid, 2, $eid);
        $rs = array();
        foreach ($tickets_booking as $ticket) {
            $TicketID = $ticket['TicketID'];
            $result = $this->m_ticket->sale_ticket($TicketID);
            $rs[$TicketID] = $result;
        }
        $tickets_sale_no_print = $this->m_ticket->get_ticket($date, $tsid, 1, $eid, '0');
        $tickets = array();
        if (count($tickets_sale_no_print) > 0) {
            $tickets = $tickets_sale_no_print;
        }

        if (count($tickets) <= 0 && $num_ticket_delete > 0) {
            $alert['alert_message'] = "หมดเวลาทำรายการ";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);
            redirect("sale/booking/$rid/$source_id/$destination_id/$tsid");
        }
        if (count($tickets) <= 0) {
            $alert['alert_message'] = "หมดเวลาทำรายการ กรุณาเลือกที่นั่ง อีกครั้ง";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect("sale/booking/$rid/$source_id/$destination_id/$tsid");
        }

        $form_data = array();

        if ($this->m_sale->validate_form_print_ticket() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_sale->get_post_form_print_ticket();
            $rs = $this->m_ticket->print_tickets($form_data);

            if (count($tickets) == count($form_data)) {

                $alert['alert_message'] = "พิมพ์ตั๋วโดยสารสำเร็จ";
                $alert['alert_mode'] = "success";

                redirect("sale/booking/$rid/$source_id/$destination_id/$tsid");
            } else {
                redirect("sale/print_ticket/$rid/$source_id/$destination_id/$tsid");
            }
        }

        $data = array(
            'previous_page' => "sale/booking/$rid/$source_id/$destination_id/$tsid",
            'next_page' => '',
            'data' => $this->m_sale->set_form_print($date, $rid, $source_id, $destination_id, $tsid, $tickets),
        );
        $data_debug = array(
//            'tickets' => $tickets,
//            'route' => $data['route'],            
//            'form_data' => $form_data,
//            'rs' => $rs,
//            'num_form_data' => count($form_data),
//            'num_ticket' => count($tickets),
//            'data' => $data['data'],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์บัตรโดยสาร');
        $this->m_template->set_Content('sale/frm_print', $data);
        $this->m_template->showSaleTemplate();
    }

    public function parcel($RID, $SourceID, $DestinationID, $TSID) {

        $form_data = array();
        $rs = array();
        $rs_parcel = array();
        if ($this->m_cost->validate_form_parcel() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_cost->get_post_form_parcel();
            $rs = reset($this->m_cost->insert_cost($form_data));
            $CostID = $rs['CostID'];
            $data_parcel_post = $form_data['data_parcel_post'];
            $rs_parcel = $this->m_cost->insert_parcel_post($CostID, $data_parcel_post);
            if ($rs_parcel) {
                redirect("sale/print_recept/$RID/$SourceID/$DestinationID/$TSID/$CostID");
            }
        }
        $data = array(
            'page_title' => "เพิ่มพัสดุ",
            'page_title_small' => "",
            'previous_page' => "sale/booking/$RID/$SourceID/$DestinationID/$TSID",
            'next_page' => '',
            'form_parcel' => $this->m_cost->set_form_parcel($RID, $SourceID, $DestinationID, $TSID),
        );
        $data_debug = array(
//            'form_parcel' => $data['form_parcel'],
//            'form_data' => $form_data,
//            'rs' => $rs,
//            'rs_parcel' => $rs_parcel,
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('เพิ่มพัสดุ');
        $this->m_template->set_Content('sale/frm_parcel', $data);
        $this->m_template->showSaleTemplate();
    }

    public function print_recept($RID, $SourceID, $DestinationID, $TSID, $CostID) {

        $data = array(
            'page_title' => "พิมพ์ใบเสร็จ",
            'page_title_small' => "",
            'previous_page' => "sale/booking/$RID/$SourceID/$DestinationID/$TSID",
            'next_page' => '',
            'data' => $this->m_sale->set_form_print_parcel($RID, $TSID, $CostID),
        );
        $data_debug = array(
//            'data' => $data['data'],
        );

        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('พิมพ์ใบเสร็จ');
        $this->m_template->set_Content('sale/frm_print_parcel', $data);
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
            'data_parcel_post' => $this->m_cost->get_parcel_post_report($SourceID, $TSID),
        );

        $data_debug = array(
//            'data' => $data['data'],
//            'data_parcel_post' => $data['data_parcel_post'],
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
        $source_id = $this->input->post("SourceID");
        $destination_id = $this->input->post("DestinationID");
        $TimeDepart = $this->input->post('TimeDepart');
        $TimeArrive = $this->input->post('TimeArrive');

        /* data station source */
        $station_source = reset($this->m_station->get_stations(NULL, NULL, $source_id));
        $SourceName = $station_source['StationName'];

        /* data station destination */
        $station_destination = reset($this->m_station->get_stations(NULL, NULL, $destination_id));
        $DestinationName = $station_destination['StationName'];

        /* data schedule */
        $schedule = reset($this->m_schedule->get_schedule(NULL, NULL, NULL, NULL, $tsid));
        $RCode = $schedule['RCode'];
        $VTID = $schedule['VTID'];
        $RID = $schedule['RID'];
        $VID = $schedule['VID'];
        $VCode = $schedule['VCode'];

        /* data fares */
        $fares = reset($this->m_fares->get_fares($RCode, $VTID, $source_id, $destination_id));
        $Price = $fares['Price'];
        $PriceDicount = $fares['PriceDicount'];
        $price_seat = $fares['Price'];
        $IsDiscount = 0;

        $ticket_data = array(
            'RID' => $RID,
            'TSID' => $tsid,
            'Seat' => $seat,
            'VID' => $VID,
            'VCode' => $VCode,
            'SourceID' => $source_id,
            'SourceName' => $SourceName,
            'DestinationID' => $destination_id,
            'DestinationName' => $DestinationName,
            'TimeDepart' => $TimeDepart,
            'TimeArrive' => $TimeArrive,
            'DateSale' => $this->m_datetime->getDateToday(),
            'PriceSeat' => $price_seat,
            'IsDiscount' => $IsDiscount,
            'Seller' => $this->m_user->get_user_id(),
        );

        $check_ticket_id = $this->m_ticket->get_TicketID($tsid, $seat, $source_id);

        if ($check_ticket_id == NULL && $seat <= 99) {
            $ticket_id = $this->m_ticket->resever_ticket($ticket_data);
            $dropdown = "id = \"FareType \" " . 'class="form-control fare" onchange="calTotalFare()"';
            if ($Price == $PriceDicount) {
                $i_fares[$Price] = "$Price (เต็ม)";
            } else {
                $i_fares[$Price] = "$Price (เต็ม)";
                $i_fares[$PriceDicount] = "$PriceDicount (ลด)";
            }

            $i_ticket_id = array(
                'type' => 'hidden',
                'name' => 'TicketID[]',
                'value' => $ticket_id
            );
            $i_source_id = array(
                'type' => 'hidden',
                'name' => 'SourceID[]',
                'value' => $source_id
            );
            $i_destination_id = array(
                'type' => 'hidden',
                'name' => 'DestinationID[]',
                'value' => $destination_id
            );

            $rs = array(
                'TicketID' => form_input($i_ticket_id),
                'DestinationName' => $DestinationName,
                'Price' => form_dropdown('PriceSeat[]', $i_fares, set_value("PriceSeat[]"), $dropdown),
                'Source' => form_input($i_source_id),
                'Destination' => form_input($i_destination_id),
            );
            echo json_encode($rs);
        } else {
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

    public function get_time_arrive() {
        $TSID = $this->input->post("TSID");
        $RID = $this->input->post("RID");
        $SourceID = $this->input->post("SourceID");
        $DestinationID = $this->input->post("DestinationID");

        $time_arrive = $this->m_schedule->time_arrive($RID, $TSID, $SourceID, $DestinationID);


        echo json_encode($time_arrive);
    }

}
