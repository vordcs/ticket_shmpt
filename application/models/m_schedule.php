<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class m_schedule extends CI_Model {

    public function set_form_view() {

        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');

        $date = $this->m_datetime->getDateToday();

        /* data schedules */
        $data_schedules = array();
        $vehicle_types = $this->m_route->get_vehicle_types();

        foreach ($vehicle_types as $vehicle_type) {
            $VTID = $vehicle_type['VTID'];
            $VTName = $vehicle_type['VTDescription'];

            $routes = $this->m_route->get_route_by_seller(NULL, $VTID);
            $num_route = count($routes);

            $route_in_type = array();

            foreach ($routes as $route) {

                $RCode = $route['RCode'];
                $detail_in_route = array();
                $routes_detail = $this->m_route->get_route_detail($RCode, $VTID); 
                foreach ($routes_detail as $rd) {
                    $RID = $rd['RID'];
                    $StartPoint = $rd['StartPoint'];
                    $source_name = $rd['RSource'];
                    $destination_name = $rd['RDestination'];
                    $route_detail_name = "$VTName เส้นทาง " . $RCode . ' ' . ' ' . $source_name . ' - ' . $destination_name;


                    $schedules = $this->m_schedule->get_schedule($date, $RCode, $VTID, $RID);
                    $stations = $this->m_station->get_station_sale_ticket($RCode, $VTID, $StartPoint);

                    $schedules_in_route = array();

                    foreach ($schedules as $schedule) {
                        $TSID = $schedule['TSID'];
                        $VCode = $schedule['VCode'];
                        $stations_in_schedule = array();

                        foreach ($stations as $station) {
                            $SID = $station['SID'];
//                            $TimeDepart = $this->get_time_depart($date, $RID, $TSID, $SID);
                            $TimeDepart = $this->time_depart($RID, $TSID, $SID);
                            $temp_station = array(
                                'SID' => $SID,
                                'StationName' => $station['StationName'],
                                'TimeDepart' => $TimeDepart,
                            );
                            array_push($stations_in_schedule, $temp_station);
                        }

                        $temp_schedules = array(
                            'TSID' => $TSID,
                            'VCode' => $VCode,
                            'stations' => $stations_in_schedule,
                        );
                        array_push($schedules_in_route, $temp_schedules);
                    }

                    $temp_route_detail = array(
                        'RID' => $RID,
                        'RouteName' => $route_detail_name,
                        'NumStation' => count($stations),
                        'stations' => $stations,
                        'schedules' => $schedules_in_route,
                    );
                    array_push($detail_in_route, $temp_route_detail);
                }

                $SourceName = $route['RSource'];
                $DestinationName = $route['RDestination'];
                $RouteName = "$VTName สาย " . $RCode . ' ' . ' ' . $SourceName . ' - ' . $DestinationName;

                $temp_route = array(
                    'RCode' => $RCode,
                    'RouteName' => $RouteName,
                    'routes_detail' => $detail_in_route,
                );
                array_push($route_in_type, $temp_route);
            }

            $temp_type = array(
                'VTID' => $VTID,
                'VTName' => $VTName,
                'NumberRoute' => $num_route,
                'routes' => $route_in_type,
            );

            array_push($data_schedules, $temp_type);
        }

        /* route seller */
        $data_routes_seller = array();
        foreach ($vehicle_types as $vehicle_type) {
            $VTID = $vehicle_type['VTID'];
            $VTName = $vehicle_type['VTDescription'];
            $routes_seller = $this->m_route->get_route_by_seller(NULL, $VTID);


            $num_route = count($routes_seller);
            $route_in_type = array();
            foreach ($routes_seller as $route) {
                $RCode = $route['RCode'];
                $SourceName = $route['RSource'];
                $DestinationName = $route['RDestination'];
                $RouteName = "สาย " . $RCode . ' ' . ' ' . $SourceName . ' - ' . $DestinationName;

                $seller_station_id = $route['SID'];
                $seller_station_name = $route['StationName'];
                $seller_station_seq = $route['Seq'];

                if ($route['SellerNote'] != NULL) {
                    $note = $route['SellerNote'];
                    $seller_station_name .= " ($note) ";
                }

                $stations = $this->m_station->get_stations($RCode, $VTID);
                $num_stations = count($stations);

                $temp_route = array(
                    'RCode' => $RCode,
                    'RouteName' => $RouteName,
                );
                if ($seller_station_seq == 1 || $seller_station_seq == $num_stations) {
                    array_push($route_in_type, $temp_route);
                }
            }

            $temp_type = array(
                'VTID' => $VTID,
                'VTName' => $VTName,
                'NumRoute' => $num_route,
                'routes' => $route_in_type,
            );
            if ($num_route > 0 && count($route_in_type) > 0) {
                array_push($data_routes_seller, $temp_type);
            }
        }


        $rs = array(
            'routes_seller' => $data_routes_seller,
            'data' => $data_schedules,
        );
        return $rs;
    }

//    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($date = NULL, $rcode = NULL, $vtid = NULL, $rid = NULL, $tsid = NULL) {
        $this->db->select('*,t_schedules_day.TSID as TSID,t_schedules_day.RID as RID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');
        $this->db->join('vehicles_type', ' vehicles.VTID = vehicles_type.VTID', 'left');
        $this->db->join('t_schedules_day_has_report', ' t_schedules_day.TSID = t_schedules_day_has_report.TSID', 'left');
        $this->db->join('t_schedules_day_has_cost', ' t_schedules_day_has_cost.TSID = t_schedules_day.TSID AND t_schedules_day_has_cost.TSID = t_schedules_day_has_report.TSID', 'left');

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        if ($rcode != NULL) {
            $this->db->where('t_routes.RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        if ($rid != NULL) {
            $this->db->where('t_schedules_day.RID', $rid);
        }
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day.TSID', $tsid);
        }

        $this->db->where('Date', $date);
        $this->db->where('t_schedules_day.ScheduleStatus', '1');

        $this->db->group_by('t_schedules_day.TSID');
        $this->db->order_by('TimeDepart', 'asc');

        $query_schedule = $this->db->get("t_schedules_day");
        return $query_schedule->result_array();
    }

    public function check_schedule($date, $rid, $time_depart) {

        $this->db->where('RID', $rid);
        $this->db->where('Date', $date);
        $this->db->where('TimeDepart', $time_depart);
        $query_schedule = $this->db->get("t_schedules_day");

        return $query_schedule->result_array();
    }

    public function time_depart($RID, $TSID, $SID = NULL) {
        /*
         * ข้อมูลเส้นทาง
         */
        $this->db->where('RID', $RID);
        $query_route = $this->db->get('t_routes');
        $route = $query_route->result_array()[0];

        $RCode = $route['RCode'];
        $VTID = $route['VTID'];
        $StartPoint = $route['StartPoint'];

        /*
         * ข้อมูลจุดจอด
         */
        $stations = $this->m_station->get_station_sale_ticket($RCode, $VTID, $StartPoint);
        $num_station = count($this->get_stations($RCode, $VTID));
        /*
         * ข้อมูลตารางเวลาเดินรถ
         */
        $schedules = $this->get_schedule(NULL, $RCode, $VTID, $RID, $TSID);

        $TimeDepart = NULL;
        $debug = array();
        foreach ($schedules as $schedule) {
            $StartTime = $schedule['TimeDepart'];
            $EndTime = $schedule['TimeArrive'];
            $temp = 0;
            foreach ($stations as $station) {
                $sid = $station['SID'];
                $travel_time = $station['TravelTime'];
                if ($station['Seq'] == '1') {
                    if ($StartPoint == 'S') {
                        $time = strtotime($StartTime);
                    } else {
                        $time = strtotime($EndTime);
                    }
                    $debug[0] = "สถานีต้นทาง";
                } elseif ($station['Seq'] == $num_station) {
                    if ($StartPoint == 'S') {
                        $time = strtotime($EndTime);
                    } else {
                        $time = strtotime($StartTime);
                    }
                    $debug[0] = "สถานีปลายทาง";
                } else {
                    $temp+=$travel_time;
                    $time = strtotime("+$temp minutes", strtotime($StartTime));
                    $debug[0] = "สถานีกลางทาง";
                }
                $time_depart = date('H:i', $time);
                if ($SID == $sid) {
                    $TimeDepart = $time_depart;
                    $debug[1] = $time_depart;
                    $debug[2] = "$sid";
                    break;
                }
            }
        }
        return $TimeDepart;
    }

    public function get_time_depart($date, $rid, $tsid = NULL, $sid = NULL) {
        /*
         * ข้อมูลเส้นทาง
         */
        $this->db->where('RID', $rid);
        $query_route = $this->db->get('t_routes');
        $route = $query_route->result_array()[0];

        $RCode = $route['RCode'];
        $VTID = $route['VTID'];
        $StartPoint = $route['StartPoint'];
        /*
         * ข้อมูลจุดจอด
         */
        $stations_in_route = array();
        $stations = $this->get_stations($RCode, $VTID);
        $num_station = count($stations);
        if ($StartPoint == "S") {
            $n = 0;
            foreach ($stations as $station) {
                $stations_in_route[$n] = $station;
                $n++;
            }
        }
        if ($StartPoint == "D") {
            $n = 0;
            for ($i = $num_station; $i >= 0; $i--) {
                foreach ($stations as $station) {
                    if ($station['Seq'] == $i) {
                        $stations_in_route[$n] = $station;
                        $n++;
                    }
                }
            }
        }

        /*
         * ข้อมูลตารางเวลาเดินรถ
         */
        $schedules = $this->get_schedule($date, $RCode, $VTID, $rid);
        if ($tsid != NULL) {
            $schedules = $this->get_schedule($date, $RCode, $VTID, $rid, $tsid);
        }

        $TimeDepart = array();
        foreach ($schedules as $schedule) {
            $start_time = $schedule['TimeDepart'];
            $rid = $schedule['RID'];
            $temp = 0;
            foreach ($stations_in_route as $s) {
                if ($s['IsSaleTicket'] == '1') {
                    $travel_time = $s['TravelTime'];
                    if ($s['Seq'] == '1' || $s['Seq'] == $num_station) {
                        $time = strtotime($start_time);
                    } else {
                        $temp+=$travel_time;
                        $time = strtotime("+$temp minutes", strtotime($start_time));
                    }

                    $time_depart = date('H:i', $time);
                    if ($sid != NULL && $sid == $s['SID']) {
                        $temp_time_depart = array(
                            'RID' => $rid,
                            'SID' => $s['SID'],
                            "StationName" => $s['StationName'],
                            'TimeDepart' => $time_depart,
                        );
                        $TimeDepart[0] = $temp_time_depart;
                        break;
                    } else {
                        $temp_time_depart = array(
                            'RID' => $rid,
                            'SID' => $s['SID'],
                            "StationName" => $s['StationName'],
                            'TimeDepart' => $time_depart,
                        );
                        array_push($TimeDepart, $temp_time_depart);
                    }
                }
            }
        }

        return $TimeDepart;
    }

    public function get_time_arrive($param) {
        
    }

    public function get_stations($rcode = null, $vtid = null, $sid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }

        $this->db->order_by('Seq', 'asc');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

}
