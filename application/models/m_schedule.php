<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class m_schedule extends CI_Model {

//    คืนค่าเวลาข้อมูลตารางเวลาเดิน ออกจากจุกเริ่มต้นของเเต่ละ RID
    public function get_schedule($date = NULL, $rcode = NULL, $vtid = NULL, $rid = NULL, $tsid = NULL) {
        $this->db->select('*,t_schedules_day.TSID as TSID,t_schedules_day.RID as RID');
        $this->db->join('t_routes', ' t_schedules_day.RID = t_routes.RID ', 'left');
        $this->db->join('vehicles_has_schedules', ' vehicles_has_schedules.TSID = t_schedules_day.TSID', 'left');
        $this->db->join('vehicles', ' vehicles.VID = vehicles_has_schedules.VID', 'left');
        $this->db->join('t_schedules_day_has_report', ' t_schedules_day.TSID = t_schedules_day_has_report.TSID', 'left');

        if ($date != NULL) {
            $this->db->where('Date', $date);
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
        $this->db->order_by('SeqNo', 'asc');
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
                            'TimeDepart' => $time_depart,
                        );
                        $TimeDepart[0] = $temp_time_depart;
                        break;
                    } else {
                        $temp_time_depart = array(
                            'RID' => $rid,
                            'SID' => $s['SID'],
                            'TimeDepart' => $time_depart,
                        );
                        array_push($TimeDepart, $temp_time_depart);
                    }
                }
            }
        }

        return $TimeDepart;
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
