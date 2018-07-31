<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/03/13
 * Time: 20:52
 */

namespace mod_cpassignment\report;

use \mod_cpassignment\constants;
use \mod_cpassignment\submission;

class grading extends basereport
{

    protected $report = "grading";
    protected $fields = array('id', 'username', 'mediafile', 'totalattempts', 'grade_p',
        'timecreated', 'gradenow', 'viewall');
    protected $headingdata = null;
    protected $qcache = array();
    protected $ucache = array();


    public function fetch_formatted_field($field, $record, $withlinks)
    {
        global $DB, $CFG, $OUTPUT;
        switch ($field) {
            case 'id':
                $ret = $record->id;
                break;

            case 'username':
                $user = $this->fetch_cache('user', $record->userid);
                $ret = fullname($user);
                if ($withlinks) {
                    $link = new \moodle_url(constants::M_URL . '/grading.php',
                        array('action' => 'viewingbyuser', 'n' => $record->cpassignmentid, 'userid' => $record->userid));
                    $ret = \html_writer::link($link, $ret);
                }
                break;

            case 'totalattempts':
                $ret = $record->totalattempts;
                break;

            case 'mediafile':
                if ($withlinks) {
                    $ret = \html_writer::div('<i class="fa fa-play-circle"></i>', constants::M_HIDDEN_PLAYER_BUTTON, array('data-audiosource' => $record->mediaurl));

                } else {
                    $ret = get_string('submitted', constants::M_LANG);
                }
                break;


            case 'grade_p':
                $ret = $record->sessionscore;
                break;

            case 'timecreated':
                $ret = date("Y-m-d H:i:s", $record->timecreated);
                break;

            case 'gradenow':
                if ($withlinks) {
                    $link = new \moodle_url(constants::M_URL . '/grading.php',
                            array('action' => 'gradenow', 'n' => $record->cpassignmentid,
                            'userid' => $record->userid, 'attemptid' => $record->id));
                    $ret = \html_writer::link($link, get_string('gradenow',
                                constants::M_LANG));
                } else {
                    $ret = get_string('cannotgradenow', constants::M_LANG);
                }
                break;
            // So this used to be delete but now has the previous "total attempts code".
            case 'viewall':
                $ret = get_string('viewall', constants::M_LANG);
                $link = new \moodle_url(constants::M_URL . '/grading.php',
                        array('action' => 'viewingbyuser', 'n' => $record->cpassignmentid,
                        'userid' => $record->userid));
                $ret = \html_writer::link($link, $ret);
                break;

            default:
                if (property_exists($record, $field)) {
                    $ret = $record->{$field};
                } else {
                    $ret = '';
                }
        }
        return $ret;

    } //end of function


    public function fetch_formatted_heading()
    {
        $record = $this->headingdata;
        $ret = '';
        if (!$record) {
            return $ret;
        }
        //$ec = $this->fetch_cache(constants::M_TABLE,$record->englishcentralid);
        return get_string('gradingheading', constants::M_LANG);

    }//end of function

    public function process_raw_data($formdata)
    {
        global $DB;

        //heading data
        $this->headingdata = new \stdClass();

        $emptydata = array();
        $user_attempt_totals = array();
        $alldata = $DB->get_records(constants::M_USERTABLE, array('cpassignmentid' => $formdata->cpassignmentid), 'id DESC, userid');

        if ($alldata) {

            foreach ($alldata as $thedata) {

                //we ony take the most recent attempt
                if (array_key_exists($thedata->userid, $user_attempt_totals)) {
                    $user_attempt_totals[$thedata->userid] = $user_attempt_totals[$thedata->userid] + 1;
                    continue;
                }
                $user_attempt_totals[$thedata->userid] = 1;

                $thedata->mediaurl = $thedata->filename;
                $this->rawdata[] = $thedata;
            }
            foreach ($this->rawdata as $thedata) {
                $thedata->totalattempts = $user_attempt_totals[$thedata->userid];
            }
        } else {
            $this->rawdata = $emptydata;
        }
        return true;
    }//end of function
}//end of class