<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_station extends CI_Model {

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
        $this->db->order_by('Seq','asc');
        $query = $this->db->get('t_stations');

        $rs = $query->result_array();

        return $rs;
    }

    public function get_station_sale_ticket($rcode = null, $vtid = null, $sid = NULL) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($sid != NULL) {
            $this->db->where('SID', $sid);
        }

        $this->db->where('IsSaleTicket', 1);

        $this->db->order_by('Seq', 'asc');
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

    public function get_stations_by_start_point($start_point, $rcode = null, $vtid = null) {
        if ($rcode != NULL) {
            $this->db->where('RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('VTID', $vtid);
        }
        if ($start_point == 'S') {
            $this->db->order_by('Seq', 'asc');
        }
        if ($start_point == 'D') {
            $this->db->order_by('Seq', 'desc');
        }
        
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
