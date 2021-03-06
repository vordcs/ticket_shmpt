<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_report extends CI_Model {

    public function get_cost_type($id = NULL) {

        if ($id != NULL) {
            $this->db->where('CostTypeID', $id);
        }
        $query = $this->db->get('cost_type');
        return $query->result_array();
    }

    public function get_report($date = NULL, $RCode = NULL, $VTID = NULL, $SID = NULL, $ReportID = NULL) {

        if ($ReportID != NULL) {
            $this->db->where('ReportID', $ReportID);
        }

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        if ($RCode != NULL) {
            $this->db->where('RCode', $RCode);
        }
        if ($VTID != NULL) {
            $this->db->where('VTID', $VTID);
        }
        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }

        if ($RCode != NULL && $VTID != NULL && $SID != NULL) {
            $this->db->order_by('ReportID', 'desc');
        }

        $this->db->where('ReportDate', $date);
        $this->db->where('CreateBy', $this->m_user->get_user_id());

        $query = $this->db->get('report_day');

        return $query->result_array();
    }

    public function check_report($EID = NULL, $TSID = NULL, $SID = NULL) {
        $this->db->select('*,report_day.CreateBy as CreateBy');
        $this->db->join('t_schedules_day_has_report', 'report_day.ReportID = t_schedules_day_has_report.ReportID', 'left');
        if ($EID == NULL) {
            $EID = $this->m_user->get_user_id();
        }
        if ($TSID != NULL) {
            $this->db->where('t_schedules_day_has_report.TSID', $TSID);
        }
        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }
        $this->db->where('report_day.CreateBy', $EID);
        $query = $this->db->get('report_day');

        if ($query->num_rows() > 0) {
            $rs = $query->row_array()['ReportID'];
        } else {
            $rs = NULL;
        }
        return $rs;
    }

    public function get_schedules_has_report($RCode, $VTID, $SID, $ReportID = NULL) {
        $date = $this->m_datetime->getDateToday();

        $this->db->select('t_schedules_day_has_report.ReportID as ReportID,t_schedules_day_has_report.TSID,TimeDepart,VCode');

        $this->db->join('t_schedules_day', 't_schedules_day_has_report.TSID = t_schedules_day.TSID ');
        $this->db->join('report_day', 'report_day.ReportID = t_schedules_day_has_report.ReportID');
//   
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day_has_report.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');
        if ($ReportID != NULL) {
            $this->db->where('t_schedules_day_has_report.ReportID', $ReportID);
        }
        $this->db->where('report_day.RCode', $RCode);
        $this->db->where('report_day.VTID', $VTID);
        $this->db->where('report_day.SID', $SID);
        $this->db->where('report_day.ReportDate', $date);
        $this->db->where('report_day.CreateBy', $this->m_user->get_user_id());
        $query = $this->db->get('t_schedules_day_has_report');

        return $query->result_array();
    }

    public function check_schedules_has_report($RCode, $VTID, $SID, $TSID, $ReportID = NULL) {
        $date = $this->m_datetime->getDateToday();

        $this->db->join('report_day', 'report_day.ReportID = t_schedules_day_has_report.ReportID');

        if ($ReportID != NULL) {
            $this->db->where('t_schedules_day_has_report.ReportID', $ReportID);
        }
        $this->db->where('t_schedules_day_has_report.TSID', $TSID);
        $this->db->where('report_day.RCode', $RCode);
        $this->db->where('report_day.VTID', $VTID);
        $this->db->where('report_day.SID', $SID);
        $this->db->where('report_day.ReportDate', $date);
        $this->db->where('report_day.CreateBy', $this->m_user->get_user_id());
        $query = $this->db->get('t_schedules_day_has_report');
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function insert_report($data) {

        $ReportID = $data['report']['ReportID'];
        $num_report = count($this->get_report(NULL, NULL, NULL, $ReportID));
        if ($num_report <= 0) {
            
            //insert report day 
            $this->db->insert('report_day', $data['report']);
            
            //insert t_schedules_day_has_report
            $i = 0;
            foreach ($data['schedules_day_has_report'] as $s_r) {
                $this->db->insert('t_schedules_day_has_report', $s_r);
                $rs[$i] = $s_r;
                $i++;
            }
            $rs = $ReportID;
        } else {
            $rs = NULL;
        }
        return $rs;
    }

    public function set_form_view() {
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');

        $date = $this->m_datetime->getDateToday();
        $rs = array();

        $vehicle_types = $this->m_route->get_vehicle_types();

        foreach ($vehicle_types as $vehicle_type) {

            $VTID = $vehicle_type['VTID'];
            $VTName = $vehicle_type['VTDescription'];


            $routes = $this->m_route->get_route_by_seller(NULL, $VTID);
            $routes_in_type = array();
            foreach ($routes as $route) {
                $rcode = $route['RCode'];
                $source = $route['RSource'];
                $destination = $route['RDestination'];
                $route_name = "$VTName " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;

                $EID = $this->m_user->get_user_id();
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

                $stations = $this->m_station->get_stations($rcode, $VTID);
                $num_station = count($stations);
                foreach ($stations as $station) {
                    if ($seller_station_id == $station['SID']) {
                        $seller_station_seq = $station['Seq'];
                    }
                }

                $route_detail = $this->m_route->get_route_detail_by_seller($rcode, $VTID);
                $detail_in_route = array();
                foreach ($route_detail as $rd) {
                    $rid = $rd['RID'];
                    $start_point = $rd['StartPoint'];
                    $source = $rd['RSource'];
                    $destination = $rd['RDestination'];
                    $route_detail_name = "$VTName เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                    $stations_in_route = $this->m_station->get_stations_by_start_point($start_point, $rcode, $VTID);

                    $schedules = $this->m_schedule->get_schedule($date, $rcode, $VTID, $rid);

                    $schedules_in_route = array();
                    foreach ($schedules as $schedule) {
                        $tsid = $schedule['TSID'];
                        $start_time = $schedule['TimeDepart'];
                        $report_id = $this->check_report($EID, $tsid, $seller_station_id);
                        $time_depart = '';
                        $temp = 0;
                        foreach ($stations_in_route as $s) {
                            if ($s['IsSaleTicket'] == '1') {
                                $station_name = $s['StationName'];
                                $travel_time = $s['TravelTime'];
                                if ($s['Seq'] == '1' || $s['Seq'] == $num_station) {
                                    $time = strtotime($start_time);
                                } else {
                                    $temp+=$travel_time;
                                    $time = strtotime("+$temp minutes", strtotime($start_time));
                                }
                                if ($seller_station_id == $s['SID']) {
                                    $time_depart = date('H:i', $time);
                                }
                            }
                        }
                        $vid = $schedule['VID'];
                        $vcode = $schedule['VCode'];
                        if ($vcode == NULL) {
                            $vcode = '-';
                        }

                        $income = 0;
                        $outcome = 0;

                        /*
                         * รายได้จากการขายตั๋ว
                         */
                        $income += $this->m_ticket->sum_ticket_price($date, $seller_station_id, $tsid)['Total'];

                        $cost_types = $this->m_cost->get_cost_type();

                        foreach ($cost_types as $cost_type) {
                            $cost_type_id = $cost_type['CostTypeID'];
                            $sum_cost_value = $this->m_cost->sum_costs($date, $seller_station_id, $tsid, $cost_type_id);
                            $cost_tsid = $sum_cost_value['TSID'];
                            $cost_total = $sum_cost_value['Total'];
                            if ($cost_tsid != NULL) {
                                if ($cost_type_id == '1') {
                                    //รายรับ
                                    $income += $cost_total;
                                }
                                if ($cost_type_id == '2') {
                                    //รายจ่าย
                                    $outcome = $cost_total;
                                }
                            }
                        }

                        $total = $income - $outcome;

                        /*
                         * รายรับ
                         * รายทาง ของสถานีต้นทางหรือปลายทางเท่านั่น
                         */
                        $along_road = 0;
                        $sum_along_road = $this->m_cost->sum_costs($date, $seller_station_id, $tsid, 1, 1);
                        $tsid_along_road = $sum_along_road['TSID'];
                        $cost_id = '';

                        if ($tsid_along_road != NULL) {
                            $along_road = $sum_along_road['Total'];
                            $cost_id = $this->m_cost->get_cost_along_road($tsid_along_road)['CostID'];
                        }

                        $temp_schedules_in_route = array(
                            'TSID' => $tsid,
                            'TimeDepart' => $time_depart,
                            'VID' => $vid,
                            'VCode' => $vcode,
                            'Income' => number_format($income, 1),
                            'Outcome' => number_format($outcome, 1),
                            'Total' => number_format($total, 1),
                            'AlongRoad' => number_format($along_road, 1),
                            'ReportID' => $report_id,
                            'CostID' => $cost_id,
                        );
                        array_push($schedules_in_route, $temp_schedules_in_route);
                    }


                    $temp_detail_in_route = array(
                        'RID' => $rid,
                        'RouteName' => $route_detail_name,
                        'RSource' => $source,
                        'RDestination' => $destination,
                        'StartPoint' => $start_point,
                        'schedules' => $schedules_in_route,
                    );
                    array_push($detail_in_route, $temp_detail_in_route);
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
                $Reports = $this->get_report($date, $rcode, $VTID, $seller_station_id);
                $temp_route = array(
                    'RCode' => $rcode,
                    'RouteName' => $route_name,
                    'seller_station_id' => $seller_station_id,
                    'seller_station_seq' => $seller_station_seq,
                    'seller_station_name' => $seller_station_name,
                    'start_point' => $start_point,
                    'Reports' => $Reports,
                    'routes_detail' => $detail_in_route,
                    'cost_along_road' => $cost_along_road,
                );
                array_push($routes_in_type, $temp_route);
            }

            $temp_ = array(
                'VTID' => $VTID,
                'VTName' => $VTName,
                'routes' => $routes_in_type,
            );
            array_push($rs, $temp_);
        }


        return $rs;
    }

    public function set_form_send($RCode, $VTID, $SID) {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');
        $this->load->model('m_checkin');

        $data = array();
        $EID = $this->m_user->get_user_id();
        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller($RCode, $VTID);

        $sum = 0;

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

            $detail_in_route = array();
            foreach ($route_detail as $rd) {
                $rid = $rd['RID'];
                $start_point = $rd['StartPoint'];
                $source = $rd['RSource'];
                $destination = $rd['RDestination'];
                $route_detail_name = "$vt_name เส้นทาง " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;
                $stations_in_route = $this->m_station->get_stations_by_start_point($start_point, $rcode, $vtid);

                $schedules = $this->m_schedule->get_schedule($date, $rcode, $vtid, $rid);

                $schedules_in_route = array();

                foreach ($schedules as $schedule) {
                    $tsid = $schedule['TSID'];
                    $start_time = $schedule['TimeDepart'];
                    $report_id = $this->check_report($EID, $tsid, $seller_station_id);
                    $time_depart = '';
                    $vcode = $schedule['VCode'];
                    $temp = 0;
                    foreach ($stations_in_route as $s) {
                        if ($s['IsSaleTicket'] == '1') {
                            $station_name = $s['StationName'];
                            $travel_time = $s['TravelTime'];
                            if ($s['Seq'] == '1' || $s['Seq'] == $num_station) {
                                $time = strtotime($start_time);
                            } else {
                                $temp+=$travel_time;
                                $time = strtotime("+$temp minutes", strtotime($start_time));
                            }
                            if ($seller_station_id == $s['SID']) {
                                $time_depart = date('H:i', $time);
                            }
                        }
                    }

                    $income = 0;
                    $outcome = 0;

                    /*
                     * รายได้จากการขายตั๋ว
                     */
                    $income += $this->m_ticket->sum_ticket_price($date, $seller_station_id, $tsid)['Total'];
                    /*
                     * ค่าใช้จ่ายที่เกิดขึ้น ในแต่ละรอบ
                     */
                    $cost_types = $this->m_cost->get_cost_type();

                    foreach ($cost_types as $cost_type) {
                        $cost_type_id = $cost_type['CostTypeID'];
                        $sum_cost_value = $this->m_cost->sum_costs($date, $seller_station_id, $tsid, $cost_type_id);
                        $cost_tsid = $sum_cost_value['TSID'];
                        $cost_total = $sum_cost_value['Total'];
                        if ($cost_tsid != NULL) {
                            if ($cost_type_id == '1') {
                                //รายรับ
                                $income += $cost_total;
                            }
                            if ($cost_type_id == '2') {
                                //รายจ่าย
                                $outcome = $cost_total;
                            }
                        }
                    }
                    $total = $income - $outcome;

                    /*
                     * รายรับ
                     * รายทาง ของสถานีต้นทางหรือปลายทางเท่านั่น
                     */
                    $along_road = 0;
                    $sum_along_road = $this->m_cost->sum_costs($date, $seller_station_id, $tsid, 1, 1);
                    $tsid_along_road = $sum_along_road['TSID'];
                    $cost_id = '';

                    if ($tsid_along_road != NULL) {
                        $along_road = $sum_along_road['Total'];
                        $cost_id = $this->m_cost->get_cost_along_road($tsid_along_road)['CostID'];
                    }

                    $tickets_in_schedule = array();
                    $income_in_schedule = array();
                    $outcome_in_schedule = array();
                    $road_in_schedule = array();
                    if ($report_id == NULL) {
                        /*
                         * รายได้จากการขายตั๋ว
                         */
                        $tickets_in_schedule = $this->get_ticket_by_seller($tsid, $seller_station_id);
                        /*
                         * รายรับ
                         */
                        $income_in_schedule = $this->get_cost(1, $tsid, $seller_station_id);
                        /*
                         * รายจ่าย
                         */
                        $outcome_in_schedule = $this->get_cost(2, $tsid, $seller_station_id);
                        /*
                         * รายทาง
                         */
                        $road_in_schedule = $this->get_cost(1, $tsid, $seller_station_id, 1);
                    }

                    $data_checkin = $this->m_checkin->get_check_in($date, $tsid, $seller_station_id, $EID);
                    $CheckInID = NULL;
                    $TimeCheckIn = NULL;
                    if (count($data_checkin) > 0) {
                        $CheckInID = $data_checkin[0]['CheckInID'];
                        $TimeCheckIn = date('H:i', strtotime($data_checkin[0]['TimeCheckIn']));
                    }
                    $temp_schedules_in_route = array(
                        'TSID' => $tsid,
                        'TimeDepart' => $time_depart,
                        'VCode' => $vcode,
                        'TotalIncome' => $income,
                        'TotalOutcome' => $outcome,
                        'TotalAlongRoad' => $along_road,
                        'Total' => $total,
                        'CheckInID' => $CheckInID,
                        'ReportID' => $report_id,
                        'tickets' => $tickets_in_schedule,
                        'Income' => $income_in_schedule,
                        'Outcome' => $outcome_in_schedule,
                        'AlongRoad' => $road_in_schedule,
                    );
                    if ($report_id == NULL && $CheckInID != NULL && ($income > 0 || $outcome > 0 || $along_road > 0)) {
                        array_push($schedules_in_route, $temp_schedules_in_route);
                        $sum +=$income + $along_road - $outcome;
                    }
                }

                $temp_detail_in_route = array(
                    'RID' => $rid,
                    'RouteName' => $route_detail_name,
                    'RSource' => $source,
                    'RDestination' => $destination,
                    'StartPoint' => $start_point,
                    'schedules' => $schedules_in_route,
                );
                array_push($detail_in_route, $temp_detail_in_route);
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

            $temp = array(
                'RCode' => $rcode,
                'VTID' => $vtid,
                'RouteName' => $route_name,
                'seller_station_id' => $seller_station_id,
                'seller_station_seq' => $seller_station_seq,
                'seller_station_name' => $seller_station_name,
                'routes_detail' => $detail_in_route,
                'cost_along_road' => $cost_along_road,
            );
            array_push($data, $temp);
        }

        $i_Total = array(
            "id" => "Total",
            "name" => "Total",
            "class" => "form-control input-lg",
            "value" => $sum,
        );
        $i_Vage = array(
            "id" => "Vage",
            "name" => "Vage",
            "class" => "form-control input-lg",
            "value" => "0",
        );
        $i_Net = array(
            "id" => "Net",
            "name" => "Net",
            "class" => "form-control input-lg",
            "value" => $sum,
        );

        $i_ReportNote = array(
            "id" => "ReportNote",
            "name" => "ReportNote",
            "class" => "form-control input-lg",
            "rows" => 3,
            "value" => "",
        );

        $form = array(
            'Total' => form_input($i_Total),
            'Vage' => form_input($i_Vage),
            'Net' => form_input($i_Net),
            'ReportNote' => form_textarea($i_ReportNote),
        );

        $rs = array(
            'data' => $data,
            'form' => $form,
        );
        $this->session->set_flashdata('RCode', $rcode);
        $this->session->set_flashdata('VTID', $vtid);
        return $rs;
    }

    public function set_data_print($RCode, $VTID, $ReportID = NULL) {
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');

        $date = $this->m_datetime->getDateToday();

        $route = reset($this->m_route->get_route_by_seller($RCode, $VTID));
        $VTName = $route['VTDescription'];
        $RSource = $route['RSource'];
        $RDestination = $route['RDestination'];
        $RouteName = " $VTName $RCode $RSource - $RDestination";

        $seller_station_id = $route['SID'];
        $seller_station_name = $route['StationName'];
        $seller_station_seq = $route['Seq'];

        if ($route['SellerNote'] != NULL) {
            $note = $route['SellerNote'];
            $seller_station_name .= " ($note) ";
        }

        $stations = $this->m_station->get_stations($RCode, $VTID);
        $num_station = count($stations);
        $reports = $this->get_report($date, $RCode, $VTID, $seller_station_id, $ReportID);
        $report = reset($reports);


        $routes_detail = $this->m_route->get_route_detail_by_seller($RCode, $VTID);
        $details = array();

//        foreach ($reports as $report) {
        foreach ($routes_detail as $rd) {
            $RID = $rd['RID'];
            $goto = $rd['RDestination'];
            $schedules_in_rd = array();
            $schedules = $this->m_schedule->get_schedule($date, $RCode, $VTID, $RID);
            foreach ($schedules as $schedule) {
                $TSID = $schedule['TSID'];
                $tickets = $this->get_ticket_by_seller($TSID, $seller_station_id);
                $ticket_in_schedule = array();

                foreach ($tickets as $ticket) {
                    $NumberTicket = $ticket['NumberTicket'];
                    $DestinationID = $ticket['DestinationID'];
                    $DestinationName = $ticket['DestinationName'];
                    $Total = $ticket['Total'];
                    $num_ticket_discount = reset($this->get_ticket_by_seller($TSID, $seller_station_id, $DestinationID, 1))['NumberTicket'];
                    $num_ticket_full = $NumberTicket - $num_ticket_discount;


                    $temp_ticket = array(
                        'NumberTicket' => $NumberTicket,
                        'NumberPriceFull' => $num_ticket_full,
                        'NumberPriceDiscount' => $num_ticket_discount,
                        'DestinationName' => $DestinationName,
                        'Total' => $Total,
                    );
                    array_push($ticket_in_schedule, $temp_ticket);
                }

                $incomes_in_schedule = $this->get_costs(1, $TSID, $seller_station_id);
                $outcome_in_schedule = $this->get_costs(2, $TSID, $seller_station_id);
                $alongRoad_in_schedule = $this->get_cost(1, $TSID, $seller_station_id, 1);
                $temp_schedule = array(
                    'TSID' => $TSID,
                    'TimeDepart' => $this->m_schedule->time_depart($RID, $TSID, $seller_station_id),
                    'VCode' => $schedule['VCode'],
                    'tickets' => $ticket_in_schedule,
                    'Incomes' => $incomes_in_schedule,
                    'Outcomes' => $outcome_in_schedule,
                    'AlongRoads' => $alongRoad_in_schedule,
                );
                if ($this->check_schedules_has_report($RCode, $VTID, $seller_station_id, $TSID, $ReportID)) {
                    array_push($schedules_in_rd, $temp_schedule);
                }
            }
            $temp_rs = array(
                'RID' => $RID,
                'DestinationName' => $goto,
                'schedules' => $schedules_in_rd,
            );
            array_push($details, $temp_rs);
        }

        if ($seller_station_seq == 1) {
            $start_point = 'S';
            array_pop($details);
        } elseif ($seller_station_seq == $num_station) {
            $start_point = 'D';
            array_shift($details);
        } else {
            $start_point = 'กลางทาง';
        }

        if ($ReportID != NULL) {
            $sum_total = $report['Total'];
            $sum_vage = $report['Vage'];
            $sum_net = $report['Net'];
            $note = $report['ReportNote'];
        } else {
            $sum_total = 0;
            $sum_vage = 0;
            $sum_net = 0;
            $note = '';
            foreach ($reports as $report) {
                $sum_total += $report['Total'];
                $sum_vage += $report['Vage'];
                $sum_net += $report['Net'];
            }
        }

        $rs = array(
            'RouteName' => $RouteName,
            'SellerStationName' => $seller_station_name,
            'ReportID' => $ReportID,
            'Total' => $sum_total,
            'Vage' => $sum_vage,
            'Net' => $sum_net,
            'ReportNote' => $note,
            'details' => $details,
        );
        return $rs;
    }

    public function get_ticket_by_seller($TSID, $SourceID, $DestinationID = NULL, $IsDiscount = NULL) {
        $this->db->select('SourceID,SourceName,DestinationID,DestinationName,PriceSeat,COUNT(TicketID) as NumberTicket,SUM(PriceSeat) as Total');
        $this->db->where('SourceID', $SourceID);
        $this->db->where('TSID', $TSID);
        $this->db->where('Seller', $this->m_user->get_user_id());

        if ($DestinationID != NULL) {
            $this->db->where('DestinationID', $DestinationID);
        }
        if ($IsDiscount != NULL) {
            $this->db->where('IsDiscount', $IsDiscount);
        }
        $this->db->group_by('SourceID,DestinationID');
        $query = $this->db->get('ticket_sale');
        return $query->result_array();
    }

    public function get_costs($CostTypeID, $TSID, $SID) {
        $this->db->select('CostDetail,OtherCostDetail,COUNT(cost.CostDetailID) as Number,SUM(CostValue) as Total,cost.CreateBy AS CreateBy');

        $this->db->join('cost', 'cost.CostID = t_schedules_day_has_cost.CostID');
        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID', 'left');

        $this->db->where('cost.CostTypeID', $CostTypeID);
        $this->db->where('cost.SID', $SID);
        $this->db->where('t_schedules_day_has_cost.TSID', $TSID);


        $this->db->where('cost.CreateBy', $this->m_user->get_user_id());

        $this->db->group_by('cost.CostDetailID');

        $query = $this->db->get('t_schedules_day_has_cost');
        return $query->result_array();
    }

    public function get_cost($CostTypeID, $TSID, $SID, $CostDetailID = NULL) {

//        $date = $this->m_datetime->getDateToday();

        $this->db->select('TSID,CostTypeName,CostDetail,OtherCostDetail,CostNote,SUM(CostValue) as Total,cost.CreateBy AS CreateBy');
        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID', 'left');
        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');

        if ($CostDetailID == NULL) {
            $this->db->where('cost_detail.CostDetailID !=', 1);
        } else {
            $this->db->where('cost_detail.CostDetailID', $CostDetailID);
        }

//        $this->db->where('cost.CostDate', $date);
        $this->db->where('cost.CostTypeID', $CostTypeID);
        $this->db->where('cost.SID', $SID);
        $this->db->where('t_schedules_day_has_cost.TSID', $TSID);
        $this->db->where('cost.CreateBy', $this->m_user->get_user_id());

        $this->db->group_by('cost.CostDetailID');

        $query = $this->db->get('cost');
        return $query->result_array();
    }

    public function get_post_form_send() {
        $RCode = $this->input->post('RCode');
        $VTID = $this->input->post('VTID');
        $SID = $this->input->post('SID');
        $Total = $this->input->post('Total');
        $Vage = $this->input->post('Vage');
        $Net = (int) $Total - (int) $Vage;
        $ReportNote = $this->input->post('ReportNote');

        $TSID = $this->input->post('TSID');

        $ReportID = $this->gennerate_report_id($RCode, $VTID, $SID);
        if (!empty($TSID) && $ReportID != FALSE) {
            $report = array(
                'ReportID' => $ReportID,
                'ReportDate' => $this->m_datetime->getDateToday(),
                'ReportTime' => $this->m_datetime->getTimeNow(),
                'Total' => $Total,
                'Vage' => $Vage,
                'Net' => $Net,
                'ReportStatus' => 0,
                'ReportNote' => $ReportNote,
                'SID' => $SID,
                'RCode' => $RCode,
                'VTID' => $VTID,
                'CreateBy' => $this->m_user->get_user_id(),
                'CreateDate' => $this->m_datetime->getDateTimeNow(),
            );

            $schedules_day_has_report = array();

            foreach ($TSID as $id) {
                $temp_schedules_day_has_report = array(
                    'TSID' => $id,
                    'ReportID' => $ReportID,
                );
                array_push($schedules_day_has_report, $temp_schedules_day_has_report);
            }

            $form_data = array(
                'report' => $report,
                'schedules_day_has_report' => $schedules_day_has_report,
            );

            return $form_data;
        } else {
            return FALSE;
        }

        return FALSE;
    }

    public function validation_form_add() {
        $this->form_validation->set_rules("Total", "รวม", 'trim|xss_clean|required');
        $this->form_validation->set_rules("Vage", "ค่าตอบแทน", 'trim|xss_clean|required');
        $this->form_validation->set_rules("Net", "คงเหลือ", 'trim|xss_clean|required');
        $this->form_validation->set_rules("ReportNote", "", 'trim|xss_clean');

        return TRUE;
    }

    public function check_schedule($date, $rid, $time_depart) {

        $this->db->where('RID', $rid);
        $this->db->where('Date', $date);
        $this->db->where('TimeDepart', $time_depart);
        $query_schedule = $this->db->get("t_schedules_day");

        return $query_schedule->result_array();
    }

    public function gennerate_report_id($RCode, $VTID, $SID) {
        $date_db = $this->m_datetime->getDateToday();

        $this->db->where('RCode', $RCode);
        $this->db->where('VTID', $VTID);
        $this->db->where('SID', $SID);
        $this->db->where('ReportDate', $date_db);
        $query = $this->db->get('report_day');

        $reports = $query->result_array();


        $num_report = count($reports);

        $ReportID = '';

        if ($num_report == 0) {
            //first report ID
            //วันที่
            $date = new DateTime();
            $ReportID .=$date->format("Ymd");
            //รหัสเส้นทาง
            $ReportID .=str_pad($RCode, 3, '0', STR_PAD_LEFT);
            //รหัสประเภทรถ
            $ReportID .=$VTID;
            //สถานี
            $ReportID .= str_pad($SID, 3, '0', STR_PAD_LEFT);
            $ReportID .=str_pad($num_report, 3, '0', STR_PAD_LEFT);
            return $ReportID;
        } elseif ($num_report > 0) {
            $report_id = (int)end($reports)['ReportID'];
            return $report_id+1;
        } else {
            return NULL;
        }
    }

}
