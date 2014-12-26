<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class m_template extends CI_Model {

    private $title = 'บริษัท สหมิตรภาพ(2512) จำกัด';
    private $view_name = NULL;
    private $set_data = NULL;
    private $permission = "ALL";
    private $debud_data = NULL;
    private $lang_value = array('theme');
    private $version = '1.0';
    
    function set_Debug($data) {
        $this->debud_data = $data;
    }

    function set_Title($name) {
        $this->title = $name . ' | ' . $this->title;
    }

    function set_Content($name, $data) {
        $this->view_name = $name;
        $this->set_data = $data;
    }

    function set_Permission($mode) {
        $this->permission = $mode;
    }
    
    function check_Alert(){
        return $this->session->flashdata('alert');
    }

    function check_permission() {
        $sess = $this->session->userdata('login');
        if ($sess == NULL || $sess== FALSE) {
            redirect('login');
        }
        return TRUE;
    }

    function set_Language($in) {
        foreach ($in as $data) {
            array_push($this->lang_value, $data);
        }
    }

    function showTemplate() {
        //--- Load language --//
        $site_lang = $this->session->userdata('site_lang');
        if (!$site_lang) {
            $site_lang = 'thai'; //Default set language to Thai
        }
        foreach ($this->lang_value as $path) {
            $this->lang->load($path, $site_lang); //Load message
        }

        //Check login
        $this->check_permission();

        //Load version for Cache CSS and JS
        $data['version'] = $this->version;

        //--- Redirect to current page ---//
        $data['page'] = $this->uri->segment(1);

        //--- Alert System ---//
        $data['alert'] = $this->session->userdata('alert');
        $this->session->unset_userdata('alert');

        $user = $this->session->userdata('user');
        $data['u_name'] = $user['u_name'];
        $data['form_login'] = form_open('logout', array('class' => 'navbar-form pull-right', 'style' => 'height: 40px;'));

        $data['title'] = $this->title;
        $data['debug'] = $this->debud_data;
        $data['alert'] = $this->check_Alert();

        $this->load->view('theme_header', $data);
        if ($this->view_name != NULL) {
            $this->load->view($this->view_name, $this->set_data);
        }
        $this->load->view('theme_footer');
    }

    function showSaleTemplate() {
        //--- Load language --//
        $site_lang = $this->session->userdata('site_lang');
        if (!$site_lang) {
            $site_lang = 'thai'; //Default set language to Thai
        }
        foreach ($this->lang_value as $path) {
            $this->lang->load($path, $site_lang); //Load message
        }

        //Check login
        $this->check_permission();

        //Load version for Cache CSS and JS
        $data['version'] = $this->version;

        //--- Redirect to current page ---//
        $data['page'] = $this->uri->segment(1);

        //--- Alert System ---//
        $data['alert'] = $this->session->userdata('alert');
        $this->session->unset_userdata('alert');

        $user = $this->session->userdata('user');
        $data['u_name'] = $user['u_name'];
        $data['form_login'] = form_open('logout', array('class' => 'navbar-form pull-right', 'style' => 'height: 40px;'));

        $data['title'] = $this->title;
        $data['debug'] = $this->debud_data;
        $data['alert'] = $this->check_Alert();

        $this->load->view('theme_header_sale', $data);
        if ($this->view_name != NULL) {
            $this->load->view($this->view_name, $this->set_data);
        }
        $this->load->view('theme_footer_sale');
    }
}
