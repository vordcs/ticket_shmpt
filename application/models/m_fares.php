<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_fares extends CI_Model {

// get  ข้อมูลค่าโดยสาร
    public function get_fares($rcode, $vtid, $source_id = NULL, $destination_id = NULL) {

        $this->db->join('f_fares_has_rate', 'f_fares_has_rate.FID = f_fares.FID');
        $this->db->join('f_rate', 'f_rate.RateID = f_fares_has_rate.RateID');

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

}
