<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_login extends CI_Model {

    function set_form() {
        $i_user = array(
            'name' => 'user',
            'value' => set_value('user'),
            'placeholder' => 'Username',
            'autofocus' => true,
            'class' => 'form-control');
        $i_pass = array(
            'name' => 'pass',
            'value' => set_value('pass'),
            'placeholder' => 'Password',
            'class' => 'form-control');

        $data = array(
            'user' => form_input($i_user),
            'pass' => form_password($i_pass)
        );
        return $data;
    }

    function set_validation() {
        $this->form_validation->set_rules('user', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('pass', 'Password', 'trim|required|xss_clean');
        return TRUE;
    }

    function get_post() {
        $get_page_data = array(
            'user' => $this->input->post('user'),
            'pass' => $this->input->post('pass')
        );
        return $get_page_data;
    }

    function login($data) {
        //Intial data
        $flag = FALSE;
        $session = array(
            'name' => 'Admin',
            'login' => FALSE
        );

        if ($data['user'] == 'admin' && $data['pass'] == 'admin') {
            $session['login'] = TRUE;
            $flag = TRUE;
            $this->session->set_userdata($session);
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
