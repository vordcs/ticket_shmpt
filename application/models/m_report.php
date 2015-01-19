
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_report extends CI_Model {

    public function get_report($RCode = NULL, $VTID = NULL, $SID = NULL) {

        if ($RCode != NULL) {
            $this->db->where('report_day.RCode');
        }
        if ($VTID != NULL) {
            $this->db->where('report_day.VTID');
        }
        if ($SID != NULL) {
            $this->db->where('report_day.SID');
        }
        $query = $this->db->get('report_day');

        return $query->result_array();
    }

    public function get_post_form_send() {
        $RCode = $this->input->post('RCode');
        $VTID = $this->input->post('VTID');
        $SID = $this->input->post('SID');
        $Total = $this->input->post('Total');
        $Vage = $this->input->post('Vage');
        $Net = $this->input->post('Net');

        $ReportID = $this->gennerate_report_id($RCode, $VTID, $SID);
    }

    public function gennerate_report_id($RCode, $VTID, $SID) {

        $Report = $this->gennerate_report_id($RCode, $VTID, $SID);
        $ReportID = '';
        if (count($Report) <= 0) {
//        วันที่
            $date = new DateTime();
            $ReportID .=$date->format("Ymd");
            //สถานี
            
        }
    }

}
