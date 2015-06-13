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
        $this->db->join('vehicles_type', ' vehicles.VTID = vehicles_type.VTID', 'left');
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

    public function get_route_detail() {

        $data = array();
        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller();

        foreach ($routes as $route) {
            $rcode = $route['RCode'];
            $vtid = $route['VTID'];
            $vt_name = $route['VTDescription'];
            $source = $route['RSource'];
            $destination = $route['RDestination'];
            $route_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;

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

            $detail_in_route = array();
            $route_details = $this->m_route->get_route_detail_by_seller($rcode, $vtid);
            foreach ($route_details as $rd) {
                $RID = $rd['RID'];
                $RCode = $rd['RCode'];
                $VTID = $rd['VTID'];
                $source_name = $rd['RSource'];
                $destination_name = $rd['RDestination'];

                $start_point = $rd['StartPoint'];
                $stations = $this->m_station->get_stations_by_start_point($start_point, $RCode, $VTID, $seller_station_seq);



                if ($seller_station_seq == 1) {
                    $start_point = 'S';
                    array_pop($detail_in_route);
                } elseif ($seller_station_seq == $num_station) {
                    $start_point = 'D';
                    array_shift($detail_in_route);
                } else {
                    $start_point = 'กลางทาง';
                }
                $temp_rd = array(
                    'RID' => $RID,
                    'SourceName' => $source_name,
                    'DestinationName' => $destination_name,
                    'Stations' => $stations,
                );
                array_push($detail_in_route, $temp_rd);
            }
        }
    }

    public function set_form_search($rcode = NULL, $vtid = NULL, $rid = NULL) {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');

        $data = array();
        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller();

        foreach ($routes as $route) {
            $rcode = $route['RCode'];
            $vtid = $route['VTID'];
            $vt_name = $route['VTDescription'];
            $source = $route['RSource'];
            $destination = $route['RDestination'];
            $route_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;

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

            $detail_in_route = array();
            $route_details = $this->m_route->get_route_detail_by_seller($rcode, $vtid);

            foreach ($route_details as $rd) {
                $RID = $rd['RID'];
                $RCode = $rd['RCode'];
                $VTID = $rd['VTID'];
                $destination_name = $rd['RDestination'];

                $schedule_in_route = array();
                $schedules = $this->m_schedule->get_schedule($date, $RCode, $VTID, $RID);

                foreach ($schedules as $schedule) {
                    
                }


                $temp_rd = array(
                    'RID' => $RID,
                    'DestinationName' => $destination_name,
                );
                array_push($detail_in_route, $temp_rd);
            }
            $cost_along_road = array();
            if ($seller_station_seq == 1) {
                $start_point = 'S';
                $cost_along_road = array_pop($detail_in_route);
            } elseif ($seller_station_seq == $num_station) {
                $start_point = 'D';
                $cost_along_road = array_shift($detail_in_route);
            } else {
                $start_point = 'กลางทาง';
            }

            $temp_ = array(
                'RCode' => $rcode,
                'VTID' => $vtid,
                'VTName' => $vt_name,
                'SourceName' => $source,
                'DestinationName' => $destination,
                'RouteName' => $route_name,
                'SID' => $seller_station_id,
                'StationName' => $seller_station_name,
                'Seq' => $seller_station_seq,
                'route_details' => $detail_in_route,
            );

            array_push($data, $temp_);
        }


        return $data;
    }

    public function set_form_booking($rid, $SourceID, $DestinationID = NUll, $tsid = NULL) {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_fares');
        $this->load->model('m_ticket');
        $this->load->model('m_report');

        $date = $this->m_datetime->getDateToday();
        $route = $this->m_route->get_route_by_seller(NULL, NULL, $rid)[0];

        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $route_name = "$vt_name  $rcode  $source - $desination";
        $start_point = $route['StartPoint'];

        $seller_station_id = $route['SID'];
        $seller_station_seq = $route['Seq'];
        $seller_station_name = $route['StationName'];

        $stations = $this->m_station->get_stations($rcode, $vtid);
        $num_station = count($stations);

        $s_station = $this->m_station->get_stations(NULL, NULL, $SourceID)[0];

        $source_id = $s_station['SID'];
        $source_name = $s_station['StationName'];
        $source_seq = $s_station['Seq'];
        $source_travel_time = $s_station['TravelTime'];

        $d_stations = $this->m_station->get_stations($rcode, $vtid, $DestinationID);

        $destination_id = '';
        $destination_name = '';
        $destination_seq = '';
        $destination_travel_time = '';

        if (count($d_stations) > 0 && $DestinationID != NULL) {
            $d_station = $d_stations[0];
            $destination_id = $d_station['SID'];
            $destination_name = $d_station['StationName'];
            $destination_seq = $d_station['Seq'];
            $destination_travel_time = $d_station['TravelTime'];
        }

        /*
         * ตารางเวลาเดินรถ
         */
        if ($tsid != NULL) {
            $schedule = reset($this->get_schedule($tsid));
        } else {
            $schedule = array();
        }
        /*
         * ค่าโดยสาร
         */
        $price = 0;
        $price_dis = 0;
        if ($destination_id != NULL) {
            $fare = $this->m_fares->get_fares($rcode, $vtid, $source_id, $destination_id);
            if (count($fare) > 0 && $fare != NULL) {
                $price = $fare[0]['Price'];
                $price_dis = $fare[0]['PriceDicount'];
            }
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
            $time_depart = $this->m_schedule->time_depart($rid, $tsid, $seller_station_id);
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

        $stations_ = $this->m_station->get_stations_by_start_point($start_point, $rcode, $vtid, $seller_station_seq);

        foreach ($stations_ as $station) {
            $i_stations[$station['SID']] = $station['StationName'];
        }

        $dropdown = 'id="DestinationID" onchange="change_destination()" ';

        $form_sale_ticket = array(
            'form' => form_open("sale/booking/$rid/$source_id/$destination_id/$tsid", array('class' => 'form', 'id' => 'form_booking', 'name' => 'form_booking')),
            'route_name' => form_input($i_route_name),
            'RID' => form_input($i_RID),
            'TSID' => form_input($i_TSID),
            'SourceID' => form_input($i_SourceID),
            'SourceName' => form_input($i_SourceName), //         
            'DestinationID' => form_dropdown('DestinationID', $i_stations, (set_value("DestinationID") == NULL) ? $destination_id : set_value("DestinationID"), $dropdown),
            'TimeDepart' => form_input($i_TimeDepart),
            'TimeArrive' => form_input($i_TimeArrive),
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
            'data_parcel_post' => $data['data_parcel_post'],
            'data_parcel_post_in'=>$data['data_parcel_post_in'],
        );

        return $rs;
    }

    public function set_data_form_booking($date, $RID, $SourceID, $DestinationID = NULL, $TSID = NULL) {

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
                $IsSold = TRUE;
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
        $route = reset($this->m_route->get_route_by_seller(NULL, NULL, $RID));

        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $start_point = $route['StartPoint'];
        $route_name = "$vt_name  $rcode  $source - $desination";

        $seller_station_id = $route['SID'];
        $seller_station_seq = $route['Seq'];

        $stations = $this->m_station->get_stations($rcode, $vtid);
        $num_station = count($stations);

        if ($seller_station_seq == 1 || $seller_station_seq == $num_station) {
            $IsFirstStation = TRUE;
        } else {
            $IsFirstStation = FALSE;
        }

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
            $TimeDepart = $this->m_schedule->get_time_depart($Date_schedule, $RID_schedule, $TSID_schedule, $SourceID)[0]['TimeDepart'];

//            $tickets_book = $this->get_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq);
//            $ticket_book_seller = $this->get_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq, $EID);
//
//            $ticket_sale = $this->get_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq);
//            $ticket_sale_seller = $this->get_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq, $EID);

            $NumberSeat = $schedule['VSeat'];
            $NumberSeatBook = $this->count_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq)['Number'];
            $NumberSeatSale = $this->count_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq)['Number'];

            /* จำนวนที่นั่งที่ว่าง */
            if ($NumberSeatBook > 0 && $NumberSeatSale > 0) {
                $NumberSeatBlank = $NumberSeat - $NumberSeatBook - $NumberSeatSale;
            } elseif ($NumberSeatBook > 0) {
                $NumberSeatBlank = $NumberSeat - $NumberSeatBook;
            } elseif ($NumberSeatSale > 0) {
                $NumberSeatBlank = $NumberSeat - $NumberSeatSale;
            } else {
                $NumberSeatBlank = $NumberSeat;
            }


            $NumberSeatBookBySeller = $this->count_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq, $EID)['Number'];
            $NumberSeatSaleBySeller = $this->count_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq, $EID)['Number'];



            $NumberTicketsExtra = 0;
            if ($NumberSeat <= $NumberSeatSale) {
                $NumberTicketsExtra = $this->count_ticket($start_point, $TSID_schedule, NULL, $seller_station_seq, $DestinationSeq, NULL, $NumberSeat)['Number'];
            }
            $NumberSeatTotal = array(
                'type' => "text",
                'name' => "NumberSeatTotal",
                'id' => "NumberSeatTotal ",
                'class' => "from-control",
                'readonly' => "TRUE",
                'value' => $NumberSeatSale + $NumberSeatBook,
            );

            $data_checkin = $this->m_checkin->get_check_in($date, $TSID_schedule, $seller_station_id, $EID);
            $CheckInID = NULL;
            $TimeCheckIn = NULL;
            if (count($data_checkin) > 0) {
                $CheckInID = $data_checkin[0]['CheckInID'];
                $TimeCheckIn = date('H:i', strtotime($data_checkin[0]['TimeCheckIn']));
            }

            $check_in_on_schedule = $this->m_checkin->get_check_in($date, $TSID_schedule);
            $data_check_in = array();
            foreach ($check_in_on_schedule as $checkin) {
                $SID_checkin = $checkin['SID'];
                $create_by = $checkin['CreateBy'];
                $seller = $this->m_checkin->get_seller($create_by, $SID_checkin);
                $station_name = $checkin['StationName'];
                if (isset($seller['SellerNote'])) {
                    $station_name .= " (" . $seller['SellerNote'] . ") ";
                }

                $temp_check_in = array(
                    'TimeCheckIn' => date('H:i', strtotime($checkin['TimeCheckIn'])),
                    'StationName' => $station_name,
                );
                array_push($data_check_in, $temp_check_in);
            }
            /* ข้อมูลการส่งรายงาน */
            $ReportID = $this->m_report->check_report(NULL, $TSID_schedule, $seller_station_id);
            /* ตรวจสอบว่าสามารถขายตั๋วได้หรือไม่ */
            if ($IsFirstStation) {
                $IsSold = TRUE;
            } elseif (count($data_check_in) > 0) {
                $IsSold = TRUE;
            } else {
                $IsSold = FALSE;
            }

            $temp_schedule_in_route = array(
                'RID' => $schedule['RID'],
                'RouteName' => $route_name,
                'StartPoint' => $start_point,
                'SourceID' => $seller_station_id,
                'SourceSeq' => $seller_station_seq,
                'DestinationID' => $DestinationID,
                'DestinationSeq' => $DestinationSeq,
                'IsSold' => $IsSold,
                'TSID' => $TSID_schedule,
                'TimeDepart' => $TimeDepart,
                'Date' => $Date_schedule,
                'VID' => $schedule['VID'],
                'VCode' => $schedule['VCode'],
                'VTID' => $schedule['VTID'],
                'ScheduleNote' => $schedule['ScheduleNote'],
                'ReportID' => $ReportID,
                'CheckInID' => $CheckInID,
                'TimeCheckIn' => $TimeCheckIn,
                'CheckInTime' => $this->m_checkin->check_checkin(NULL, $TSID_schedule, $seller_station_id),
                'ScheduleCheckIn' => $data_check_in,
                'NumberSeat' => $NumberSeat,
                'NumberSeatBlank' => $NumberSeatBlank,
                'NumberSeatBook' => $NumberSeatBook,
                'NumberSeatSale' => $NumberSeatSale,
                'NumberTicketsExtra' => $NumberTicketsExtra,
                'NumberSeatBookBySeller' => $NumberSeatBookBySeller,
                'NumberSeatSaleBySeller' => $NumberSeatSaleBySeller,
                'NumberSeatTotal' => form_input($NumberSeatTotal),
            );
            if ($TSID != NULL && $TSID == $TSID_schedule) {

                $ticket_book_seller = $this->get_ticket($start_point, $TSID_schedule, 2, $seller_station_seq, $DestinationSeq, $EID); //
                $ticket_sale = $this->get_ticket($start_point, $TSID_schedule, 1, $seller_station_seq, $DestinationSeq);
                $TicketsExtra = $this->get_ticket($start_point, $TSID_schedule, NULL, $seller_station_seq, $DestinationSeq, NULL, $NumberSeat);

                /*
                 * ค่าโดยสาร
                 */
                $price = 0;
                $price_dis = 0;

                $i_Fare = array(
                    $price => $price,
                    $price_dis => $price_dis,
                );

                if ($DestinationID != NULL) {
                    $fare = $this->m_fares->get_fares($rcode, $vtid, $seller_station_id, $DestinationID);
                    if (count($fare) > 0 && $fare != NULL) {
                        $price = $fare[0]['Price'];
                        $price_dis = $fare[0]['PriceDicount'];
                        $i_Fare = array(
                            $price_dis => $price_dis . ' (ลด) ',
                            $price => $price . ' (เต็ม) ',
                        );
                    }
                }

                $SeatBookBySeller = array();
                foreach ($ticket_book_seller as $ticket) {
                    $TicketID = $ticket['TicketID'];
                    $Seat = $ticket['Seat'];
                    $PriceSeat = $ticket['PriceSeat'];

                    $i_TicketID = array(
                        'type' => 'hidden',
                        'name' => 'TicketID[]',
                        'value' => $TicketID,
                    );
                    $i_SourceID = array(
                        'type' => 'hidden',
                        'name' => 'SourceID[]',
                        'value' => $ticket['SourceID']
                    );
                    $i_DestinationID = array(
                        'type' => 'hidden',
                        'name' => 'DestinationID[]',
                        'value' => $ticket['DestinationID'],
                    );
                    $dropdown = "id = \"FareType \" " . 'class="form-control fare" onchange="calTotalFare()"';
                    $temp_ticket_book = array(
                        'Seat' => $Seat,
                        'txtTicketID' => $TicketID,
                        'TicketID' => form_input($i_TicketID),
                        'PriceSeat' => $PriceSeat,
                        'fares' => form_dropdown("PriceSeat[]", $i_Fare, $PriceSeat, $dropdown),
                        'Source' => form_input($i_SourceID),
                        'Destination' => form_input($i_DestinationID),
                    );
                    array_push($SeatBookBySeller, $temp_ticket_book);
                }
                $Reports = array();

                if ($NumberSeatSaleBySeller > 0) {
                    $Reports = $this->get_report_by_seller($TSID_schedule, $seller_station_id);
                }

                $temp_schedule_in_route['TicketsBook'] = $ticket_book_seller;
                $temp_schedule_in_route['TicketsSale'] = $ticket_sale;
                $temp_schedule_in_route['TicketsExtra'] = $TicketsExtra;
                $temp_schedule_in_route['SeatBookBySeller'] = $SeatBookBySeller;
                $temp_schedule_in_route['ScheduleReport'] = $Reports;
                $schedule_select = $temp_schedule_in_route;
            }
            array_push($schedules_in_route, $temp_schedule_in_route);
        }
        $rs = array(
            'routes_seller' => $data_routes_seller,
            'schedules' => $schedules_in_route,
            'schedule_select' => $schedule_select,
            'data_parcel_post' => $this->get_parcel_post_report($SourceID, $TSID),
            'data_parcel_post_in'=>  $this->get_parcel_post_in($SourceID),
        );
        return $rs;
    }

    public function get_parcel_post_in($SID, $date = NULL, $ReceptID = NULL, $VCode = NULL) {

        $this->db->select('parcel_post.ReceiptID,'
                . 'vehicles.VCode,'
                . 'cost.CostDate,'
                . 'cost.CostNote,'
                . 'parcel_post.Number,'
                . 'parcel_post.SourceID,'
                . 'parcel_post.DestinationID,'
                . 'parcel_post.SenderName,'
                . 'parcel_post.SenderPhone,'
                . 'parcel_post.ReceiverName,'
                . 'parcel_post.ReceiverPhone,'
               
        );

        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');
        $this->db->join('parcel_post', 'parcel_post.CostID = cost.CostID', 'left');
        $this->db->join('vehicles_has_schedules', 'vehicles_has_schedules.TSID = t_schedules_day_has_cost.TSID', 'left');
        $this->db->join('vehicles', 'vehicles.VID = vehicles_has_schedules.VID', 'left');

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }

        $this->db->where('parcel_post.DestinationID', $SID);

        $this->db->where('cost.CostDate', $date);
        $this->db->where('cost.CostDetailID', '6');

        $query = $this->db->get('cost');
        return $query->result_array();
    }

    public function get_parcel_post_report($SourceID, $TSID = NULL) {
        $this->db->select(''
                . 'SUM(cost.CostValue) AS Total,'
                . 'SUM(parcel_post.Number) AS Number,'
                . 't_stations.StationName AS DestinationName,'
                . 'cost.CreateBy AS CreateBy,'
                . 'cost.CreateDate AS CreateDate,'
        );
        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID', 'left');
        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');
        $this->db->join('parcel_post', 'parcel_post.CostID = cost.CostID', 'left');
        $this->db->join('t_stations', 't_stations.SID = parcel_post.DestinationID', 'left');

        $date = $this->m_datetime->getDateToday();
        if ($TSID != NULL) {
            $this->db->where('t_schedules_day_has_cost.TSID', $TSID);
        }
        $this->db->group_by('parcel_post.DestinationID');
        $this->db->where('cost.CostDate', $date);
        $this->db->where('cost.SID', $SourceID);
        $this->db->where('cost.CostDetailID', '6');
        $this->db->where('cost.CreateBy', $this->m_user->get_user_id());
        $query = $this->db->get('cost');
        if ($TSID == NULL) {
            return array();
        }
        return $query->result_array();
    }

    public function get_post_form_print_ticket() {
        $TicketIDs = $this->input->post('TicketID');
        $data_tickets = array();
        foreach ($TicketIDs as $TicketID) {
            $this->db->select('NumberPrint');
            $this->db->where('TicketID', $TicketID);
            $query = $this->db->get('ticket_sale');
//            $NumberPrint = $query->row_array()['NumberPrint'];
            $temp_ticket = array(
                'TicketID' => $TicketID,
                'NumberPrint' => 1,
            );
            array_push($data_tickets, $temp_ticket);
        }
        return $data_tickets;
    }

    public function get_post_booking() {

        $TSID = $this->input->post('TSID');
        $SourceID = $this->input->post('SourceID');
        $DestinationID = $this->input->post('DestinationID');
        $TicketID = $this->input->post('TicketID');
        $PriceSeat = $this->input->post('PriceSeat');

        /* data schedule */
        $schedule = reset($this->m_schedule->get_schedule(NULL, NULL, NULL, NULL, $TSID));
        $RCode = $schedule['RCode'];
        $VTID = $schedule['VTID'];

        $rs = array();

        for ($i = 0; $i < count($TicketID); $i++) {
            /* data fares */
            $fares = reset($this->m_fares->get_fares($RCode, $VTID, $SourceID[$i], $DestinationID[$i]));
            $Price = $fares['Price'];
            $PriceDicount = $fares['PriceDicount'];

            $IsDiscount = 0;
            if ($Price == $PriceDicount) {
                $IsDiscount = 0;
            } elseif ($PriceDicount == $PriceSeat[$i]) {
                $IsDiscount = 1;
            }
            $temp_ticket = array(
                'TicketID' => $TicketID[$i],
                'PriceSeat' => $PriceSeat[$i],
                'IsDiscount' => $IsDiscount,
            );
            array_push($rs, $temp_ticket);
        }
        return $rs;
    }

    public function set_form_print($date, $RID, $SourceID, $DestinationID, $TSID, $tickets) {
        $this->load->model('m_route');

        $data_ticket = array();

        $route = reset($this->m_route->get_route(NULL, NULL, $RID));

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
            array_push($data_ticket, $temp_ticket);
        }
        $rs = array(
            'form' => form_open("sale/print_ticket/$RID/$SourceID/$DestinationID/$TSID", array('id' => 'form_print_ticket')),
            'tickets' => $data_ticket,
        );

        return $rs;
    }

    /*
     * log seat
     */

    public function set_form_print_log($TSID, $SourceID) {

        $this->load->model('m_route');
        $this->load->model('m_ticket');
        $this->load->model('m_station');
        $this->load->model('m_schedule');

        $date = $this->m_datetime->getDateToday();
        $EID = $this->m_user->get_user_id();

        $schedule = reset($this->get_schedule($TSID));
        $RID = $schedule['RID'];
        $RCode = $schedule['RCode'];
        $VTID = $schedule['VTID'];
        $SourceName = $schedule['RSource'];
        $DestinationName = $schedule['RDestination'];
        $VTName = $schedule['VTDescription'];

        $VCode = $schedule['VCode'];

        $route = reset($this->m_route->get_route_by_seller($RCode, $VTID, $RID));
        $seller_station_name = $route['StationName'];

        if ($route['SellerNote'] != NULL) {
            $note = $route['SellerNote'];
            $seller_station_name .= " ($note) ";
        }

        $reports = $this->get_report_by_seller($TSID, $SourceID);
        $reports_in_schedule = array();
        foreach ($reports as $report) {
            $source_id = $report['SourceID'];
            $destination_id = $report['DestinationID'];
            $SeatNo = array();
            $tickets = $this->m_ticket->get_ticket($date, $TSID, 1, $EID);
            foreach ($tickets as $ticket) {
                if ($source_id == $ticket['SourceID'] && $destination_id == $ticket['DestinationID']) {
                    array_push($SeatNo, $ticket['Seat']);
                }
            }
            $temp_report = array(
                'DestinationName' => $report['DestinationName'],
                'NumberTicket' => $report['NumberTicket'],
                'SeatNo' => $SeatNo,
            );
            array_push($reports_in_schedule, $temp_report);
        }
        $RouteName = "$RCode $SourceName - $DestinationName ";
        $TimeDepart = reset($this->m_schedule->get_time_depart($date, $RID, $TSID, $SourceID))['TimeDepart'];

        $rs = array(
            'VTName' => $VTName,
            'RouteName' => $RouteName,
            'SallerStationName' => $seller_station_name,
            'Date' => $this->m_datetime->getDateThaiString($date),
            'TimeDepart' => $TimeDepart,
            'VCode' => $VCode,
            'reports' => $reports_in_schedule,
        );
        return $rs;
    }

    /*
     * recept parcel
     */

    public function set_form_print_parcel($RID, $TSID, $CostID) {
        $this->load->model('m_route');
        $this->load->model('m_cost');
        $this->load->model('m_station');
        $this->load->model('m_schedule');

        $route = reset($this->m_route->get_route(NULL, NULL, $RID));

        $RCode = $route['RCode'];
        $VTName = $route['VTDescription'];
        $RSource = $route['RSource'];
        $RDestination = $route['RDestination'];


        $RouteName = $RCode . ' ' . ' ' . $RSource . ' - ' . $RDestination;

        $data_parcel = reset($this->m_cost->get_parcel_post(NULL, $CostID, $TSID));

        $source = reset($this->m_station->get_stations(NULL, NULL, $data_parcel['SourceID']));
        $destination = reset($this->m_station->get_stations(NULL, NULL, $data_parcel['DestinationID']));

        $TimeDepart = $this->m_schedule->time_depart($RID, $TSID, $data_parcel['SourceID']);
        $TimeArrive = $this->m_schedule->time_arrive($RID, $TSID, $data_parcel['SourceID'], $data_parcel['DestinationID']);

        $data = array();

        $data['VTName'] = $VTName;
        $data['RouteName'] = $RouteName;
        $data['SourceName'] = $source['StationName'];
        $data['DestinationName'] = $destination['StationName'];
        $data['TimeDepart'] = $TimeDepart;
        $data['TimeArrive'] = $TimeArrive;
        $data['Date'] = $this->m_datetime->getDateThaiStringShort($data_parcel['CostDate']);
        $data += $data_parcel;
        $data['debug'] = $this->m_cost->get_parcel_post(NULL, $CostID, $TSID);



        return $data;
    }

    public function get_ticket($StartPoint, $TSID, $StatusSeat = NULL, $SourceSeq = NULL, $DestinationSeq = NULL, $EID = NULL, $Seat = NULL) {

        $this->db->select('TSID,TicketID,Seat,StatusSeat,SourceID,SourceName,DestinationID,DestinationName,PriceSeat,Seller,t_stations.Seq as DestinationSeq,IsDiscount');
        $this->db->join('t_stations', 't_stations.SID = ticket_sale.DestinationID', 'left');
        $this->db->where('TSID', $TSID);

        if ($StatusSeat != NULL) {
            $this->db->where('StatusSeat', $StatusSeat);
        }
        if ($StartPoint == 'S' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq >', $SourceSeq);
        }
        if ($StartPoint == 'D' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq <', $SourceSeq);
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

    public function count_ticket($StartPoint, $TSID, $StatusSeat = NULL, $SourceSeq = NULL, $DestinationSeq = NULL, $EID = NULL, $Seat = NULL) {

        $this->db->select('COUNT(TicketID) as Number');
        $this->db->join('t_stations', 't_stations.SID = ticket_sale.DestinationID', 'left');
        $this->db->where('TSID', $TSID);

        if ($StatusSeat != NULL) {
            $this->db->where('StatusSeat', $StatusSeat);
        }
        if ($StartPoint == 'S' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq >', $SourceSeq);
        }
        if ($StartPoint == 'D' && $SourceSeq != NULL) {
            $this->db->where('t_stations.Seq <', $SourceSeq);
        }

        if ($EID != NULL) {
            $this->db->where('Seller', $EID);
        }

        if ($Seat != NULL) {
            $this->db->where('Seat >', $Seat);
        }

        $query = $this->db->get('ticket_sale');

        return $query->row_array();
    }

    public function get_data_check_in() {
        $this->load->model('m_checkin');
//        $this->m_checkin->get_check_in($date, $TSID_schedule);
    }

    public function get_report_by_seller($TSID, $SourceID) {
        $this->load->model('m_report');
        $this->load->model('m_fares');
        $this->load->model('m_schedule');

        $schedule = reset($this->m_schedule->get_schedule(NULL, NULL, NULL, NULL, $TSID));
        $RCode = $schedule['RCode'];
        $VTID = $schedule['VTID'];

        $tickets = $this->m_report->get_ticket_by_seller($TSID, $SourceID);
        $ticket_in_schedule = array();

        foreach ($tickets as $ticket) {
            $NumberTicket = $ticket['NumberTicket'];
            $DestinationID = $ticket['DestinationID'];
            $DestinationName = $ticket['DestinationName'];
            $Total = $ticket['Total'];
            $num_ticket_discount = reset($this->m_report->get_ticket_by_seller($TSID, $SourceID, $DestinationID, 1))['NumberTicket'];
            $num_ticket_full = $NumberTicket - $num_ticket_discount;
            $fare = reset($this->m_fares->get_fares($RCode, $VTID, $SourceID, $DestinationID));
            $price = $fare['Price'];
            $price_dis = $fare['PriceDicount'];
            if ($price != $price_dis && $num_ticket_discount > 0) {
                $PriceSeat = "$price/$price_dis";
            } else {
                $PriceSeat = $price;
            }

            $temp_ticket = array(
                'SourceID' => $SourceID,
                'DestinationID' => $DestinationID,
                'PriceSeat' => $PriceSeat,
                'NumberTicket' => $NumberTicket,
                'NumberPriceFull' => $num_ticket_full,
                'NumberPriceDiscount' => $num_ticket_discount,
                'DestinationName' => $DestinationName,
                'Total' => $Total,
            );
            array_push($ticket_in_schedule, $temp_ticket);
        }
        return $ticket_in_schedule;
    }

    public function validate_form_sale() {
        $Seat = $this->input->post('TicketID');
        if (count($Seat) <= 0 || $Seat == NULL) {
            return FALSE;
        }
        $this->form_validation->set_rules("SourceID[]", "ต้นทาง", 'trim|required|xss_clean');
        $this->form_validation->set_rules('DestinationID[]', 'ปลายทาง', 'trim|xss_clean|callback_check_dropdown');


        return TRUE;
    }

    public function validate_form_print_ticket() {
        $this->form_validation->set_rules("TicketID[]", "เลขที่ตั๋วโดยสาร", 'trim|required|xss_clean');
        return TRUE;
    }

    //    ตรวจสอบค่าใน dropdown
    public function check_dropdown($str) {
        if ($str === '0') {
            $this->form_validation->set_message('check_dropdown', 'เลือก %s');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
