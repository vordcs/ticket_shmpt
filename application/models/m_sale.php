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

    public function set_form_sale($route, $s_station, $d_station, $schedule = NULL, $fare = NULL) {
        $rid = $route['RID'];
        $rcode = $route['RCode'];
        $vtid = $route['VTID'];
        $vt_name = $route['VTDescription'];
        $source = $route['RSource'];
        $desination = $route['RDestination'];
        $route_name = "$vt_name  $rcode  $source - $desination";

        $this->load->model('m_station');
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

//        รหัสตารางเวลาเดินรถ
        $tsid = '';
//        เวลาออกจาก $source_id
        $time_depart = "";
//        เวลาถึง $destination_id
        $time_arrive = "-";
//        วันเดินทาง
        $date = '';
//        ราคาค่าโดยสาร
        $price = 0;
        $price_dis = 0;

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
                $date = $this->m_datetime->setDateThai($schedule['Date']);
            }
        }
        if (count($fare) > 0 && $fare != NULL) {
            $price = $fare['Price'];
            $price_dis = $fare['PriceDicount'];
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
            'type' => "text",
            'name' => "Date",
            'id' => "Date",
            'class' => "form-control text-center",
            'readonly' => "",
            'value' => $date,
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
            'TSID' => form_input($i_TSID),
            'SourceID' => form_input($i_SourceID),
            'SourceName' => form_input($i_SourceName),
            'DestinationID' => form_input($i_DestinationID),
            'DestinationName' => form_input($i_DestinationName),
            'TimeDepart' => form_input($i_TimeDepart),
            'TimeArrive' => form_input($i_TimeArrive),
            'Date' => form_input($i_Date),
            'Price' => form_input($i_Price),
            'PriceDicount' => form_input($i_PriceDicount),
            'VCode' => form_input($i_VCode),
            'Seat' => "",
        );
        return $form_sale_ticket;
    }

    public function get_post_form_sale() {

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

        $Date = $this->m_datetime->strDateThaiToDB($this->input->post('Date'));
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
        $ticket = array();

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
            array_push($ticket, $temp_ticket);
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
        return $ticket;
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
