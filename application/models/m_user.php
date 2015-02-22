<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_user extends CI_Model {

    public function get_user_id() {
        $eid = $this->session->userdata('EID');
        return $eid;
    }

    public function get_user_first_name() {
        $eid = $this->session->userdata('EID');
        $this->db->where('EID', $eid);
        $query = $this->db->get_where('employees');
        $rs = $query->result_array();
        return $rs[0]['FirstName'];
    }

    public function get_user_full_name() {
        $eid = $this->session->userdata('EID');
        $this->db->where('EID', $eid);
        $query = $this->db->get_where('employees');
        $rs = $query->result_array();


        $full_name = $rs[0]['FirstName'].'  '.$rs[0]['LastName'];

        return $full_name;
    }

    public function get_saller_station($rcode = NULL, $vtid = NULL, $rid = NULL) {
//        $this->db->select('*');
        $this->db->join('t_routes', 'sellers.RCode = t_routes.RCode AND sellers.VTID = t_routes.VTID');
        $this->db->join('vehicles_type', 'vehicles_type.VTID = t_routes.VTID');
        $this->db->join('t_stations', 'sellers.SID =  t_stations.SID');

        $this->db->where('sellers.EID', $this->m_user->get_user_id());

        if ($rcode != NULL) {
            $this->db->where('sellers.RCode', $rcode);
        }
        if ($vtid != NULL) {
            $this->db->where('sellers.VTID', $vtid);
        }
        if ($rid != NULL) {
            $this->db->where('t_routes.RID', $rid);
        } else {
            $this->db->group_by(array('t_routes.RCode', 't_routes.VTID'));
        }

        $query = $this->db->get('sellers');

        return $query->result_array();
    }

}
