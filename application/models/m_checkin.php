<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_checkin extends CI_Model {

    public function get_check_in($date = NULL, $TSID = NULL, $SID = NULL, $EID = NULL, $CheckInID = NULL) {

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }

        if ($TSID != NULL) {
            $this->db->where('TSID', $TSID);
        }

        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }

        if ($EID != NULL) {
            $this->db->where('CreateBy', $EID);
        }

        if ($CheckInID != NULL) {
            $this->db->where('CheckInID', $CheckInID);
        }

        $this->db->where('DateCheckIn', $date);

        $query = $this->db->get('check_in');

        return $query->result_array();
    }

    public function get_checkin_time($TSID, $SID, $EID = NULL) {

//        $this->db->select('');

        if ($TSID != NULL) {
            $this->db->where('TSID', $TSID);
        }

        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }

        if ($EID != NULL) {
            $this->db->where('CreateBy', $EID);
        }

        $query = $this->db->get('check_in');

        return $query->row_array();
    }

    public function set_form_check_in() {
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_report');

        $data = array();
        $EID = $this->m_user->get_user_id();

        $date = $this->m_datetime->getDateToday();
        $routes = $this->m_route->get_route_by_seller();
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

                $schedules_in_route = array();
                $schedules = $this->m_schedule->get_schedule($date, $rcode, $vtid, $rid);
                foreach ($schedules as $schedule) {
                    $tsid = $schedule['TSID'];
                    $time_departs = $this->m_schedule->get_time_depart($date, $rid, $tsid, $seller_station_id);
                    $time_depart = '';
                    if (count($time_departs) > 0) {
                        $time_depart = $time_departs[0]['TimeDepart'];
                    }

                    $vid = $schedule['VID'];
                    $vcode = $schedule['VCode'];
                    if ($vcode == NULL) {
                        $vcode = '-';
                    }

                    $report_id = $this->m_report->check_report($EID, $tsid, $seller_station_id);

                    $data_checkin = $this->get_check_in($date, $tsid, $seller_station_id, $EID);
                    $checkin_id = NULL;
                    $checkin_time = NULL;

                    if (count($data_checkin) > 0) {
                        $checkin_id = $data_checkin[0]['CheckInID'];
                        $checkin_time = date('H:i', strtotime($data_checkin[0]['TimeCheckIn']));
                    }

                    $temp_schedules_in_route = array(
                        'TSID' => $tsid,
                        'TimeDepart' => $time_depart,
                        'VID' => $vid,
                        'VCode' => $vcode,
                        'ReportID' => $report_id,
                        'CheckInID' => $checkin_id,
                        'TimeCheckIn' => $checkin_time,
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

            if ($seller_station_seq == 1) {
                $start_point = 'S';
                array_pop($detail_in_route);
            } elseif ($seller_station_seq == $num_station) {
                $start_point = 'D';
                array_shift($detail_in_route);
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
                'StartPoint' => $start_point,
                'routes_detail' => $detail_in_route,
            );
            array_push($data, $temp_route);
        }
        return $data;
    }

    public function insert_checkin($data) {
        $rs = '';
        $EID = $this->m_user->get_user_id();
        $date = $this->m_datetime->getDateToday();

        $TSID = $data['TSID'];
        $SID = $data['SID'];

        $CheckIn = $this->get_check_in($date, $TSID, $SID, $EID);
        if (count($CheckIn) <= 0) {
            $this->db->insert('check_in', $data);
            if($this->db->affected_rows()==1){
//                $this->update_vehicles_current_stations();
            }            
            $rs = "INSERT -> $TSID";
        } else {
            $CheckInID = $CheckIn[0]['CheckInID'];
            $this->update_checkin($CheckInID, $data);
            $rs = "UPDATE -> $TSID";
        }  
        return $rs;
    }

    public function update_checkin($CheckInID, $data) {
        $this->db->where('CheckInID', $CheckInID);
        $this->db->update('check_in', $data);
        if ($this->db->affected_rows() == 1) {
            return $CheckInID;
        } else {
            return NULL;
        }
    }

    public function get_post_form_add($TSID, $SID) {
        $data_form_add = array(
            'CheckInID' => $this->gennerate_checkin_id($SID),
            'SID' => $SID,
            'TSID' => $TSID,
            'TimeCheckIn' => $this->m_datetime->getTimeNow(),
            'DateCheckIn' => $this->m_datetime->getDateToday(),
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
            'CreateBy' => $this->m_user->get_user_id(),
        );

        return $data_form_add;
    }

    public function get_post_form_edit() {

        $data_form_edit = array(
            'TimeCheckIn' => $this->m_datetime->getTimeNow(),
            'DateCheckIn' => $this->m_datetime->getDateToday(),
            'UpdateDate' => $this->m_datetime->getDatetimeNow(),
            'UpdateBy' => $this->m_user->get_user_id(),
        );

        return $data_form_edit;
    }

    public function gennerate_checkin_id($SID) {
        $CheckInID = '';
        $date_ = $this->m_datetime->getDateToday();
        $checkin = $this->get_check_in($date_, NULL, $SID);
        $num_checkin = count($checkin);
        //วันที่
        $date = new DateTime();
        $CheckInID .=$date->format("Ymd");
        //สถานี
        $CheckInID .= str_pad($SID, 3, '0', STR_PAD_LEFT);
        //run number
        $CheckInID .= str_pad($num_checkin, 3, '0', STR_PAD_LEFT);

        return $CheckInID;
    }

    public function check_checkin($EID = NULL, $TSID = NULL, $SID = NULL) {

        if ($EID == NULL) {
            $EID = $this->m_user->get_user_id();
        }
        if ($TSID != NULL) {
            $this->db->where('vehicles_check_in.TSID', $TSID);
        }
        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }
        $this->db->where('DateCheckIn', $this->m_datetime->getDateToday());
        $this->db->where('vehicles_check_in.CreateBy', $EID);
//        $this->db->or_where('vehicles_check_in.UpdateBy', $EID);

        $query = $this->db->get('vehicles_check_in');
        if ($query->num_rows() > 0) {
            $rs = $query->row_array()['TimeCheckIn'];
        } else {
            $rs = NULL;
        }
        return $rs;
    }

    public function update_vehicles_current_stations($VID, $SID, $Seq, $Time) {
        
    }

}
