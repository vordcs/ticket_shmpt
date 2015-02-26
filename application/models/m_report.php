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

    public function insert_report($data) {
//        $this->db->truncate('report_day');
//        $this->db->truncate('t_schedules_day_has_report');


        $rs = array();
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

        $routes = $this->m_route->get_route_by_seller();

        foreach ($routes as $route) {
            $rcode = $route['RCode'];
            $vtid = $route['VTID'];
            $vt_name = $route['VTDescription'];
            $source = $route['RSource'];
            $destination = $route['RDestination'];
            $route_name = "$vt_name " . $rcode . ' ' . ' ' . $source . ' - ' . $destination;

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
            $Reports = $this->get_report($date, $rcode, $vtid, $seller_station_id);
            $temp_route = array(
                'RCode' => $rcode,
                'VTID' => $vtid,
                'RouteName' => $route_name,
                'seller_station_id' => $seller_station_id,
                'seller_station_seq' => $seller_station_seq,
                'seller_station_name' => $seller_station_name,
                'start_point' => $start_point,
                'Reports' => $Reports,
                'routes_detail' => $detail_in_route,
                'cost_along_road' => $cost_along_road,
            );
            array_push($rs, $temp_route);
        }

        return $rs;
    }

    public function set_form_send($RCode, $VTID, $SID) {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');

        $data = array();
        $EID = $this->m_user->get_user_id();
        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller($RCode, $VTID);

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

                    $temp_schedules_in_route = array(
                        'TSID' => $tsid,
                        'TimeDepart' => $time_depart,
//                        'VID' => $vid,
                        'VCode' => $vcode,
                        'TotalIncome' => $income,
                        'TotalOutcome' => $outcome,
                        'TotalAlongRoad' => $along_road,
                        'Total' => $total,
                        'ReportID' => $report_id,
                        'tickets' => $tickets_in_schedule,
                        'Income' => $income_in_schedule,
                        'Outcome' => $outcome_in_schedule,
                        'AlongRoad' => $road_in_schedule,
                    );
                    if ($report_id == NULL && ($income > 0 || $outcome > 0 || $along_road > 0)) {
                        array_push($schedules_in_route, $temp_schedules_in_route);
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
        $rs = array(
            'data' => $data,
            'form' => ''
        );
        $this->session->set_flashdata('RCode', $rcode);
        $this->session->set_flashdata('VTID', $vtid);
        return $rs;
    }

    public function set_form_print($ReportID, $RCode = NULL, $VTID = NULL, $SID = NULL) {


        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        $this->load->model('m_cost');

        $data = array();
        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller($RCode, $VTID);

        $Report = $this->get_report($date, NULL, NULL, NULL, $ReportID)[0];

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
                    $report_id = $schedule['ReportID'];
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
                    if ($report_id == $ReportID) {
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

                    $temp_schedules_in_route = array(
                        'ReportID' => $report_id,
                        'TSID' => $tsid,
                        'TimeDepart' => $time_depart,
//                        'VID' => $vid,
                        'VCode' => $vcode,
                        'TotalIncome' => $income,
                        'TotalOutcome' => $outcome,
                        'TotalAlongRoad' => $along_road,
                        'Total' => $total,
                        'tickets' => $tickets_in_schedule,
                        'Income' => $income_in_schedule,
                        'Outcome' => $outcome_in_schedule,
                        'AlongRoad' => $road_in_schedule,
                    );
                    if ($report_id == $ReportID && ($income > 0 || $outcome > 0 || $along_road > 0)) {
                        array_push($schedules_in_route, $temp_schedules_in_route);
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
                'ReportID' => $ReportID,
                'ReportDate' => $Report['ReportDate'],
                'ReportTime' => $Report['ReportTime'],
                'Total' => $Report['Total'],
                'Vage' => $Report['Vage'],
                'Net' => $Report['Net'],
                'ReportStatus' => $Report['ReportStatus'],
                'ReportNote' => $Report['ReportNote'],
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

        return $data;
    }

    public function get_ticket_by_seller($TSID, $SourceID) {
        $this->db->select('SourceID,SourceName,DestinationID,DestinationName,PriceSeat,COUNT(TicketID) as NumberTicket,SUM(PriceSeat) as Total');
        $this->db->where('SourceID', $SourceID);
        $this->db->where('TSID', $TSID);
        $this->db->where('Seller', $this->m_user->get_user_id());
        $this->db->group_by('SourceID,DestinationID');
        $query = $this->db->get('ticket_sale');
        return $query->result_array();
    }

    public function get_cost($CostTypeID, $TSID, $SID, $CostDetailID = NULL) {

        $date = $this->m_datetime->getDateToday();

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
        $date = $this->m_datetime->getDateToday();
//        $Report = $this->get_report($date, $RCode, $VTID,$SID);
        $this->db->where('RCode', $RCode);
        $this->db->where('VTID', $VTID);
        $this->db->where('SID', $SID);
        $this->db->where('ReportDate', $date);
        $query = $this->db->get('report_day');

        $Report = $query->result_array();


        $num_report = count($Report);

        $ReportID = '';

        if ($num_report >= 0) {
            //วันที่
            $date = new DateTime();
            $ReportID .=$date->format("Ymd");
            //รหัสเส้นทาง
            $ReportID .=str_pad($RCode, 3, '0', STR_PAD_LEFT);
            //รหัสประเภทรถ
            $ReportID .=$VTID;
            //สถานี
            $ReportID .= str_pad($SID, 2, '0', STR_PAD_LEFT);
            //run number
            $ReportID .=str_pad($num_report, 2, '0', STR_PAD_LEFT);
            return $ReportID;
        }
        return FALSE;
    }

}
