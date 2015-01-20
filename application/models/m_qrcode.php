<?php

Class m_qrcode extends CI_Model {

    function gen_qrcode($code = NULL) {
        $eid = '';
//        return path to file qr code
        if ($code != NULL) {
            $eid = $this->session->userdata('EID');
            $folder_qrcode = FCPATH . "assets/qrcode/$eid";
            if (!is_dir($folder_qrcode)) {
                mkdir($folder_qrcode, 0777, TRUE);
            }
            
//            $this->load->helper("file");
//            delete_files($folder_qrcode);
            
            $this->load->library('ciqrcode');

            $params['data'] = "$code";
            $params['level'] = 'H';
            $params['size'] = 5;
            $params['savename'] = FCPATH . $eid . "_qrcode.png";
            $this->ciqrcode->generate($params);

            $file = $eid . "_qrcode.png";
            $file_new = $code . '.png';

            $oldDir = FCPATH;
            $newDir = FCPATH . "assets/qrcode/$eid/";

            rename($oldDir . $file, $newDir . $file_new);


            $file_path = base_url() . "assets/qrcode/$eid/$file_new";

            return $file_path;
        }
        return FALSE;
    }

}
