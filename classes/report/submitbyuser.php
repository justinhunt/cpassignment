<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/03/13
 * Time: 20:52
 */

namespace mod_cpassignment\report;

use \mod_cpassignment\constants;
use \mod_cpassignment\utils;

class submitbyuser extends basereport
{

    protected $report="submitbyuser";
    protected $fields = array('id', 'mediafile', 'status', 'timecreated', 'submit');
    protected $headingdata = null;
    protected $qcache=array();
    protected $ucache=array();

    // When selecting an attempt for submission, check it hasn't already been done.
    private $submitted;

    public function fetch_formatted_field($field, $record, $withlinks) {
        global $DB, $CFG, $OUTPUT;

        switch ($field) {
            case 'id':
                $ret = $record->id;
                break;
            /*
            case 'username':
                $user = $this->fetch_cache('user', $record->userid);
                $ret = fullname($user);
                break;
            */
            case 'mediafile':
                if ($withlinks) {

                    $ret = \html_writer::div('<i class="fa fa-play-circle"></i>',
                        constants::M_HIDDEN_PLAYER_BUTTON, array('data-audiosource' => $record->mediaurl));

                } else {
                    $ret = get_string('submitted', constants::M_LANG);
                }
                break;

            case 'status':
                if ($record->status == constants::M_SUBMITSTATUS_SELECTED) {
                        $ret = get_string('submitted', constants::M_LANG);
                } else {

                    $ret = '';
                }
                break;

            case 'timecreated':
                $ret = date("Y-m-d H:i:s", $record->timecreated);
                break;

            case 'submit':
                // The submit button
                $url = new \moodle_url(constants::M_URL . '/manageattempts.php',
                        array('action' => 'submitbyuser', 'n' => $record->cpassignmentid,
                        'attemptid' => $record->id, 'source' => $this->report));
                $btn = new \single_button($url, get_string('submit'), 'post');
                $btn->add_confirm_action(get_string('submitbyuserconfirm',
                        constants::M_LANG));
                $ret = $OUTPUT->render($btn);
                break;

            default:
                if (property_exists($record, $field)) {
                    $ret = $record->{$field};
                } else {
                    $ret = '';
                }
        }
        return $ret;
    }

    public function fetch_formatted_heading(){
        global $USER;

        $record = $this->headingdata;
        $ret='';
        if(!$record){return $ret;}
        return get_string('submitbyuserheading', constants::M_LANG, fullname($USER));

    }

    public function process_raw_data($formdata){
        global $DB;

        //heading data
        $this->headingdata = new \stdClass();

        $emptydata = array();
        $alldata = $DB->get_records(constants::M_USERTABLE,array('cpassignmentid'=>$formdata->cpassignmentid,'userid'=>$formdata->userid));

        if($alldata){
            $this->submitted = false;
            foreach($alldata as $thedata){
                $thedata->mediaurl  = $thedata->filename;
                if ($thedata->status == constants::M_SUBMITSTATUS_SELECTED) {
                    $this->submitted = true;
                }
                $this->rawdata[] = $thedata;
            }
            $this->rawdata= $alldata;
        }else{
            $this->rawdata= $emptydata;
        }
        return true;
    }
}