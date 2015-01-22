<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_report extends CI_Model {

    public function get_cost($cid = null, $ctid = NULL, $date = NULL, $tsid = NULL, $vid = NULL) {
        $this->db->select('*,cost.CreateBy AS CreateBy,cost.CreateDate as CreateDate');
        $this->db->join('cost_type', 'cost_type.CostTypeID = cost.CostTypeID');
        $this->db->join('cost_detail', 'cost_detail.CostDetailID = cost.CostDetailID', 'left');
        $this->db->join('vehicles_has_cost', 'vehicles_has_cost.CostID = cost.CostID', 'left');
        $this->db->join('vehicles', 'vehicles.VID = vehicles_has_cost.VID', 'left');
        $this->db->join('t_schedules_day_has_cost', 't_schedules_day_has_cost.CostID = cost.CostID');
        $this->db->join('t_stations', 't_stations.SID = cost.SID', 'left');
        if ($cid != NULL) {
            $this->db->where('cost.CostID', $cid);
        }
        if ($ctid != NULL) {
            $this->db->where('cost.CostTypeID', $ctid);
        }
        if ($tsid != NULL) {
            $this->db->where('t_schedules_day_has_cost.TSID', $tsid);
        }
        if ($vid != NULL) {
            $this->db->where('vehicles_has_cost.VID', $vid);
        }
        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }

        $this->db->where('cost.CostDate', $date);
        $this->db->where('cost.CreateBy', $this->m_user->get_user_id());
        $query = $this->db->get('cost');
        return $query->result_array();
    }

    public function get_cost_type($id = NULL) {

        if ($id != NULL) {
            $this->db->where('CostTypeID', $id);
        }
        $query = $this->db->get('cost_type');
        return $query->result_array();
    }

    public function get_report($date = NULL, $RCode = NULL, $VTID = NULL, $SID = NULL, $ReportID = NULL) {

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        if ($RCode != NULL) {
            $this->db->where('RCode', $RCode);
        }
        if ($VTID != NULL) {
            $this->db->where('VTID', $VTID);
        }
        if ($SID != NULL) {
            $this->db->where('SID', $SID);
        }
        if ($ReportID != NULL) {
            $this->db->where('ReportID', $ReportID);
        }

        $this->db->where('ReportDate', $date);

        $query = $this->db->get('report_day');

        return $query->result_array();
    }

    public function insert_report($data) {
        $rs = array();
        $ReportID = $data['report']['ReportID'];
        $num_report = count($this->get_report(NULL, NULL, NULL, $ReportID));
        if ($num_report <= 0) {


            //insert report day 
            $this->db->insert('report_day', $data['report']);
            //insert t_schedules_day_has_report
            $i = 0;
            foreach ($data['schedules_day_has_report'] as $s_r) {
                $this->db->insert('t_schedules_day_has_report', $s_r);
                $rs[$i] = $s_r;
                $i++;
            }
        } else {
            $rs = 'UPDATE';
        }
        return $rs;
    }

    public function set_form_add($RCode, $VTID, $SID) {
        
    }

    public function get_post_form_send() {
        $RCode = $this->input->post('RCode');
        $VTID = $this->input->post('VTID');
        $SID = $this->input->post('SID');
        $Total = $this->input->post('Total');
        $Vage = $this->input->post('Vage');
        $Net = (int) $Total - (int) $Vage;
        $ReportNote = $this->input->post('ReportNote');

        $TSID = $this->input->post('TSID');

        $ReportID = $this->gennerate_report_id($RCode, $VTID, $SID);
        if (!empty($TSID) && $ReportID != FALSE) {
            $report = array(
                'ReportID' => $ReportID,
                'ReportDate' => $this->m_datetime->getDateToday(),
                'ReportTime' => $this->m_datetime->getTimeNow(),
                'Total' => $Total,
                'Vage' => $Vage,
                'Net' => $Net,
                'ReportStatus' => 0,
                'ReportNote' => $ReportNote,
                'SID' => $SID,
                'RCode' => $RCode,
                'VTID' => $VTID,
                'CreateBy' => $this->m_user->get_user_id(),
                'CreateDate' => $this->m_datetime->getDateTimeNow(),
            );

            $schedules_day_has_report = array();

            foreach ($TSID as $id) {
                $temp_schedules_day_has_report = array(
                    'TSID' => $id,
                    'ReportID' => $ReportID,
                );
                array_push($schedules_day_has_report, $temp_schedules_day_has_report);
            }

            $form_data = array(
                'report' => $report,
                'schedules_day_has_report' => $schedules_day_has_report,
            );

            return $form_data;
        } else {
            return FALSE;
        }

        return FALSE;
    }

    public function validation_form_add() {
        $this->form_validation->set_rules("Total", "รวม", 'trim|xss_clean|required');
        $this->form_validation->set_rules("Vage", "ค่าตอบแทน", 'trim|xss_clean|required');
        $this->form_validation->set_rules("Net", "คงเหลือ", 'trim|xss_clean|required');
        $this->form_validation->set_rules("ReportNote", "", 'trim|xss_clean');

        return TRUE;
    }

    public function gennerate_report_id($RCode, $VTID, $SID) {
        $date = $this->m_datetime->getDateToday();
        $Report = $this->get_report($date, $RCode, $VTID, $SID);
        $num_report = count($Report);
        $ReportID = '';
        if ($num_report >= 0) {
            //วันที่
            $date = new DateTime();
            $ReportID .=$date->format("Ymd");
            //รหัสเส้นทาง
            $ReportID .=str_pad($RCode, 3, '0', STR_PAD_LEFT);
            //รหัสประเภทรถ
            $ReportID .=$VTID;
            //สถานี
            $ReportID .= str_pad($SID, 2, '0', STR_PAD_LEFT);
            //run number
            $ReportID .=str_pad($num_report, 1, '0', STR_PAD_LEFT);

            return $ReportID;
        }
        return FALSE;
    }

}
