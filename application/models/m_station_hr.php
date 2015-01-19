<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_station_hr extends CI_Model {

    public function insert_station($rcode, $vtid, $data) {
        $rs = array();
        $i = 0;
        foreach ($data['station'] as $station) {
            $sid = $this->is_exits_station($rcode, $vtid, $station['StationName']);
            if ($sid == NULL) {
                $this->db->insert('t_stations', $station);
                $sid = $this->db->insert_id();

                $route = array(
                    'RCode' => $rcode,
                    'VTID' => $vtid,
                );
                $this->db->where('SID', $sid);
                $this->db->update('t_stations', $route);
                $rs[$i] = 'INSERT -> ' . $station['StationName'];
            } else {
                $this->db->where('SID', $sid);
                $this->db->update('t_stations', $station);
                $rs[$i] = 'UPDATE -> ' . $sid . '  ' . $station['StationName'];
            }
            $i++;
        }
        return $rs;
    }

    public function update_station($rcode, $vtid, $data) {
        $rs = array();
        $station_db = $this->get_stations($rcode, $vtid);
        $source = $station_db[0]['StationName'];
        $source_id = $station_db[0]['SID'];
        $destination = $station_db[count($station_db) - 1]['StationName'];
        $des_sid = $station_db[count($station_db) - 1]['SID'];

//        $rs[99] = $des_sid . ' -> ' . $destination;

        if (count($data['station']) <= count($station_db) + 2) {
            for ($j = 1; $j < count($station_db); $j++) {
                $station_name = $station_db[$j]['StationName'];
                $id = $station_db[$j]['SID'];
                $del = TRUE;
                foreach ($data['station'] as $s) {
                    if ($s['StationName'] == $station_name) {
                        $del = FALSE;
                    }
                }
                if ($del && $station_name != $destination) {
                    $this->delete_station($rcode, $vtid, $id);
                    $rs[99][$j] = 'DELETE Station ->' . $station_name;
                }
            }
        }


        $i = 0;
        foreach ($data['station'] as $station) {
            $station_name = $station['StationName'];
            $station['UpdateDate'] = $this->m_datetime->getDatetimeNowTH();

            $sid = $this->is_exits_station($rcode, $vtid, $station['StationName']);

            if ($station_name == $destination) {
                $this->db->where('SID', $des_sid);
                $this->db->update('t_stations', $station);
                $rs[$i] = 'destination UPDATE Seq ->' . $station_name;
            } elseif ($station_name == $source) {
                $this->db->where('SID', $source_id);
                $this->db->update('t_stations', $station);
                $rs[$i] = 'source UPDATE Seq ->' . $station_name;
            } elseif ($sid != NULL) {
                $this->db->where('SID', $sid);
                $this->db->update('t_stations', $station);
                $rs[$i] = 'UPDATE Seq ->' . $station_name;
            } elseif (in_array('StationName', $station_db, $station_name) == FALSE) {
                $station['CreateDate'] = $this->m_datetime->getDatetimeNowTH();
                $this->db->insert('t_stations', $station);
                $sid_insert = $this->db->insert_id();

                $route = array(
                    'RCode' => $rcode,
                    'VTID' => $vtid,
                );
                $this->db->where('SID', $sid_insert);
                $this->db->update('t_stations', $route);
                $rs[$i] = 'INSERT Station ->' . $station_name;
            }

            $i++;
        }


        return $rs;
    }

    public function delete_station($rcode, $vtid, $sid = NULL) {
        $station = $this->get_stations($rcode, $vtid);
        if ($sid == NULL) {

            foreach ($station as $s) {
//        detete all stations
                $this->db->where('RCode', $rcode);
                $this->db->where('VTID', $vtid);
                $this->db->where('SID', $s['SID']);
                $this->db->delete('t_stations');
            }
        } else {
//        detete station by SID
            $this->db->where('SID', $sid);
            $this->db->delete('t_stations');
        }
    }

    public function delete_fares($rcode, $vtid, $station_id) {
        $this->db->where('RCode', $rcode);
        $this->db->where('VTID', $vtid);

//                delete fares and rate 
//                $this->db->where('SourceID', $s['SID']);
        $this->db->delete('f_fares');
//
//                $this->db->where('DestinationID', $s['SID']);
//                $this->db->delete('f_fares');
    }

    public function get_stations($rcode = null, $vtid = null, $sid = NULL, $seq = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }
        if ($seq != NULL) {
            $this->db->where('Seq', $seq);
        }
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_stations_detail($rcode = null, $vtid = null, $sid = NULL, $seq = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }
        if ($seq != NULL) {
            $this->db->where('Seq', $seq);
        }
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_stations_sale_ticket($rcode = NULL, $vtid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        $this->db->where('IsSaleTicket', 1);
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_fares($rcode, $vtid, $source_id = NULL, $destination_id = NULL) {

        $this->db->join('f_fares_has_rate', 'f_fares_has_rate.FID=f_fares.FID');
        $this->db->join('f_rate', 'f_rate.RateID=f_fares_has_rate.RateID');

        $this->db->where('RCode', $rcode);
        $this->db->where('VTID', $vtid);
        if ($source_id != NULL) {
            $this->db->where('SourceID', $source_id);
        }
        if ($destination_id != NULL) {
            $this->db->where('DestinationID', $destination_id);
        }

        $query = $this->db->get('f_fares');

        return $query->result_array();
    }

    public function set_form_add($rcode = NULL, $vtid = NULL) {
        $station_name = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');
        $stop_time = $this->input->post('StopTime');

        $route = $this->m_route->get_route($rcode, $vtid);
        $travel_time_total = $route[0]['Time'];

        $IsSaleTiket = array();
        $StationName = array();
        $TravelTime = array();
        $StopTime = array();

        $i_TimeTotal = array(
            'type' => "hidden",
            'name' => "TimeTotal",
            'id' => "TimeTotal",
            'class' => "form-control",
            'readonly' => "",
            'value' => $travel_time_total,
        );
        $i_Source = array(
            'name' => 'Source',
            'value' => $source,
            'placeholder' => 'ต้นทาง',
            'readonly' => '',
            'class' => 'form-control'
        );

        $i_Destination = array(
            'name' => 'Destination',
            'value' => $desination,
            'placeholder' => 'ปลายทาง',
            'readonly' => '',
            'class' => 'form-control');
        if (!empty($station_name) && count($station_name) > 0) {
            for ($i = 0; $i < count($station_name); $i++) {
                $checked = '';
                $display = 'none';

                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $checked = 'checked';
                    $display = 'block';
                }
                if (!empty($stop_time) && array_key_exists($i, $stop_time) == FALSE) {
                    $stop_time [$i] = NULL;
                }

                $i_IsSaleTiket = "<input "
                        . "type=\"checkbox\"  "
                        . "name=\"IsSaleTicket[$i]\" "
                        . "class=\"IsSaleTicket\" "
                        . "onclick=\"ShowItemp('$i')\" "
                        . "value=\"$i\" "
                        . "$checked";

                $i_StationName = array(
                    'name' => 'StationName[]',
                    'value' => (set_value("StationName[$i]") == NULL) ? $station_name [$i] : set_value("StationName[$i]"),
                    'placeholder' => 'ชื่อจุดจอด',
                    'class' => 'form-control text-center'
                );

                $i_TravelTime = array(
                    'id' => "TravelTime$i",
                    'name' => 'TravelTime[]',
                    'value' => (set_value("TravelTime[$i]") == NULL) ? $travel_time [$i] : set_value("TravelTime[$i]"),
                    'placeholder' => '',
                    'class' => 'form-control text-center',
                    'style' => "display: $display;>",
                );
                $i_StopTime = array(
                    'id' => "StopTime$i",
                    'name' => 'StopTime[]',
                    'value' => (set_value("StopTime[$i]") == NULL) ? $stop_time [$i] : set_value("StopTime[$i]"),
                    'placeholder' => '',
                    'class' => 'form-control text-center',
                    'style' => "display: $display;>",
                );

                array_push($IsSaleTiket, $i_IsSaleTiket);
                array_push($StationName, form_input($i_StationName));
                array_push($TravelTime, form_input($i_TravelTime));
                array_push($StopTime, form_input($i_StopTime));
            }
        }

        $i_StationName_ = '';

        $form_add = array(
            'form' => form_open('station/add/' . $rcode . '/' . $vtid, array('class' => 'form-horizontal', 'id' => 'form_station')),
            'TimeTotal' => form_input($i_TimeTotal),
            'Source' => form_input($i_Source),
            'Destination' => form_input($i_Destination),
            'station' => $i_StationName_,
            'IsSaleTiket' => $IsSaleTiket,
            'StationName' => $StationName,
            'TravelTime' => $TravelTime,
            'StopTime' => $StopTime,
        );

        return $form_add;
    }

    public function set_form_edit($rcode = NULL, $vtid = NULL) {
        $stations_db = $this->get_stations($rcode, $vtid);
        $station_post = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');
        $stop_time = $this->input->post('StopTime');

        $number_station = 0;
        $i_Station = '';
        $source = $stations_db[0]['StationName'];
        $desination = $stations_db[count($stations_db) - 1]['StationName'];

        $route = $this->m_route->get_route($rcode, $vtid);
        $travel_time_total = $route[0]['Time'];

        $i_TimeTotal = array(
            'type' => "hidden",
            'name' => "TimeTotal",
            'id' => "TimeTotal",
            'class' => "form-control",
            'readonly' => "",
            'value' => $travel_time_total,
        );

        $i_Source = array(
            'name' => 'Source',
            'value' => $source,
            'placeholder' => 'ต้นทาง',
            'readonly' => '',
            'class' => 'form-control text-center');

        $i_Destination = array(
            'name' => 'Destination',
            'value' => $desination,
            'placeholder' => 'ปลายทาง',
            'readonly' => '',
            'class' => 'form-control text-center');


        $IsSaleTiket = array();
        $StationName = array();
        $TravelTime = array();
        $StopTime = array();

        if (!empty($station_post) && count($station_post) > 0) {
            for ($i = 0; $i < count($station_post); $i++) {
                $checked = '';
                $display = 'none';
                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $checked = 'checked';
                    $display = 'block';
                }
                if (!empty($stop_time) && array_key_exists($i, $stop_time) == FALSE) {
                    $stop_time [$i] = NULL;
                }
                $i_IsSaleTiket = "<input "
                        . "type=\"checkbox\"  "
                        . "name=\"IsSaleTicket[$i]\" "
                        . "class=\"IsSaleTicket\" "
                        . "onclick=\"ShowItemp('$i')\" "
                        . "value=\"$i\" "
                        . "$checked";

                $i_StationName = array(
                    'name' => 'StationName[]',
                    'value' => (set_value("StationName[$i]") == NULL) ? $station_post [$i] : set_value("StationName[$i]"),
                    'placeholder' => 'ชื่อจุดจอด',
                    'class' => 'form-control text-center'
                );

                $i_TravelTime = array(
                    'id' => "TravelTime$i",
                    'name' => 'TravelTime[]',
                    'value' => (set_value("TravelTime[$i]") == NULL) ? $travel_time [$i] : set_value("TravelTime[$i]"),
                    'placeholder' => '',
                    'class' => 'form-control text-center',
                    'style' => "display: $display;>",
                );

                $i_StopTime = array(
                    'id' => "StopTime$i",
                    'name' => 'StopTime[]',
                    'type' => "text",
                    'class' => "form-control text-center ",
                    'style' => "display: $display;",
                    'value' => (set_value("StopTime[$i]") == NULL) ? $stop_time [$i] : set_value("StopTime[$i]"),
                );

                array_push($IsSaleTiket, $i_IsSaleTiket);
                array_push($StationName, form_input($i_StationName));
                array_push($TravelTime, form_input($i_TravelTime));
                array_push($StopTime, form_input($i_StopTime));
            }
        } else {
            $i = 0;
            foreach ($stations_db as $s) {
                $station_name = $s['StationName'];
                $is_sale_ticket = $s['IsSaleTicket'];
                $travel_time = $s['TravelTime'];
                $stop_time = $s['StopTime'];

                if ($station_name != $source && $station_name != $desination) {
                    $checked = '';
                    $display = 'none';
                    if ($s['IsSaleTicket'] == 1) {
                        $checked = 'checked';
                        $display = 'block';
                    }

                    $i_IsSaleTiket = "<input "
                            . "type=\"checkbox\"  "
                            . "name=\"IsSaleTicket[$i]\" "
                            . "class=\"IsSaleTicket\" "
                            . "onclick=\"ShowItemp('$i')\""
                            . "value=\"$i\" "
                            . "$checked";

                    $i_StationName = array(
                        'name' => 'StationName[]',
                        'value' => (set_value("StationName[$i]") == NULL) ? $station_name : set_value("StationName[$i]"),
                        'placeholder' => 'ชื่อจุดจอด',
                        'class' => 'form-control text-center'
                    );

                    $i_TravelTime = array(
                        'id' => "TravelTime$i",
                        'name' => 'TravelTime[]',
                        'value' => (set_value("TravelTime[$i]") == NULL) ? $travel_time : set_value("TravelTime[$i]"),
                        'placeholder' => '',
                        'class' => 'form-control text-center',
                        'style' => "display: $display;>",
                    );

                    $i_StopTime = array(
                        'id' => "StopTime$i",
                        'name' => 'StopTime[]',
                        'type' => "text",
                        'class' => "form-control text-center ",
                        'style' => "display: $display;",
                        'value' => (set_value("StopTime[$i]") == NULL) ? $stop_time : set_value("StopTime[$i]"),
                    );

                    array_push($IsSaleTiket, $i_IsSaleTiket);
                    array_push($StationName, form_input($i_StationName));
                    array_push($TravelTime, form_input($i_TravelTime));
                    array_push($StopTime, form_input($i_StopTime));

                    $i++;
                }
            }
        }

        $form_edit = array(
            'form' => form_open('station/edit/' . $rcode . '/' . $vtid, array('class' => 'form-horizontal', 'id' => 'form_station')),
            'TimeTotal' => form_input($i_TimeTotal),
            'Source' => form_input($i_Source),
            'Destination' => form_input($i_Destination),
            'station' => $i_Station,
            'IsSaleTiket' => $IsSaleTiket,
            'StationName' => $StationName,
            'TravelTime' => $TravelTime,
            'StopTime' => $StopTime,
        );

        return $form_edit;
    }

    public function get_post_form_add() {

        $station_name = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');
        $stop_time = $this->input->post('StopTime');
        $source = $this->input->post('Source');
        $destination = $this->input->post('Destination');

        $TimeTotal = $this->input->post('TimeTotal');

        $station = array();
        $seq = 1;
        $first = array(
            'StationName' => $source,
            'TravelTime' => $TimeTotal,
            'IsSaleTicket' => '1',
            'StopTime' => '',
            'Seq' => $seq,
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );
        $seq++;
        array_push($station, $first);
        if (!empty($station_name) && count($station_name) > 0) {
            for ($i = 0; $i < count($station_name); $i++) {
                $st = 0;

                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $st = 1;
                } else {
                    $travel_time[$i] = '';
                    $stop_time[$i] = '';
                }
                $temp = array(
                    'StationName' => $station_name[$i],
                    'TravelTime' => $travel_time[$i],
                    'IsSaleTicket' => $st,
                    'StopTime' => $stop_time[$i],
                    'Seq' => $seq,
                    'CreateDate' => $this->m_datetime->getDatetimeNow(),
                );
                array_push($station, $temp);
                $seq++;
            }
        }

        $last = array(
            'StationName' => $destination,
            'TravelTime' => $TimeTotal,
            'IsSaleTicket' => '1',
            'StopTime' => '',
            'Seq' => $seq,
            'CreateDate' => $this->m_datetime->getDatetimeNow(),
        );

        array_push($station, $last);

        $form_data = array(
//            'IsSaleTicket' => $is_sale_ticket,
//            'StationName' => $station_name,
//            'TravelTime' => $travel_time,            
            'station' => $station,
        );

        return $form_data;
    }

    public function get_post_form_edit() {
        $station_name = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');
        $stop_time = $this->input->post('StopTime');
        $TimeTotal = $this->input->post('TimeTotal');

        $source = $this->input->post('Source');
        $destination = $this->input->post('Destination');

        $station = array();
        $seq = 1;
        $first = array(
            'StationName' => $source,
            'TravelTime' => $TimeTotal,
            'IsSaleTicket' => '1',
            'StopTime' => '',
            'Seq' => $seq,
            'UpdateDate' => $this->m_datetime->getDatetimeNow(),
        );
        $seq++;
        array_push($station, $first);
        if (!empty($station_name) && count($station_name) > 0) {
            for ($i = 0; $i < count($station_name); $i++) {
                $st = 0;

                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $st = 1;
                } else {
                    $travel_time[$i] = '';
                    $stop_time[$i] = '';
                }
                $temp = array(
                    'StationName' => $station_name[$i],
                    'TravelTime' => $travel_time[$i],
                    'IsSaleTicket' => $st,
                    'StopTime' => $stop_time[$i],
                    'Seq' => $seq,
                    'UpdateDate' => $this->m_datetime->getDatetimeNow(),
                );
                array_push($station, $temp);
                $seq++;
            }
        }

        $last = array(
            'StationName' => $destination,
            'TravelTime' => $TimeTotal,
            'IsSaleTicket' => '1',
            'StopTime' => '',
            'Seq' => $seq
        );

        array_push($station, $last);

        $form_data = array(
//            'IsSaleTicket' => $is_sale_ticket,
//            'StationName' => $station_name,
//            'TravelTime' => $travel_time,            
            'station' => $station,
        );

        return $form_data;
    }

    public function validation_form_add() {
        $station_name = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $stop_time = $this->input->post('StopTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');

        $this->form_validation->set_rules("Source", "สถานีต้นทาง", 'trim|required|xss_clean');
        $this->form_validation->set_rules("Destination", "สถานีปลายทาง", 'trim|required|xss_clean');

        if (!empty($station_name) && count($station_name) > 0) {
            for ($i = 0; $i < count($station_name); $i++) {
                $this->form_validation->set_rules("StationName[$i]", "ชื่อจุดจอด $i", 'trim|required|xss_clean');
                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $this->form_validation->set_rules("TravelTime[$i]", "เวลาเดินทาง $i", 'trim|required|numeric|xss_clean');
                    $this->form_validation->set_rules("StopTime[$i]", "เวลาพัก $i", 'trim|required|numeric|xss_clean');
                }
            }
        }
        return TRUE;
    }

    public function validation_form_edit() {
        $station_name = $this->input->post('StationName');
        $travel_time = $this->input->post('TravelTime');
        $stop_time = $this->input->post('StopTime');
        $is_sale_ticket = $this->input->post('IsSaleTicket');

        $this->form_validation->set_rules("Source", "สถานีต้นทาง", 'trim|required|xss_clean');
        $this->form_validation->set_rules("Destination", "สถานีปลายทาง", 'trim|required|xss_clean');

        if (!empty($station_name) && count($station_name) > 0) {
            for ($i = 0; $i < count($station_name); $i++) {
                $this->form_validation->set_rules("StationName[$i]", "ชื่อจุดจอด $i", 'trim|required|xss_clean');
                if (!empty($is_sale_ticket) && array_key_exists($i, $is_sale_ticket)) {
                    $this->form_validation->set_rules("TravelTime[$i]", "เวลาเดินทาง $i", 'trim|required|numeric|xss_clean');
                    $this->form_validation->set_rules("StopTime[$i]", "เวลาพัก $i", 'trim|required|numeric|xss_clean');
                }
            }
        }
        return TRUE;
    }

    public function is_exits_station($rcode, $vtid, $station_name, $seq = NULL) {

        $this->db->where('RCode', $rcode);
        $this->db->where('VTID', $vtid);
        $this->db->where('StationName', $station_name);

        if ($seq != NULL) {
            $this->db->where('Seq', $seq);
        }

        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        if (count($rs) > 0) {
            return $rs[0]['SID'];
        }
        return NULL;
    }

}
