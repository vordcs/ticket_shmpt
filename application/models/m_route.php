<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_route extends CI_Model {

    public function get_route($rcode = NULL, $vtid = NULL, $rid = NULL) {

        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');

        if ($rid != NULL) {
            $this->db->where('RID', $rid);
        } else {
            $this->db->where('StartPoint', 'S');
        }
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }
        $this->db->group_by(array('RCode', 't_routes.VTID'));

//        $this->db->order_by('StartPoint');
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function search_route($rcode = NULL, $source = NULL, $destination = NULL, $rid = NULL) {

        if ($rcode != NULL)
            $this->db->where('RCode', $rcode);
        if ($source != NULL)
            $this->db->where('RSource', $source);
        if ($destination != NULL)
            $this->db->where('RDestination', $destination);
        $this->db->where('StartPoint', 'S');
//        $this->db->group_by(array('RCode', 't_routes.VTID'));
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function get_route_detail($rcode = NULL, $vtid = NULL) {
        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('t_routes.VTID', $vtid);
        }

        $this->db->order_by('StartPoint', 'DESC');
        $query = $this->db->get('t_routes');
        return $query->result_array();
    }

    public function get_vehicle_types($id = NULL) {
        if ($id != NULL) {
            $this->db->where('VTID', $id);
        }
        $temp = $this->db->get('vehicles_type');
        return $temp->result_array();
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

    public function get_stations_source($rcode = null, $vtid = null, $sid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID ', $sid);
        }
        $this->db->where('IsSaleTicket ', 1);
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_stations_destination($rcode = null, $vtid = null, $sid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID ', $sid);
        }
        $this->db->order_by('Seq');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function set_form_search_route($vtid = NULL, $rcode = NULL, $source_id = NULL, $destination_id = NULL) {

        //ข้อมูลเส้นทาง
        $i_RCode[0] = 'เลือกเส้นทาง';
        foreach ($this->get_route(NULL, $vtid) as $value) {
            $i_RCode[$value['RCode']] = $value['VTDescription'] . '  ' . $value['RCode'] . ' ' . $value['RSource'] . ' - ' . $value['RDestination'];
        }
        $i_source[0] = 'เลือกเส้นทาง';
        if ($vtid != NULL && $rcode != NULL) {
            $i_source[0] = 'เลือกเส้นทาง';
            foreach ($this->get_stations_source($rcode, $vtid)as $s) {
                $i_source[$s['SID']] = $s['StationName'];
            }
        }
        $i_destination[0] = $source_id; //'เลือกเส้นทาง';
        if ($vtid != NULL && $rcode != NULL && $source_id != NULL) {
            $i_destination[0] = 'เลือกต้นทาง';
            foreach ($this->get_stations($rcode, $vtid)as $s) {
                $i_destination[$s['SID']] = $s['StationName'];
            }
        }
        $dropdown = 'class="selecter_3" data-selecter-options = \'{"cover":"true"}\' ';

        $form_search_route = array(
            'form' => form_open('sale/', array('id' => 'form_search_route', 'class' => 'form')),
            'RCode' => form_dropdown('RCode', $i_RCode, (set_value('RCode') == NULL) ? $rcode : set_value('RCode'), $dropdown),
            'SourceID' => form_dropdown('SourceID', $i_source, (set_value('SourceID') == NULL) ? $source_id : set_value('SourceID'), $dropdown),
//            'DestinationID' => form_dropdown('DestinationID', $i_destination, (set_value('DestinationID') == NULL) ? $destination_id : set_value('DestinationID'), $dropdown),
        );
        return $form_search_route;
    }

}