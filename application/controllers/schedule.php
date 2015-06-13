<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class schedule extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('m_template');
        $this->load->model('m_route');
        $this->load->model('m_station');
        $this->load->model('m_schedule');
        $this->load->model('m_ticket');
        //from hr
        $this->load->model('m_route_hr');
        $this->load->model('m_station_hr');
        $this->load->model('m_vehicle_hr');
        $this->load->model('m_schedule_hr');
        $this->load->library('form_validation');

        //Initial language
        $this->m_template->set_Language(array('plan'));
    }

    public function index() {

        $date = $this->m_datetime->getDateToday();
        $date_th = $this->m_datetime->DateThaiToDay();
        $schedules = $this->m_schedule->get_schedule($date);

        if (count($schedules) <= 0) {
            $alert['alert_message'] = "ไม่พบข้มูลรอบเวลา วันที่ $date_th";
            $alert['alert_mode'] = "warning";
            $this->session->set_flashdata('alert', $alert);

            redirect('home/');
        }

        $form = $this->m_schedule->set_form_view();

        $data = array(
            'page_title' => 'ตารางเวลาเดินรถ : ',
            'page_title_small' => '',
            'routes_seller' => $form['routes_seller'],            
            'data' => $form['data'],
        );
        $data['stations'] = $this->m_station->get_stations();
        $data['schedules'] = $schedules;
        $data_debug = array(
//            'from_search' => $data['from_search'],
//            'routes' => $data['routes'],
//            'stations' => $data['stations'],
//            'schedules' => $data['schedules'],
//            'routes_seller' => $data['routes_seller'],
//            'data' => $data['data'],
        );
        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title('ตารางเวลาเดินรถ');
        $this->m_template->set_Content('schedule/schedule', $data);
        $this->m_template->showTemplate();
    }

    public function view($rcode, $vtid) {

        $route_detail = $this->m_route_hr->get_route($rcode, $vtid);
        $vt_name = $route_detail[0]['VTDescription'];
        $source = $route_detail[0]['RSource'];
        $desination = $route_detail[0]['RDestination'];

        $route_name = $vt_name . ' เส้นทาง ' . $route_detail[0]['RCode'] . ' ' . ' ' . $source . ' - ' . $desination;


        $data = array(
//            'form_search' => $this->m_schedule->set_form_search(),
            'page_title' => 'ตารางเวลาเดิน ' . $route_name,
            'page_title_small' => '',
            'route_detail' => $this->m_route_hr->get_route_detail($rcode, $vtid),
        );

        // ตารางรถที่จะนำไปแสดงใน view และจะเอาไปใช้ตรวจสอบการ POST ว่ามีอะไรเปลี่ยน
        $data['schedules'] = $this->m_schedule_hr->get_schedule($this->m_datetime->getDateToday(), $rcode, $vtid);

        /*
         * ตรวจสอบการเปลี่ยนแปลงจากค่า POST และนำมาตรวจสอบการเปลี่ยนแปลง
         */
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $post = $this->input->post();
            $DIS_TSID_S = $post['DIS_TSID_S'];
            $TSID_S = $post['TSID_S'];
            $DIS_VID_S = $post['DIS_VID_S'];
            $VID_S = $post['VID_S'];
            $DIS_TSID_D = $post['DIS_TSID_D'];
            $TSID_D = $post['TSID_D'];
            $DIS_VID_D = $post['DIS_VID_D'];
            $VID_D = $post['VID_D'];
            $REMOVE_TSID_S = isset($post['REMOVE_TSID_S']) ? $post['REMOVE_TSID_S'] : NULL;
            $REMOVE_TSID_D = isset($post['REMOVE_TSID_D']) ? $post['REMOVE_TSID_D'] : NULL;
            $temp_route_detail = $this->m_route_hr->get_route_detail($rcode, $vtid);

            /*
             * เรียงข้อมูลใหม่ของ S เตรียมไว้อัพเดท
             */
            $new_vehicles_has_schedules_S = array();
            $vehicles_update_S = array();
            $ROUTE_DETAIL_S = $temp_route_detail[0];
            $RID_S = $ROUTE_DETAIL_S['RID'];

            for ($i = 0; $i < count($VID_S); $i++) {
                if (isset($TSID_S[$i])) {
                    $new_vehicles_has_schedules_S[$i]['TSID'] = $TSID_S[$i];
                    $new_vehicles_has_schedules_S[$i]['VID'] = $VID_S[$i];
                } else {
                    array_push($vehicles_update_S, $VID_S[$i]);
                }
            }

            /*
             * ตรวจสอบว่ามีการเปลี่ยนแปลงของ ตารางฝั่ง S ไหม
             */

            $Total_schedules_S = array();
            $loop = count($DIS_TSID_S) + count($TSID_S);
            for ($i = 0; $i < $loop; $i++) {
                if ($i < count($DIS_TSID_S)) {
                    $Total_schedules_S[$i]['TSID'] = $DIS_TSID_S[$i];
                    $Total_schedules_S[$i]['VID'] = $DIS_VID_S[$i];
                } else {
                    $Total_schedules_S[$i]['TSID'] = $TSID_S[$i - count($DIS_TSID_S)];
                    $Total_schedules_S[$i]['VID'] = $VID_S[$i - count($DIS_TSID_S)];
                }
            }
            $flag_S_change = FALSE;
            $i = 0;
            foreach ($data['schedules'] as $row) {
                if ($row['StartPoint'] == 'S') {
                    if ($i < count($Total_schedules_S)) {
                        if ($Total_schedules_S[$i]['VID'] != $row['VID']) {
                            $flag_S_change = TRUE;
                        }
                        $i++;
                    } else {
                        $flag_S_change = TRUE;
                    }
                }
            }

            /*
             * เรียงข้อมูลใหม่ของ D เตรียมไว้อัพเดท
             */
            $new_vehicles_has_schedules_D = array();
            $vehicles_update_D = array();
            $ROUTE_DETAIL_D = $temp_route_detail[1];
            $RID_D = $ROUTE_DETAIL_D['RID'];

            for ($i = 0; $i < count($VID_D); $i++) {
                if (isset($TSID_D[$i])) {
                    $new_vehicles_has_schedules_D[$i]['TSID'] = $TSID_D[$i];
                    $new_vehicles_has_schedules_D[$i]['VID'] = $VID_D[$i];
                } else {
                    array_push($vehicles_update_D, $VID_D[$i]);
                }
            }

            /*
             * ตรวจสอบว่ามีการเปลี่ยนแปลงของ ตารางฝั่ง D ไหม
             */
            $Total_schedules_D = array();
            $loop = count($DIS_TSID_D) + count($TSID_D);
            for ($i = 0; $i < $loop; $i++) {
                if ($i < count($DIS_TSID_D)) {
                    $Total_schedules_D[$i]['TSID'] = $DIS_TSID_D[$i];
                    $Total_schedules_D[$i]['VID'] = $DIS_VID_D[$i];
                } else {
                    $Total_schedules_D[$i]['TSID'] = $TSID_D[$i - count($DIS_TSID_D)];
                    $Total_schedules_D[$i]['VID'] = $VID_D[$i - count($DIS_TSID_D)];
                }
            }
            $flag_D_change = FALSE;
            $i = 0;
            foreach ($data['schedules'] as $row) {
                if ($row['StartPoint'] == 'D') {
                    if ($i < count($Total_schedules_D)) {
                        if ($Total_schedules_D[$i]['VID'] != $row['VID']) {
                            $flag_D_change = TRUE;
                        }
                        $i++;
                    } else {
                        $flag_D_change = TRUE;
                    }
                }
            }

            /*
             * ตรวจสอบการลบตาราง ถ้าไม่มีการลบตารางถึงให้เข้าไปทำการจัดตาราง
             */
            if ($REMOVE_TSID_S == NULL && $REMOVE_TSID_D == NULL) {

                if ($flag_S_change) {
                    //ข้อมูลที่เรียงแล้วของ S พร้อมจะนำเข้าฐานข้อมูล
                    $old_vcs = $this->m_schedule_hr->get_list_destination_vehicle($rcode, $vtid, $RID_S);
                    for ($i = count($new_vehicles_has_schedules_S) - 1; $i >= 0; $i--) {
                        $temp = array_pop($old_vcs);
                        $new_vehicles_has_schedules_S[$i]['CurrentTime'] = $temp['CurrentTime'];
                    }

                    //อัพเดทตาราง t_schedules_day เพื่อบอกว่ายกเลิกรอบนั้นๆ และลบ TSID ออกจาก vehicles_has_schedules
                    $this->m_schedule_hr->update_vehicles_has_schedules_with_new_data($new_vehicles_has_schedules_S);
                    //อัพเดทตาราง vehicles_current_stations เพื่อแก้ไข้ตารางเวลาใหม่
                    foreach ($new_vehicles_has_schedules_S as $row) {
                        $this->m_schedule_hr->update_vehicle_curent_stations($row['VID'], NULL, $row['CurrentTime']);
                    }

                    $sort['ROUTE_DETAIL_S'] = $ROUTE_DETAIL_S;
                    $sort['RID_S'] = $RID_S;
                    $sort['Schedule_S'] = $new_vehicles_has_schedules_S;
                    $sort['Update_S'] = $vehicles_update_S;
                    $sort['Change_S'] = $flag_S_change;
                }

                if ($flag_D_change) {
                    //ข้อมูลที่เรียงแล้วของ D พร้อมจะนำเข้าฐานข้อมูล
                    $old_vcs = $this->m_schedule_hr->get_list_destination_vehicle($rcode, $vtid, $RID_D);
                    for ($i = count($new_vehicles_has_schedules_D) - 1; $i >= 0; $i--) {
                        $temp = array_pop($old_vcs);
                        $new_vehicles_has_schedules_D[$i]['CurrentTime'] = $temp['CurrentTime'];
                    }

                    //อัพเดทตาราง t_schedules_day เพื่อบอกว่ายกเลิกรอบนั้นๆ และลบ TSID ออกจาก vehicles_has_schedules
                    $this->m_schedule_hr->update_vehicles_has_schedules_with_new_data($new_vehicles_has_schedules_D);
                    //อัพเดทตาราง vehicles_current_stations เพื่อแก้ไข้ตารางเวลาใหม่
                    foreach ($new_vehicles_has_schedules_D as $row) {
                        $this->m_schedule_hr->update_vehicle_curent_stations($row['VID'], NULL, $row['CurrentTime']);
                    }

                    $sort['ROUTE_DETAIL_D'] = $ROUTE_DETAIL_D;
                    $sort['RID_D'] = $RID_D;
                    $sort['Schedule_D'] = $new_vehicles_has_schedules_D;
                    $sort['Update_D'] = $vehicles_update_D;
                    $sort['Change_D'] = $flag_D_change;
                }

                if ($flag_S_change || $flag_D_change) {
                    //Alert success and redirect to candidate
                    $alert['alert_message'] = "จัดข้อมูลตารางของ " . $route_name . " สำเร็จแล้ว";
                    $alert['alert_mode'] = "success";
                    $this->session->set_flashdata('alert', $alert);
                    redirect('schedule/view/' . $rcode . '/' . $vtid);
                }
            }
            /*
             * (จบ)ตรวจสอบการลบตาราง ถ้าไม่มีการลบตารางถึงให้เข้าไปทำการจัดตาราง
             */ else {
                $temp_SID = $this->m_schedule_hr->get_stations($rcode, $vtid);
                for ($i = count($REMOVE_TSID_S) - 1; $i >= 0; $i--) {
                    $temp_TSID = $REMOVE_TSID_S[$i];
                    $this->m_schedule_hr->update_t_schedules_day_status($temp_TSID, $RID_S, '0');
                    $this->m_schedule_hr->delete_vehicles_has_schedules($temp_TSID, $vehicles_update_S[$i]);
                    //อัพเดทตำแหน่งปัจจุบันของรถ
                    $temp = $this->m_schedule_hr->get_next_vehicle($rcode, $vtid, $RID_S)[0];
                    $duration = 10; //บวกเพิ่มแต่ 10 นาที ป้องกันเวลาเกินเที่ยงคืน
                    $t = date('H:i', strtotime("-$duration minutes", strtotime($temp['CurrentTime'])));
                    $new_last_time = $t;
                    $this->m_schedule_hr->update_vehicle_curent_stations($vehicles_update_S[$i], $temp_SID[0]['SID'], $new_last_time);
                }
                for ($i = count($REMOVE_TSID_D) - 1; $i >= 0; $i--) {
                    $temp_TSID = $REMOVE_TSID_D[$i];
                    $this->m_schedule_hr->update_t_schedules_day_status($temp_TSID, $RID_D, '0');
                    $this->m_schedule_hr->delete_vehicles_has_schedules($temp_TSID, $vehicles_update_D[$i]);
                    //อัพเดทตำแหน่งปัจจุบันของรถ
                    $temp = $this->m_schedule_hr->get_next_vehicle($rcode, $vtid, $RID_D)[0];
                    $duration = 10; //บวกเพิ่มแต่ 10 นาที ป้องกันเวลาเกินเที่ยงคืน
                    $t = date('H:i', strtotime("-$duration minutes", strtotime($temp['CurrentTime'])));
                    $new_last_time = $t;
                    $this->m_schedule_hr->update_vehicle_curent_stations($vehicles_update_D[$i], end($temp_SID)['SID'], $new_last_time);
                }
                //Alert success and redirect to candidate
                $alert['alert_message'] = "ลบข้อมูลตารางของ " . $route_name . " สำเร็จแล้ว";
                $alert['alert_mode'] = "success";
                $this->session->set_flashdata('alert', $alert);
                redirect('schedule/view/' . $rcode . '/' . $vtid);
            }



            //Alert success and redirect to candidate
//            $alert['alert_message'] = "เปลี่ยนแปลงข้อมูลตารางของ " . $route_name . " สำเร็จแล้ว";
//            $alert['alert_mode'] = "success";
//            $this->session->set_flashdata('alert', $alert);
//            redirect('schedule/view/' . $rcode . '/' . $vtid);
        }

        $data['vehicles_type'] = $this->m_vehicle_hr->get_vehicle_types();
        $data['route'] = $this->m_route_hr->get_route();
        $data['stations'] = $this->m_station_hr->get_stations($rcode, $vtid);
        $data['schedule_master'] = $this->m_route_hr->get_schedule_manual();

        $data['form_open'] = form_open('schedule/view/' . $rcode . '/' . $vtid, array('id' => 'form_main'));
        $data['form_close'] = form_close();

        $data_debug = array(
//            'vehicles_type' => $data['vehicles_type'],
//            'route' => $data['route'],
//            'route_detail' => $data['route_detail'],
//            'stations' => $data['stations'],
//            'form_search' => $data['form_search'],
//            'stations' => $data['stations']
//            'schedules' => $data['schedules'],
//            'check' => isset($post) ? $post : '',
//            'post' => $this->input->post(),
//            'sort' => isset($sort) ? $sort : '',
        );


        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title("ตารางเวลาเดิน $route_name");
        $this->m_template->set_Permission('SCV');
        $this->m_template->set_Content('schedule/view_schedule', $data);
        $this->m_template->showTemplate();
    }

    public function add($rcode, $vtid, $rid) {

        $route_detail = $this->m_route_hr->get_route($rcode, $vtid, $rid);
        $vt_name = $route_detail[0]['VTDescription'];
        $source = $route_detail[0]['RSource'];
        $desination = $route_detail[0]['RDestination'];

        $route_name = $vt_name . ' เส้นทาง ' . $route_detail[0]['RCode'] . ' ' . ' ' . $source . ' - ' . $desination;

        $data = array(
//            'form_search' => $this->m_schedule->set_form_search(),
            'page_title' => 'เพิ่มเที่ยวรถ',
            'page_title_small' => ' ' . $vt_name,
            'previous_page' => "schedule/view/$rcode/$vtid/$rid",
            'next_page' => "schedule/view/$rcode/$vtid/$rid",
        );

        $form_data = $rs = array();
        if ($this->m_schedule_hr->validation_form_add() && $this->form_validation->run() == TRUE) {
            $form_data = $this->m_schedule_hr->get_post_form_add();

            //ตรวจสอบมามีอยู่ของรถในคิว
            $temp = $this->m_schedule_hr->get_next_vehicle($rcode, $vtid, $rid);
            if (count($temp) > 0) {
                //เตรียมข้อมูลก่อนการ insert t_schedules_day
                $duration = $route_detail[0]['Time'];
                $t = date('H:i', strtotime("+$duration minutes", strtotime($form_data['TimeDepart'])));
                $prepare_t_schedules_day = array(
                    'TSID' => $form_data['TSID'],
                    'RID' => $form_data['RID'],
                    'Date' => $form_data['Date'],
                    'TimeDepart' => $form_data['TimeDepart'],
                    'TimeArrive' => $t,
                    'ScheduleStatus' => '1',
                    'ScheduleNote' => $form_data['ScheduleNote'],
                );
                //นำข้อมูลเข้า
                $result['insert'] = $this->m_schedule_hr->insert_new_t_schedules_day($prepare_t_schedules_day);


                /*
                 * ดึงข้อมูลการทำงานจัดเรียงลำดับใหม่
                 */
                $result['sort'] = $this->m_schedule_hr->get_schedule_to_sort($form_data['RID'], $this->m_datetime->getDateToday());
                $temp1 = $result['sort'];
                $temp2 = $result['sort'];
                $i2 = 0;
                $flag = FALSE;
                for ($i1 = 0; $i1 < count($temp1); $i1++, $i2++) {
                    if ($temp1[$i1]['VID'] == NULL && $flag != TRUE) {
                        if ($i2 + 1 < count($temp2)) {
                            $temp1[$i1]['VID'] = $temp2[$i2 + 1]['VID'];
                            $flag = TRUE;
                        }
                        if ($temp1[$i1]['VID'] == NULL && $i1 == (count($temp1) - 1))
                            $flag = TRUE;
                    }
                    if ($flag) {
                        if ($i2 + 1 < count($temp2)) {
                            $temp1[$i1]['VID'] = $temp2[$i2 + 1]['VID'];
                        } else {
                            //ตรวจสอบรถที่จะนำเขามาเสริมในคิว
                            $RID = $form_data['RID'];
                            $RCode = $form_data['RCode'];
                            $VTID = $route_detail[0]['VTID'];
                            $Free_VID = $this->m_schedule_hr->get_next_vehicle($RCode, $VTID, $RID);
                            $temp1[$i1]['VID'] = $Free_VID[0]['VID'];

                            //ตรวจสอบสถานีสุดท้าย ตัวนี้จะเป็นถ้าเริ่ม S ต้องไปเอาตัวสุดท้าย, ถ้าเป็น D ต้องไปเอาตัวแรก
                            $StartPoint = $this->m_schedule_hr->get_route_detail($RCode, $VTID, $RID)[0]['StartPoint'];
                            if ($StartPoint == 'S') {
                                $temp = end($this->m_schedule_hr->get_stations($RCode, $VTID));
                                $next_station_id = $temp['SID'];
                            } else {
                                $temp = $this->m_schedule_hr->get_stations($RCode, $VTID)[0];
                                $next_station_id = $temp['SID'];
                            }

                            //ดึงเวลาสุดท้ายเพื่อไปต่อคิว
                            //อัพเดทตำแหน่งปัจจุบันของรถ
                            $temp = end($this->m_schedule_hr->get_list_destination_vehicle($RCode, $VTID, $RID));
                            $duration = 10; //บวกเพิ่มแต่ 10 นาที ป้องกันเวลาเกินเที่ยงคืน
                            $t = date('H:i', strtotime("+$duration minutes", strtotime($temp['CurrentTime'])));
                            $new_last_time = $t;
                            $this->m_schedule_hr->update_vehicle_curent_stations($Free_VID[0]['VID'], $next_station_id, $new_last_time);
                            // อัพเดทตาราง vehicles_has_schedules ที่ได้จากการ sort
                            $this->m_schedule_hr->update_vehicles_has_schedules_with_new_data($temp1);
                        }
                    }
                }
                //Alert success and redirect to candidate
                $alert['alert_message'] = "เพิ่มรอบ " . $vt_name . " เวลา " . $form_data['TimeDepart'] . " ของเส้นทาง " . $route_name . " สำเร็จแล้ว";
                $alert['alert_mode'] = "success";
                $this->session->set_flashdata('alert', $alert);
                redirect('schedule/view/' . $rcode . '/' . $vtid);
            } else {
                //ไม่มีรถในคิวให้ทำการเพิ่ม
                //Alert fail and redirect to candidate
                $alert['alert_message'] = "ไม่มีรถในคิว ให้ทำการเพิ่มได้";
                $alert['alert_mode'] = "danger";
                $this->session->set_flashdata('alert', $alert);
                redirect('schedule/add/' . $rcode . '/' . $vtid . '/' . $rid);
            }
        }

        $data['form'] = $this->m_schedule_hr->set_form_add($rcode, $vtid, $rid);
        $get_vehicle_current_stations = $this->m_schedule_hr->get_vehicle_current_stations($rcode, $vtid, 1);
        $data_debug = array(
//            'vehicles_type' => $data['vehicles_type'],
//            'route' => $data['route'],
//            'route_detail' => $route_detail,
//            'stations' => $data['stations'],
//            'form_search' => $data['form_search'],
//            'stations' => $data['stations']
//            'schedules' => $data['schedules'],
//            'post' => $this->input->post(),
//            'form_data' => $form_data,
//            'get_vehicle_current_stations' => $get_vehicle_current_stations,
//            'prepare_t_schedules_day' => isset($prepare_t_schedules_day) ? $prepare_t_schedules_day : '',
//            'result' => isset($result) ? $result : '',
//            'temp' => isset($temp1) ? $temp1 : '',
//            'last' => isset($Free_VID) ? $Free_VID : '',
//            'temp1' => $this->m_schedule->get_schedule_to_sort($rid, $this->m_datetime->getDateToday()),
//            'temp2' => $this->m_schedule->get_schedule_to_sort($rid, $this->m_datetime->getDateToday()),
        );



        $this->m_template->set_Debug($data_debug);
        $this->m_template->set_Title("ตารางเวลาเดิน $route_name");
        $this->m_template->set_Permission('SCA');
        $this->m_template->set_Content('schedule/frm_schedule', $data);
        $this->m_template->showTemplate();
    }

    //    ตรวจสอบค่าใน dropdown
    public function check_dropdown($str) {
        if ($str === '0') {
            $this->form_validation->set_message('check_dropdown', 'เลือก %s');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_schedule($str) {
        $rid = $this->input->post('RID');
        $time_depart = $str; //$this->input->post('TimeDepart');
        $date = $this->input->post('Date');

        if ($this->m_schedule_hr->IsExitSchedule($date, $rid, $time_depart)) {
            $this->form_validation->set_message('check_schedule', '%s ถูกใช้งานแล้ว');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function change_vehicle($TSID) {
        $temp = $this->m_schedule_hr->get_route_detail_by_TSID($TSID)[0];
        $temp_vehicle = $this->m_schedule_hr->get_vehicle($temp['RCode'], $temp['VTID']);
        $route_name = $temp['VTDescription'] . ' เส้นทาง ' . $temp['RCode'] . ' ' . $temp['RSource'] . ' - ' . $temp['RDestination'];
        if ($temp['StartPoint'] == 'S') {
            $route_name .= ' (ไป ' . $temp['RDestination'] . ')';
        } else {
            $route_name .= ' (ไป ' . $temp['RSource'] . ')';
        }
        $data = array(
            'page_title' => 'แก้ไขตารางเวลาเดิน',
            'page_title_small' => ' ' . $route_name,
            'detail' => $temp,
        );

        if ($this->m_schedule_hr->validation_form_change() && $this->form_validation->run() == TRUE) {
            $data['post'] = $this->input->post();
            $data['post']['TSID'] = $TSID;
            if ($this->m_schedule_hr->update_change_vehicle($data['post'])) {
                //Alert success
                $alert['alert_message'] = "แก้ไขตารางเวลาเดิน " . $route_name . ' สำเร็จ';
                $alert['alert_mode'] = "success";
                $this->session->set_flashdata('alert', $alert);
                redirect('schedule/view/' . $temp['RCode'] . '/' . $temp['VTID']);
            } else {
                //Alert fail
                $alert['alert_message'] = "แก้ไขตารางเวลาเดิน " . $route_name . ' ไม่สำเร็จ';
                $alert['alert_mode'] = "danger";
                $this->session->set_flashdata('alert', $alert);
                redirect('schedule/view/' . $temp['RCode'] . '/' . $temp['VTID']);
            }
        }

        $data['form']['open'] = form_open('schedule/change_vehicle/' . $TSID);
        $data['form']['input'] = $this->m_schedule_hr->set_form_change($temp['RCode'], $temp['VTID'], $temp['VID']);
        $data['form']['close'] = form_close();

//        $this->m_template->set_Debug($data);
        $this->m_template->set_Title("แก้ไขตารางเวลาเดิน $route_name");
        $this->m_template->set_Permission('SCC');
        $this->m_template->set_Content('schedule/change_vehicle', $data);
        $this->m_template->showTemplate();
    }

}
