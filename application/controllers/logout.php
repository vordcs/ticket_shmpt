<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class logout extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_log_clocking');
    }

    public function index() {
        $eid = $this->session->userdata('EID');
        if (count($this->m_log_clocking->check_log_clocking_today($eid)) == 0) {
            //Error logout but no log_clocking
            if ($this->m_log_clocking->insert_clock_out($eid) == FALSE) {
                redirect('logout');
            }
        } else {
            //if error update log_clocking will loop again
            if ($this->m_log_clocking->update_clock_out($eid) == FALSE) {
                redirect('logout');
            }
        }

        $this->session->sess_destroy();
        redirect('login');
    }

}
