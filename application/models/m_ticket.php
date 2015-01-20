<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_ticket extends CI_Model {

    public function get_ticket($date, $tsid = null, $status_seat = NULL, $eid = NULL) {
//        $this->check_ticket();
        $this->db->where('DateSale', $date);
        if ($tsid != NULL) {
            $this->db->where('TSID', $tsid);
        }
        if ($status_seat != NULL) {
            $this->db->where('StatusSeat', $status_seat);
        }
        if ($eid != NULL) {
//            $eid = $this->m_user->get_user_id();
            $this->db->where('Seller', $eid);
        }
        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_ticket_by_station($sid, $tsid = NULL) {
        $this->check_ticket();

        $this->db->where('SourceID', $sid);
        if ($tsid != NULL) {
            $this->db->where('TSID', $tsid);
        }


        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_ticket_by_saller($tsid) {
        $this->check_ticket();

        $eid = $this->session->userdata('EID');

        $this->db->where('TSID', $tsid);
        $this->db->where('Seller', $eid);

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function sum_ticket_price($date, $sid = NULL, $tsid = NULL) {

        $this->db->select('*');
        if ($tsid != NULL) {
            $this->db->where('TSID', $tsid);
        }
        if ($sid != NULL) {
            $this->db->where('SourceID', $sid);
        }
        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        $this->db->where('DateSale', $date);
        $this->db->where('Seller', $this->m_user->get_user_id());

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function generate_ticket_id($tsid, $source_id, $destination_id, $vcode, $seat) {
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

    /*
     * อัปเดทข้อมูลตั๋วโดยสาร สถานะที่กำลังจอง ให้เป็น ขายเเล้ว
     */

    public function sale_ticket($ticket_id) {
        $data = array(
            'StatusSeat' => 1,
        );
        $this->db->where('TicketID', $ticket_id);
        $this->db->update('ticket_sale', $data);
        if ($this->db->affected_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * ตรวจสอบสถานะการจอง เมื่อเกิน 2 นาทีจะถูกลบ
     */

    public function check_ticket($tsid = NULL) {
        $today = $this->m_datetime->getDateToday();
        $tickets_reseve = $this->get_ticket($today, $tsid, 2);
        foreach ($tickets_reseve as $ticket) {
            $tsid = $ticket['TSID'];
            $seat = $ticket['Seat'];
            $now = $this->m_datetime->getDateTimeNow();
            $date_time_sale = $ticket['CreateDate'];
            $diff = strtotime($now) - strtotime($date_time_sale);
            $minutes = floor($diff / (60));
            if ((int) $minutes > 2) {
                $this->delete_ticket($tsid, $seat);
            }
        }
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
