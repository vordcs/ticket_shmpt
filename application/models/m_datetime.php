<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class m_datetime extends CI_Model {

    private $month_th = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");

    function getDatetimeNow() {
        return date('Y-m-d H:i:s');
    }

    function getDateToday() {
        return date('Y-m-d');
    }
    function getDatetimeNowTH() {       
        $hour = date("H");
        $minute = date("i");
        $seconds = date("s");
        $day = date('d');
        $month = date('m');
        $year = date('Y') + 543;
        $today = $year . '-' . $month . '-' . $day . ' ' . $hour.':'.$minute.':'.$seconds;
        $dt = new DateTime($today);
        $datetime = $dt->format('Y-m-d H:i:s');
        return $datetime;
    }

    function getDateTodayTH() {

        $day = date('d');
        $month = date('m');
        $year = date('Y') + 543;
        $today = $year . '-' . $month . '-' . $day;
        $d = new DateTime($today);
        $date = $d->format('Y-m-d');
        return $date;
    }

    public function getDateThaiString($strDate) {
//        string input 2557-11-15
        if ($strDate == NULL) {
            return '-';
        } else {
            $str = explode('-', $strDate);
            $strYear = trim($str[0]);
            $strMonthThai = $this->month_th[(int) $str[1]];
            $strDay = $str[2];
            return "$strDay $strMonthThai $strYear";
        }
    }

    function setDateFomat($input_date) {
        $date = NULL;
        $d = new DateTime($input_date);
        if ($d->format('Y') > date('Y')) {
            $date .= ($d->format('Y') - 543) . '-';
            $date .= ($d->format('m')) . '-';
            $date .= $d->format('d');
        } else {
            $date = $d->format('Y-m-d');
        }
        return $date;
    }

    function setYearFomat($input_year) {
        $y = new DateTime($input_year);
        $year = $y->format('yyyy');
        return $year;
    }

    public function monthTHtoDB($str_date_th) {
        for ($i = 0; $i < count($this->month_th); $i++) {
            if ($this->month_th[$i] == $str_date_th) {
                return $i;
            }
        }
    }

    public function getMonthThai($i) {
        return $this->month_th[$i];
    }

    public function DateThai($strDate) {
        if ($strDate == NULL) {
            return '-';
        } else {
            $str = explode('-', $strDate);
            $strYear = trim($str[0]) + 543;
            $strMonthThai = $this->month_th[(int) $str[1]];
            $strDay = $str[2];
            return "$strDay $strMonthThai $strYear";
        }
    }

    public function DateThaiToDay() {
        $strDate = $this->getDateToday();
        if ($strDate == NULL) {
            return '-';
        } else {
            $str = explode('-', $strDate);
            $strYear = trim($str[0]) + 543;
            $strMonthThai = $this->month_th[(int) $str[1]];
            $strDay = $str[2];
            return "$strDay $strMonthThai $strYear";
        }
    }

    public function DateTimeThai($strDate) {
        if ($strDate == NULL) {
            return '-';
        } else {
            $date = new DateTime($strDate);
            $strYear = date("Y", strtotime($strDate)) + 543;
            $strMonth = date("n", strtotime($strDate));
            $strDay = date("j", strtotime($strDate));
            $strHour = date("H", strtotime($strDate));
            $strMinute = date("i", strtotime($strDate));
            $strSeconds = date("s", strtotime($strDate));
            $strMonthCut = $this->month_th;
            $strMonthThai = $strMonthCut[$strMonth];
            return "$strDay $strMonthThai $strYear " . " เวลา $strHour:$strMinute ";
        }
    }

}
