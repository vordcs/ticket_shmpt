<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of m_barcode
 *
 * @author VoRDcs
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class m_barcode extends CI_Model {

    function gen_barcode($code = NULL) {
        $eid = '';
//        return path to file bar code
        if ($code != NULL) {
            $eid = $this->session->userdata('EID');
            $folder_barcode = FCPATH . "assets/barcode/$eid";
            if (!is_dir($folder_barcode)) {
                mkdir($folder_barcode, 0777, TRUE);
            }

            $this->load->helper("file");
            delete_files($folder_barcode);

            $this->load->library('zend');
            $this->zend->load('Zend/Barcode');
            $imageResource = Zend_Barcode::draw('code128', 'image', array('text' => $code), array());

            imagejpeg($imageResource, $eid . "_barcode.jpg", 100);

            $file = $eid . "_barcode.jpg";
            $file_new = $code . '.jpg';

            $oldDir = FCPATH;
            $newDir = FCPATH . "assets/barcode/$eid/";

            rename($oldDir . $file, $newDir . $file_new);
            imagedestroy($imageResource);

            $file_path = base_url() . "assets/barcode/$eid/$file_new";

            return $file_path;
        }
    }

}
