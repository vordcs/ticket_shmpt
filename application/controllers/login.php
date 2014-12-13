<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_login');
        $this->load->library('form_validation');
        $this->load->helper('form');

        //Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {

        if ($this->m_login->set_validation() && $this->form_validation->run()) {
            $temp = $this->m_login->get_post();
            if($this->m_login->login($temp)){
                redirect('home');
            }
        }
        $data['form_action'] = form_open('login', array('class' => 'form-signin'));
        $data['form_input'] = $this->m_login->set_form();

        $this->load->view('login', $data);
    }

}
