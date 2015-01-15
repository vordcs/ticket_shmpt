<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_user extends CI_Model {

    public function get_user_id() {
        $eid = $this->session->userdata('EID');
        return $eid;
    }
    
    public function get_saller_info() {
        
    }
    
    

}
