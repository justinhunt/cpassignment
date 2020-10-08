<?php


/**
 * External class.
 *
 * @package mod_readaloud
 * @author  Justin Hunt - Poodll.com
 */

use \mod_cpassignment\utils;
use \mod_cpassignment\constants;

class mod_cpassignment_external extends external_api {

    public static function submit_attempt_parameters() {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
                'filename' => new external_value(PARAM_TEXT)
        ]);
    }

    public static function submit_attempt($cmid,$filename) {
        global $DB, $USER;

        $params = self::validate_parameters(self::submit_attempt_parameters(),
                array('cmid'=>$cmid,'filename'=>$filename));
        extract($params);

        $cm = get_coursemodule_from_id(constants::M_MODNAME, $cmid, 0, false, MUST_EXIST);
        $themodule = $DB->get_record(constants::M_TABLE, array('id' => $cm->instance), '*', MUST_EXIST);
        $modulecontext = \context_module::instance($cm->id);

        //make database items and adhoc tasks
        $ret = new stdClass();
        $ret->success = false;
        $newattempt = utils::save_to_moodle($filename, $themodule);

        if($newattempt){
            if(\mod_cpassignment\utils::can_transcribe($themodule)) {
                $ret->success = utils::register_aws_task($themodule->id, $newattempt->id, $modulecontext->id);
                if(!$ret->success){
                    $ret->message = "Unable to create adhoc task";
                }
            }else{
                $ret->success = true;
                $ret->newattempt = $newattempt;
            }
            //make this the selected attempt
            utils::select_attempt_as_submission($themodule,$USER->id,$newattempt->id);
        }else{
            $ret->message = "Unable to add update database with submission";
        }

        return json_encode($ret);
    }

    public static function submit_attempt_returns() {
        return new external_value(PARAM_RAW);
    }

    public static function submit_rec_parameters() {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
                'subid' => new external_value(PARAM_INT),
                'filename' => new external_value(PARAM_TEXT),
                'itemname' => new external_value(PARAM_TEXT),
                'itemid' => new external_value(PARAM_TEXT),
                'accesskey' => new external_value(PARAM_TEXT)
        ]);
    }

    public static function submit_rec($cmid,$subid, $filename,$itemname,$itemid, $accesskey) {
        global $DB, $USER;

        $params = self::validate_parameters(self::submit_rec_parameters(),
                array('cmid'=>$cmid,'subid'=>$subid,'filename'=>$filename,'itemname'=>$itemname,'itemid'=>$itemid,'accesskey'=>$accesskey));
        extract($params);

        $cm = get_coursemodule_from_id(constants::M_MODNAME, $cmid, 0, false, MUST_EXIST);
        $themodule = $DB->get_record(constants::M_TABLE, array('id' => $cm->instance), '*', MUST_EXIST);
        $modulecontext = \context_module::instance($cm->id);

        //make database items and adhoc tasks
        $ret = new stdClass();
        $ret->success = false;
        $item = utils::save_rec_to_moodle( $themodule, $filename, $subid, $itemname,$itemid,$accesskey);

        if($item){
                $ret->success = true;
                $ret->item = $item;

        }else{
            $ret->message = "Unable to add update database with submission";
        }

        return json_encode($ret);
    }

    public static function submit_rec_returns() {
        return new external_value(PARAM_RAW);
    }

    public static function remove_rec_parameters() {
        return new external_function_parameters([
                'itemid' => new external_value(PARAM_TEXT),
        ]);
    }

    public static function remove_rec($itemid) {
        global $DB, $USER;

        $params = self::validate_parameters(self::remove_rec_parameters(),
                array('itemid'=>$itemid));
        extract($params);

        //make database items and adhoc tasks
        $ret = new stdClass();
        $ret->success = false;
        $result = utils::remove_rec_from_moodle($itemid);

        if($result){
            $ret->success = true;
        }else{
            $ret->message = "Unable to add remove submission from database";
        }

        return json_encode($ret);
    }

    public static function remove_rec_returns() {
        return new external_value(PARAM_RAW);
    }



    public static function select_attempt_parameters() {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
                'attemptid' => new external_value(PARAM_INT)
        ]);
    }

    public static function select_attempt($cmid,$attemptid) {
        global $DB, $USER;

        $params = self::validate_parameters(self::select_attempt_parameters(),
                array('cmid'=>$cmid,'attemptid'=> $attemptid));
        extract($params);

        $cm = get_coursemodule_from_id(constants::M_MODNAME, $cmid, 0, false, MUST_EXIST);
        $themodule = $DB->get_record(constants::M_TABLE, array('id' => $cm->instance), '*', MUST_EXIST);

        //handle return to Moodle
        $ret = new stdClass();
        if(!$attemptid){
            $ret->success=false;
            $ret->message="You must specify an attemptid";
        }else {
            $ret->success = utils::select_attempt_as_submission($themodule, $USER->id, $attemptid);
            if(!$ret->success){
                $ret->message="Unable to select attempt as submission";
            }
        }

        return json_encode($ret);
    }

    public static function select_attempt_returns() {
        return new external_value(PARAM_RAW);
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function reset_key_parameters() {
        $params = array();
        $params['moduleid'] = new external_value(PARAM_INT, 'The id of the activity');

        return new external_function_parameters(
                $params
        );
    }

    /*
  * Reset CPAPI secret
    * $username
    * $currentsecret
  */
    public static function reset_key($moduleid){

        global $DB, $USER;

        $rawparams = array();
        $rawparams['moduleid'] = $moduleid;


        //Parameter validation
        $params = self::validate_parameters(self::reset_key_parameters(),
                $rawparams);

        //Context validation
        //OPTIONAL but in most web service it should present
        $moduleinstance  = $DB->get_record('cpassignment', array('id' => $moduleid), '*', MUST_EXIST);
        $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
        $cm         = get_coursemodule_from_instance('cpassignment', $moduleinstance->id, $course->id, false, MUST_EXIST);
        $modulecontext = context_module::instance($cm->id);
        self::validate_context($modulecontext);

        //Capability checking
        if (!has_capability('mod/cpassignment:view', $modulecontext)) {
            throw new moodle_exception('nopermission');
        }



        //do the job and process the result
        $newkey = utils::do_reset_accesskey($params['moduleid']);

        //handle return to Moodle
        $ret = new stdClass();
        if(!$newkey){
            $ret->success=false;
            $ret->message="could not get a new key";
        }else {
            $ret->success = true;
            $ret->message=$newkey;
        }

        return json_encode($ret);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function reset_key_returns() {
        return new external_value(PARAM_RAW);
    }//end of function
}
