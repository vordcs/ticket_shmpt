<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_ticket extends CI_Model {

    public function get_ticket($tsid, $status_seat = NULL, $eid = NULL) {
        $this->db->where('TSID', $tsid);
        if ($status_seat != NULL) {
            $this->db->where('StatusSeat', $status_seat);
        }
        if ($eid != NULL) {
            $this->db->where('Seller', $eid);
        }

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_ticket_by_saller($tsid) {
        $eid = $this->session->userdata('EID');

        $this->db->where('TSID', $tsid);
        $this->db->where('Seller', $eid);

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function generate_ticket_id($tsid, $source_id, $destination_id, $vcode, $seat) {
        $EID = $this->session->userdata('EID');
        $str_vcode = explode('-', $vcode);
        $vcode = $str_vcode[0] . $str_vcode[1];
        $ticket_id = '';
        $ticket_id .= $tsid;
//        $ticket_id .= $EID;
        $ticket_id .= str_pad($source_id, 3, '0', STR_PAD_LEFT);
        $ticket_id .= str_pad($destination_id, 3, '0', STR_PAD_LEFT);
        $ticket_id .= $vcode;
        $ticket_id .=str_pad($seat, 2, '0', STR_PAD_LEFT);

        return $ticket_id;
    }

    public function get_TicketID($tsid, $seat) {
        $ticket_id = NULL;
        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);
        $query = $this->db->get('ticket_sale');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $ticket_id = $row->TicketID;
        }
        return $ticket_id;
    }

    /*
     * SeatStatus -> 
     * 0=ว่าง ,
     * 1=ไม่ว่าง(ขายเเล้ว), 
     * 2=กำลังจอง(กำลังจะขาย)
     */

    public function resever_ticket($data) {
        $TicketID = $this->insert_ticket($data, 2);
        return $TicketID;
    }

    public function update_resever_ticket($data) {
        $TicketID = array();
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $TicketID[$i] = $this->update_ticket($data[$i], 2);
            }
        }
        return $TicketID;
    }

    public function insert_ticket($data, $status_seat) {
        $EID = $this->session->userdata('EID');
        $rs = '';
        $tsid = $data['TSID'];
        $vcode = $data['VCode'];
        $source_id = $data['SourceID'];
        $destination_id = $data['DestinationID'];
        $seat = $data['Seat'];

//        ตรวจสอบก่อนว่าที่นั่งที่เลือกว่างหรือไม่ หรือมีอยู่ในดาต้าเบสหรือยัง        

        if ($this->check_seat_status($tsid, $seat, 0)) {
//            นั่งว่าง
            $ticket_id = $this->generate_ticket_id($tsid, $source_id, $destination_id, $vcode, $seat);
            $data['TicketID'] = $ticket_id;
            $data['StatusSeat'] = $status_seat;
            $data['Seller'] = $EID;
            $data['CreateBy'] = $EID;
            $data['CreateDate'] = $this->m_datetime->getDatetimeNow();
//            insert ticket
            $this->db->insert('ticket_sale', $data);

            $rs = $ticket_id;
        } elseif ($this->check_seat_status($tsid, $seat, 2, $EID) == FALSE) {
//            ที่นั่งกำลังจอง
            $ticket_id = $this->get_TicketID($tsid, $seat);
//            $rs = "กำลังดำเนินการ โดย $EID เลขที่ตั๋ว -> $ticket_id";
            $rs = $ticket_id;
        } else {
            $rs = "";
        }
        return $rs;
    }

    public function update_ticket($data, $status_seat = NULL) {
        $EID = $this->session->userdata('EID');
        $tsid = $data['TSID'];
        $seat = $data['Seat'];

        $data['UpdateBy'] = $EID;
        $data['UpdateDate'] = $this->m_datetime->getDatetimeNow();

        if ($status_seat != NULL) {
            $this->db->where('StatusSeat', $status_seat);
        }

        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);
        $this->db->where('Seller', $EID);
        $this->db->update('ticket_sale', $data);


        return $this->get_TicketID($tsid, $seat);
    }

    public function delete_ticket($tsid, $seat) {
        $EID = $this->session->userdata('EID');
        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);
        $this->db->where('Seller', $EID);
        $this->db->delete('ticket_sale');

        return TRUE;
    }

//    ตรวจสอบว่าที่นั่งสามารถจองหรือนั่งได้หรือไม่
    public function check_seat_status($tsid, $seat, $status_seat, $saller = NULL) {
        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);
        $this->db->where('StatusSeat', $status_seat);
        if ($saller != NULL) {
            $this->db->where('Seller', $saller);
        }
        $query = $this->db->get('ticket_sale');

        if ($query->num_rows() >= 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
