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
            'user_name' => 'Admin',
            'EID' => "E000000000",
            'login' => FALSE
        );

        if ($data['user'] == 'admin' && $data['pass'] == 'admin') {
            $session['login'] = TRUE;
            $flag = TRUE;
            $this->session->set_userdata($session);
            return TRUE;
        } else {
            $temp = $this->check_sellers($data['user'], $data['pass']);
            if ($temp != NULL) {
                if($temp[0]['StatusID'] == '0'){
                    return FALSE;
                }
                $session['user_name'] = $temp[0]['Title'] . $temp[0]['FirstName'] . ' ' . $temp[0]['LastName'];
                $session['EID'] = $temp[0]['EID'];
                $session['login'] = TRUE;
                $this->session->set_userdata($session);
                return TRUE;
            }
            return FALSE;
        }
    }

    function check_sellers($user, $pass) {
        $this->db->from('username AS una');
        $this->db->join('employees AS em', 'em.EID = una.UserName');
        $this->db->where('em.PID', '4'); // 4 = พนักงานขายตั๋ว(นายท่า)
        $this->db->where('una.UserName', $user);
        $this->db->where('una.Password', md5($pass));
        $this->db->where('una.IsNormal', '1');
        $query = $this->db->get();
        return $query->result_array();
    }

}
