<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/03/13
 * Time: 20:52
 */

namespace mod_cpassignment\report;

use \mod_cpassignment\constants;

class attempts extends basereport
{

    protected $report="attempts";
    protected $fields = array('id', 'status', 'mediafile','grade_p','timecreated','action');
    protected $formdata=null;
    protected $headingdata = null;
    protected $qcache=array();
    protected $ucache=array();


    public function fetch_formatted_field($field, $record, $withlinks)
    {
        global $DB, $CFG, $OUTPUT;
        switch ($field) {
            case 'id':
                $ret = $record->id;
                break;

            case 'status':
               if ($record->status == constants::M_SUBMITSTATUS_SELECTED) {
                        $ret = get_string('submitted', constants::M_LANG);
                } else {

                    $ret = "-";
                }
                break;

            case 'mediafile':
                if ($withlinks) {

                    $ret = \html_writer::div('<i class="fa fa-play-circle"></i>',
                        constants::M_HIDDEN_PLAYER_BUTTON, array('data-audiosource' => $record->mediaurl));

                } else {
                    $ret = $record->mediaurl;
                }
                break;
                break;

            case 'grade_p':
                $ret = $record->sessionscore;
                break;

            case 'timecreated':
                $ret = date("Y-m-d H:i:s", $record->timecreated);
                break;

            case 'action':
                $ret = '';
                $url = new \moodle_url(constants::M_URL . '/manageattempts.php',
                    array('action' => 'delete', 'n' => $record->cpassignmentid, 'attemptid' => $record->id));
                $btn = new \single_button($url, get_string('delete'), 'post');
                $btn->add_confirm_action(get_string('deleteattemptconfirm', constants::M_LANG));
                $ret = $OUTPUT->render($btn);


                $url = new \moodle_url(constants::M_URL . '/manageattempts.php',
                    array('action' => 'selectattempt', 'n' => $record->cpassignmentid, 'attemptid' => $record->id,'userid'=>$record->userid));
                $btn = new \single_button($url, get_string('submit'), 'post');
                $btn->add_confirm_action(get_string('submitbyuserconfirm', constants::M_LANG));
                $ret .= $OUTPUT->render($btn);
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
        $record = $this->headingdata;
        $ret='';
        if(!$record){return $ret;}
        $user = $this->fetch_cache('user', $this->formdata->userid);
        $username = fullname($user);
        return get_string('userattemptsheading',constants::M_LANG,$username );

    }

    public function process_raw_data($formdata){
        global $DB;

        //heading data
        $this->headingdata = new \stdClass();
        $this->formdata = $formdata;

        $emptydata = array();
        $alldata = $DB->get_records(constants::M_USERTABLE,array('cpassignmentid'=>$formdata->cpassignmentid,'userid'=>$formdata->userid));

        if($alldata){
            foreach($alldata as $thedata){
                $thedata->mediaurl  = $thedata->filename;
                $this->rawdata[] = $thedata;
            }
            $this->rawdata= $alldata;
        }else{
            $this->rawdata= $emptydata;
        }
        return true;
    }

}