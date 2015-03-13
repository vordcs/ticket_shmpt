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
        $this->db->order_by('Seat');
        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_ticket_report_by_seller($TSID, $SourceID) {
        $this->db->select('SourceID,SourceName,DestinationID,DestinationName,PriceSeat,COUNT(TicketID) as NumberTicket,SUM(PriceSeat) as Total');
        $this->db->where('SourceID', $SourceID);
        $this->db->where('TSID', $TSID);
        $this->db->where('Seller', $this->m_user->get_user_id());
        $this->db->group_by('SourceID,DestinationID');
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

    public function get_ticket_for_booking($tsid = NULL) {
        $this->check_ticket();
        $eid = $this->session->userdata('EID');

        $this->db->where('TSID', $tsid);

        $this->db->where('Seller', $eid);

        $query = $this->db->get('ticket_sale');

        return $query->result_array();
    }

    public function get_ticket_by_seller($date = NULL, $tsid = NULL, $SourceID = NULL, $StatusSeat = NULL) {
        $this->check_ticket();
        $eid = $this->session->userdata('EID');

        $this->db->select('ticket_sale.TSID as TSID,SourceID,SourceName,DestinationID,DestinationName,PriceSeat,StatusSeat, COUNT(TicketID) as num_ticket,SUM(PriceSeat) as total_price_ticket,DateSale');

        if ($date == NULL) {
            $date = $this->m_datetime->getDateToday();
        }
        if ($tsid != NULL) {
            $this->db->where('TSID', $tsid);
        }
        if ($SourceID != NULL) {
            $this->db->where('SourceID', $SourceID);
        }
        if ($StatusSeat != NULL) {
            $this->db->where('StatusSeat', $StatusSeat);
        }
        $this->db->where('DateSale', $date);
        $this->db->where('Seller', $eid);

        $query = $this->db->get('ticket_sale');

        if ($tsid != NULL) {
            
        } else {
            
        }

        return $query->result_array();
    }

    public function sum_ticket_price($date = NULL, $sid = NULL, $tsid = NULL) {

        $this->db->select('SUM(PriceSeat) as Total');
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

        return $query->row_array();
    }

    public function generate_ticket_id($tsid, $source_id, $destination_id, $vcode, $seat) {
        $str_vcode = explode('-', $vcode);
        $vcode = $str_vcode[0] . $str_vcode[1];
        $ticket_id = '';
        $ticket_id .= $tsid;
        $ticket_id .= str_pad($source_id, 3, '0', STR_PAD_LEFT);
        $ticket_id .= str_pad($destination_id, 3, '0', STR_PAD_LEFT);
        $ticket_id .= $vcode;
        $ticket_id .=str_pad($seat, 2, '0', STR_PAD_LEFT);

        return $ticket_id;
    }

    public function get_TicketID($tsid, $seat, $SourceID = NULL, $Seller = NULL) {
        $ticket_id = NULL;
        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);

        if ($SourceID != NULL) {
            $this->db->where('SourceID', $SourceID);
        }

        if ($Seller != NULL) {
            $this->db->where('Seller', $Seller);
        }

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
        $TSID = $data['TSID'];
        $SourceID = $data['SourceID'];
        $Seat = $data['Seat'];
        $Seller = $data['Seller'];
        if ($this->get_TicketID($TSID, $Seat, $SourceID, $Seller) == NULL) {
            $TicketID = $this->insert_ticket($data, 2);
        } else {
            $TicketID = NULL;
        }
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

    public function delete_ticket($tsid, $seat, $SourceID = NULL) {
        $EID = $this->session->userdata('EID');

        $ticket_id = $this->get_TicketID($tsid, $seat, $SourceID, $EID);
        if ($ticket_id == NULL) {
            return FALSE;
        }

        $this->db->where('TSID', $tsid);
        $this->db->where('Seat', $seat);
        if ($SourceID != NULL) {
            $this->db->where('SourceID', $SourceID);
        }
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

    public function check_ticket($TSID = NULL) {
        $today = $this->m_datetime->getDateToday();
        $tickets_reseve = $this->get_ticket($today, $TSID, 2);
        foreach ($tickets_reseve as $ticket) {
            $ticket_id = $ticket['TicketID'];
            $now = $this->m_datetime->getDateTimeNow();
            $date_time_sale = $ticket['CreateDate'];
            $diff = strtotime($now) - strtotime($date_time_sale);
            $minutes = floor($diff / (60));
            if ((int) $minutes > 2) {
                $this->db->where('TicketID', $ticket_id);
                $this->db->delete('ticket_sale');
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
