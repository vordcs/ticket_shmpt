<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_sale extends CI_Model {

    //    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($tsid = NULL, $rid = NULL) {
        $this->db->select('*,t_schedules_day.TSID as TSID,t_schedules_day.RID as RID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');
        $this->db->join('t_schedules_day_has_report', ' t_schedules_day.TSID = t_schedules_day_has_report.TSID', 'left');

        if ($tsid != NULL) {
            $this->db->where('t_schedules_day.TSID', $tsid);
        }

        if ($rid != NULL) {
            $this->db->where('t_schedules_day.RID', $rid);
        }

        $this->db->where('t_schedules_day.ScheduleStatus', '1');

        $this->db->order_by('t_schedules_day.TimeDepart', 'asc');
        $query_schedule = $this->db->get("t_schedules_day");
        return $query_schedule->result_array();
    }

    public function set_form_search_route($rcode = NULL, $vtid = NULL) {
        
    }

    public function set_form_booking($rid, $s_station, $d_station, $tsid = NULL) {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');

        $date = $this->m_datetime->getDateToday();
        $route = $this->m_route->get_route_by_seller(NULL, NULL, $rid)[0];

        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $route_name = "$vt_name  $rcode  $source - $desination";

        $seller_station_id = $route['SID'];


        $stations = $this->m_station->get_stations($rcode, $vtid);
        $num_station = count($stations);

        $source_id = $s_station['SID'];
        $source_name = $s_station['StationName'];
        $source_seq = $s_station['Seq'];
        $source_travel_time = $s_station['TravelTime'];

        $destination_id = $d_station['SID'];
        $destination_name = $d_station['StationName'];
        $destination_seq = $d_station['Seq'];
        $destination_travel_time = $d_station['TravelTime'];

        /*
         * ตารางเวลาเดินรถ
         */
        if ($tsid != NULL) {
            $schedule = $this->get_schedule($tsid)[0];
        } else {
            $schedule = array();
        }
        /*
         * ค่าโดยสาร
         */
        $price = 0;
        $price_dis = 0;
        $fare = $this->m_fares->get_fares($rcode, $vtid, $source_id, $destination_id)[0];
        if (count($fare) > 0 && $fare != NULL) {
            $price = $fare['Price'];
            $price_dis = $fare['PriceDicount'];
        }

//        รหัสตารางเวลาเดินรถ
//        เวลาออกจาก $source_id
        $time_depart = "";
//        เวลาถึง $destination_id
        $time_arrive = "-";
//        วันเดินทาง

        $date_th = '';
//        รถโดยสาร
        $vid = '-';
        $vcode = '-';

        if (count($schedule) > 0 && $schedule != NULL) {
//            รหัสตารางเวลาเดินรถ
            $tsid = $schedule['TSID'];
            $vid = $schedule['VID'];
            $vcode = $schedule['VCode'];
//            เวลาเริ่มต้นของการเดินทาง
            $start_time = $schedule["TimeDepart"];
            if (($source_seq == '1' || $source_seq == $num_station)) {
                $time_depart = date('H:i', strtotime($schedule["TimeDepart"]));
            } else {
                $time_depart = date('H:i', strtotime("+$source_travel_time minutes", strtotime($start_time)));
            }
            if (($source_seq != '1' || $source_seq != $num_station)) {
                if ($destination_travel_time != '0') {
                    $time_arrive = date('H:i', strtotime("+$destination_travel_time minutes", strtotime($start_time)));
                }
                $date = $schedule['Date']; //getDateThaiString();
                $date_th = $this->m_datetime->getDateThaiString($schedule['Date']);
            }
        }


        $i_route_name = array(
            'type' => "text",
            'name' => "route_name",
            'id' => "route_name",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $route_name,
        );
        $i_RID = array(
            'type' => "hidden",
            'name' => "RID",
            'id' => "RID",
            'class' => "from-control",
            'readonly' => "TRUE",
            'value' => $rid,
        );
        $i_VID = array(
            'type' => "hidden",
            'name' => "VID",
            'id' => "VID ",
            'class' => "from-control",
            'readonly' => "TRUE",
            'value' => $vid,
        );
        $i_VTID = array(
            'type' => "hidden",
            'name' => "VTID",
            'id' => "VTID",
            'class' => "from-control",
            'readonly' => "TRUE",
            'value' => $vtid,
        );
        $i_TSID = array(
            'type' => "hidden",
            'name' => "TSID",
            'id' => "TSID",
            'class' => "from-control",
            'readonly' => "",
            'value' => $tsid,
        );
        $i_SourceID = array(
            'type' => "hidden",
            'name' => "SourceID",
            'id' => "SourceID",
            'class' => "from-control",
            'readonly' => "",
            'value' => $source_id,
        );
        $i_SourceName = array(
            'type' => "text",
            'name' => "SourceName",
            'id' => "SourceName",
            'class' => "form-control  text-center",
            'readonly' => "TRUE",
            'value' => $source_name,
        );
        $i_DestinationID = array(
            'type' => "hidden",
            'name' => "DestinationID",
            'id' => "DestinationID",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $destination_id,
        );
        $i_DestinationName = array(
            'type' => "text",
            'name' => "DestinationName",
            'id' => "DestinationName",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $destination_name,
        );

        $i_TimeDepart = array(
            'type' => "text",
            'name' => "TimeDepart",
            'id' => "TimeDepart",
            'class' => "form-control text-center",
            'readonly' => "TRUE",
            'value' => $time_depart,
        );
        $i_TimeArrive = array(
            'type' => "text",
            'name' => "TimeArrive",
            'id' => "TimeArrive",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $time_arrive,
        );
        $i_Date = array(
            'type' => "hidden",
            'name' => "Date",
            'id' => "Date",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $date,
        );
        $i_DateTH = array(
            'type' => "text",
            'name' => "DateTH",
            'id' => "DateTH",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $date_th,
        );
        $i_Price = array(
            'type' => "text",
            'name' => "Price",
            'id' => "Price",
            'class' => "form-control  text-center",
            'readonly' => "",
            'value' => $price,
        );
        $i_PriceDicount = array(
            'type' => "text",
            'name' => "PriceDicount",
            'id' => "PriceDicount",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $price_dis,
        );
        $i_VCode = array(
            'type' => "text",
            'name' => "VCode",
            'id' => "VCode",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $vcode,
        );

        $form_sale_ticket = array(
            'form' => form_open("sale/booking/$rid/$source_id/$destination_id/$tsid", array('class' => 'form', 'id' => 'form_sale')),
            'route_name' => form_input($i_route_name),
            'RID' => form_input($i_RID),
            'VID' => form_input($i_VID),
            'VTID' => form_input($i_VTID),
            'TSID' => form_input($i_TSID),
            'SourceID' => form_input($i_SourceID),
            'SourceName' => form_input($i_SourceName),
            'DestinationID' => form_input($i_DestinationID),
            'DestinationName' => form_input($i_DestinationName),
            'TimeDepart' => form_input($i_TimeDepart),
            'TimeArrive' => form_input($i_TimeArrive),
            'Date' => form_input($i_Date),
            'DateTH' => form_input($i_DateTH),
            'Price' => form_input($i_Price),
            'PriceDicount' => form_input($i_PriceDicount),
            'VCode' => form_input($i_VCode),
            'Seat' => "",
        );


        $data = $this->set_data_form_booking($date, $rid, $source_id, $destination_id, $tsid);

        $rs = array(
            'form' => $form_sale_ticket,
            'routes_seller' => $data['routes_seller'],
            'schedules' => $data['schedules'],
            'schedule_select' => $data['schedule_select'],
        );
        return $rs;
    }

    public function set_data_form_booking($date, $RID, $SourceID, $DestinationID, $TSID = NULL) {

        $EID = $this->m_user->get_user_id();
        /*
         * set infomation route seller permission
         */

        $routes = $this->m_route->get_route_by_seller();
        $data_routes_seller = array();
        foreach ($routes as $route) {
            $rcode = $route['RCode'];
            $vtid = $route['VTID'];
            $vt_name = $route['VTDescription'];
            $source = $route['RSource'];
            $destination = $route['RDestination'];
            $route_name = "$vt_name " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;


            $seller_station_id = $route['SID'];
            $seller_station_name = $route['StationName'];
            $seller_station_seq = $route['Seq'];

            if ($route['SellerNote'] != NULL) {
                $note = $route['SellerNote'];
                $seller_station_name .= " ($note) ";
            }
            /*
             * ตรวจสอบข้อมูลพนักงานขายตั๋ว 
             * ว่าเป็นจุดเริ่มต้นหรือว่าสุดท้าย
             * ถ้าเป็นจุดต้นทาง ให้แสดง เฉพาะ S
             * ถ้าเป็นจุดปลายทาง ให้แสดง เฉพาะ D
             */

            $stations = $this->m_station->get_stations($rcode, $vtid);
            $num_station = count($stations);
            foreach ($stations as $station) {
                if ($seller_station_id == $station['SID']) {
                    $seller_station_seq = $station['Seq'];
                }
            }

            $route_detail = $this->m_route->get_route_detail_by_seller($rcode, $vtid);
            $detail_in_routes = array();
            foreach ($route_detail as $rd) {
                $rid = $rd['RID'];
                $start_point = $rd['StartPoint'];
                $stations = $this->m_station->get_stations_by_start_point($start_point, $rcode, $vtid, $seller_station_seq);

                $temp_detail_in_routes = array(
                    'RID' => $rid,
                    'SourceName' => $rd['RSource'],
                    'DestinationName' => $rd['RDestination'],
                    'stations' => $stations,
                );
                array_push($detail_in_routes, $temp_detail_in_routes);
            }

            if ($seller_station_seq == 1) {
                $start_point = 'S';
                array_pop($detail_in_routes);
            } elseif ($seller_station_seq == $num_station) {
                $start_point = 'D';
                array_shift($detail_in_routes);
            } else {
                $start_point = 'กลางทาง';
            }
            $temp_route = array(
                'RCode' => $rcode,
                'VTID' => $vtid,
                'RouteName' => $route_name,
                'seller_station_id' => $seller_station_id,
                'seller_station_seq' => $seller_station_seq,
                'seller_station_name' => $seller_station_name,
                'start_point' => $start_point,
                'routes_deatil' => $detail_in_routes,
            );
            array_push($data_routes_seller, $temp_route);
        }


        /*
         * ข้อมูลเสนทาง
         */
        $route = $this->m_route->get_route_by_seller(NULL, NULL, $RID)[0];

        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $start_point = $route['StartPoint'];
        $route_name = "$vt_name  $rcode  $source - $desination";

        $seller_station_id = $route['SID'];
        $seller_station_seq = $route['Seq'];

        /*
         * ข้อมูลสถานีปลายทาง
         */
        $StationDestination = $this->m_station->get_stations($rcode, $vtid, $DestinationID)[0];
        $DestinationSeq = $StationDestination['Seq'];
        /*
         * ข้อมูลตารางเวลาเดินรถ
         */

        $schedules_in_route = array();
        $schedule_select = array(
            'NumberSeat' => 0,
            'NumberTicketsExtra' => 0,
        );
        $schedules = $this->m_schedule->get_schedule($date, $rcode, $vtid, $RID);

        foreach ($schedules as $schedule) {
            $TSID_schedule = $schedule['TSID'];
            $RID_schedule = $schedule['RID'];
            $Date_schedule = $schedule['Date'];
            $TimeDepart = $this->m_schedule->get_time_depart($Date_schedule, $RID, $TSID_schedule, $SourceID)[0]['TimeDepart'];

            $ticket_book = $this->get_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq);
            $ticket_book_seller = $this->get_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq, $EID);

            $ticket_sale = $this->get_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq);
            $ticket_sale_seller = $this->get_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq, $EID);

            $NumberSeat = $schedule['VSeat'];
            $NumberSeatBook = count($ticket_book);
            $NumberSeatSale = count($ticket_sale);

            $NumberSeatBookBySeller = count($ticket_book_seller);
            $NumberSeatSaleBySeller = count($ticket_sale_seller);



            $TicketsExtra = $this->get_ticket($start_point, $TSID_schedule, NULL, $seller_station_seq, $DestinationSeq, NULL, $NumberSeat);

            $NumberTicketsExtra = 0;
            if ($NumberSeat <= $NumberSeatSale) {
                $NumberTicketsExtra = count($TicketsExtra);
            }


            $Tickets = $this->get_ticket($start_point, $TSID_schedule, NULL, $seller_station_seq);

            $fare = $this->m_fares->get_fares($rcode, $vtid, $seller_station_id, $DestinationID)[0];
            $i_Fare = array(
                $fare['PriceDicount'] => $fare['PriceDicount'] . ' (ลด) ',
                $fare['Price'] => $fare['Price'] . ' (เต็ม) ',
            );

            $Reports = array();

            if ($NumberSeatSaleBySeller > 0) {
                $Reports = $this->get_report_by_seller($TSID_schedule, $seller_station_id);
            }

            $NumberSeatTotal = array(
                'type' => "text",
                'name' => "NumberSeatTotal",
                'id' => "NumberSeatTotal ",
                'class' => "from-control",
                'readonly' => "TRUE",
                'value' => $NumberSeatSale + $NumberSeatBook,
            );

            $temp = array(
                'RID' => $schedule['RID'],
                'RouteName' => $route_name,
                'StartPoint' => $start_point,
                'SourceID' => $seller_station_id,
                'SourceSeq' => $seller_station_seq,
                'DestinationID' => $DestinationID,
                'DestinationSeq' => $DestinationSeq,
                'TSID' => $TSID_schedule,
                'TimeDepart' => $TimeDepart,
                'Date' => $Date_schedule,
                'VID' => $schedule['VID'],
                'VCode' => $schedule['VCode'],
                'VTID' => $schedule['VTID'],
                'Fare' => $i_Fare,
                'NumberSeat' => $NumberSeat,
                'NumberSeatBook' => $NumberSeatBook,
                'NumberSeatSale' => $NumberSeatSale,
                'NumberTicketsExtra' => $NumberTicketsExtra,
                'NumberSeatBookBySeller' => $NumberSeatBookBySeller,
                'NumberSeatSaleBySeller' => $NumberSeatSaleBySeller,
                'NumberSeatTotal' => form_input($NumberSeatTotal),
                'TicketsBook' => $ticket_book_seller,
                'TicketsSale' => array(),
                'TicketsExtra' => $TicketsExtra,
                'ScheduleReport' => $Reports,
                'Tickets' => $Tickets,
            );
            if ($TSID != NULL && $TSID == $TSID_schedule) {
                $schedule_select = $temp;
            }
            array_push($schedules_in_route, $temp);
        }
        $rs = array(
            'routes_seller' => $data_routes_seller,
            'schedules' => $schedules_in_route,
            'schedule_select' => $schedule_select,
        );
        return $rs;
    }

    public function get_post_form_booking() {

        $route_name = $this->input->post('route_name');
        $RID = $this->input->post('RID');
        $VID = $this->input->post('VID');
        $VCode = $this->input->post('VCode');
        $TSID = $this->input->post('TSID');
        $SourceID = $this->input->post('SourceID');
        $SourceName = $this->input->post('SourceName');
        $DestinationID = $this->input->post('DestinationID');
        $DestinationName = $this->input->post('DestinationName');
        $TimeDepart = $this->input->post('TimeDepart');
        $TimeArrive = $this->input->post('TimeArrive');

        $Date = $this->m_datetime->getDateToday(); //$this->$this->input->post('Date');
        $Price = $this->input->post('Price');
        $PriceDicount = $this->input->post('PriceDicount');

        $Seat = $this->input->post('Seat');
        $PriceSeat = $this->input->post('PriceSeat');
        $IsDiscount = array();
//        0=ราคาเต็ม ,1 =ราคาลด
        for ($i = 0; $i < count($PriceSeat); $i++) {
            if ($PriceSeat[$i] == $Price) {
                $IsDiscount[$i] = 0;
            } else {
                $IsDiscount[$i] = 1;
            }
        }
        $note = "ตั๋วโดยสารเดินทางจาก $SourceName ไป $DestinationName เส้นทาง $route_name เวลาออก $TimeDepart เวลาถึง $TimeArrive ";
        $tickets = array();

        for ($i = 0; $i < count($Seat); $i++) {
            $temp_ticket = array(
                'TSID' => $TSID,
                'RID' => $RID,
                'VID' => $VID,
                'VCode' => $VCode,
                'SourceID' => $SourceID,
                'SourceName' => $SourceName,
                'DestinationID' => $DestinationID,
                'DestinationName' => $DestinationName,
                'TimeDepart' => $TimeDepart,
                'TimeArrive' => $TimeArrive,
                'DateSale' => $Date,
                'Seat' => $Seat[$i],
                'PriceSeat' => $PriceSeat[$i],
                'IsDiscount' => $IsDiscount[$i],
                'Seller' => $this->session->userdata('EID'),
//                'StatusSeat' => 1,                'TicketSaleNote' => $note,
            );
            array_push($tickets, $temp_ticket);
        }

//        $form_data = array(
//            'TSID' => $TSID,
//            'RID' => $RID,
//            'VID' => $VID,
//            'VCode' => $VCode,
//            'SourceID' => $SourceID,
//            'SourceName' => $SourceName,
//            'DestinationID' => $DestinationID,
//            'DestinationName' => $DestinationName,
//            'TimeDepart' => $TimeDepart,
//            'TimeArrive' => $TimeArrive,
//            'DateSale' => $Date,
//            'PriceDicount' => $PriceDicount,
//            'Seat' => $Seat,
//            'PriceSeat' => $PriceSeat,
//            'IsDiscount' => $IsDiscount,
//        );
        return $tickets;
    }

    public function set_form_print($date, $rid, $tsid) {
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');

        $rs = array();
        $eid = $this->m_user->get_user_id();

        $route = $this->m_route->get_route(NULL, NULL, $rid)[0];

        $tickets = $this->m_ticket->get_ticket($date, $tsid, 2, $eid);

        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $route_name = "$vt_name $rcode " . $route['RSource'] . '-' . $route['RDestination'];

        $note = '&nbsp;';

        foreach ($tickets as $ticket) {

            $ticket_id = $ticket['TicketID'];
            $source_name = $ticket['SourceName'];
            $destination_name = $ticket['DestinationName'];
            $vcode = $ticket['VCode'];
            $seat = $ticket['Seat'];
            $time_depart = date('H:i', strtotime($ticket['TimeDepart']));
            $time_arrive = date('H:i', strtotime($ticket['TimeArrive']));
            $date = $this->m_datetime->getDateThaiString($ticket['DateSale']);
            $price = $ticket['PriceSeat'];
            $barcode = $this->m_barcode->gen_barcode($ticket_id);
            $qrcode = $this->m_qrcode->gen_qrcode($ticket_id);
            $name_seller = $this->m_user->get_user_first_name();

            if ($time_arrive == '00:00') {
                $time_arrive = '-';
            }
            if ($vtid == '1') {
                $note = '** รถเต็มออกก่อนเวลา **';
                $time_depart.='*';
            }

            $temp_ticket = array(
                'TicketID' => $ticket_id,
                'RCode' => $rcode,
                'VTID' => $vtid,
                'VTName' => $vt_name,
                'VCode' => $vcode,
                'Seat' => $seat,
                'TimeDepart' => $time_depart,
                'TimeArrive' => $time_arrive,
                'Date' => $date,
                'RouteName' => $route_name,
                'SourceName' => $source_name,
                'DestinationName' => $destination_name,
                'Price' => $price,
                'BarCode' => $barcode,
                'QrCode' => $qrcode,
                'Note' => $note,
                'DateSale' => $this->m_datetime->getDatetimeNow(),
                'SellerName' => $name_seller,
            );
            array_push($rs, $temp_ticket);
        }

        return $rs;
    }

    public function get_ticket($StartPoint, $TSID, $StatusSeat = NULL, $SourceSeq = NULL, $DestinationSeq = NULL, $EID = NULL, $Seat = NULL) {

        $this->db->select('TSID,TicketID,Seat,StatusSeat,SourceName,DestinationName,PriceSeat,Seller,t_stations.Seq as DestinationSeq,IsDiscount');
        $this->db->join('t_stations', 't_stations.SID = ticket_sale.DestinationID', 'left');
        $this->db->where('TSID', $TSID);

        if ($StatusSeat != NULL) {
            $this->db->where('StatusSeat', $StatusSeat);
        }
        if ($StartPoint == 'S' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq >=', $SourceSeq);
        }
        if ($StartPoint == 'D' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq <=', $SourceSeq);
        }
        if ($EID != NULL) {
            $this->db->where('Seller', $EID);
        }

        if ($Seat != NULL) {
            $this->db->where('Seat >', $Seat);
        }

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_report_by_seller($TSID, $SourceID) {
        $this->db->select('SourceID,SourceName,DestinationID,DestinationName,PriceSeat,COUNT(TicketID) as NumberTicket,SUM(PriceSeat) as Total');
        $this->db->where('SourceID', $SourceID);
        $this->db->where('TSID', $TSID);
        $this->db->where('StatusSeat', 1);
        $this->db->where('Seller', $this->m_user->get_user_id());
        $this->db->group_by('SourceID,DestinationID');
        $query = $this->db->get('ticket_sale');
        return $query->result_array();
    }

    public function validate_form_sale() {
        $Seat = $this->input->post('Seat');
        if (count($Seat) <= 0 || $Seat == NULL) {
            return FALSE;
        }
        $this->form_validation->set_rules("Seat[]", "เวลาเดินทาง", 'trim|required|xss_clean');

        return TRUE;
    }

}
