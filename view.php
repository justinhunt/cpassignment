<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of cpassignment
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
use \mod_cpassignment\constants;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$retake = optional_param('retake', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // cpassignment instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('cpassignment', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance  = $DB->get_record('cpassignment', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $moduleinstance  = $DB->get_record('cpassignment', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('cpassignment', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$PAGE->set_url('/mod/cpassignment/view.php', array('id' => $cm->id));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

// Trigger module viewed event.
$event = \mod_cpassignment\event\course_module_viewed::create(array(
   'objectid' => $moduleinstance->id,
   'context' => $modulecontext
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('cpassignment', $moduleinstance);
$event->trigger();


//if we got this far, we can consider the activity "viewed"
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

//are we a teacher or a student?
$mode= "view";

/// Set up the page header
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

//Get an admin settings
$config = get_config(constants::M_FRANKY);

//Get our renderers
$renderer = $PAGE->get_renderer('mod_cpassignment');
$submissionrenderer = $PAGE->get_renderer(constants::M_FRANKY,'submission');

//if we are in review mode, lets review
$attempts = $DB->get_records(constants::M_USERTABLE,array('userid'=>$USER->id,'cpassignmentid'=>$moduleinstance->id),'id DESC');

//can attempt ?
$canattempt = has_capability('mod/cpassignment:preview',$modulecontext);
if(!$canattempt || $moduleinstance->maxattempts > 0){
	$canattempt=true;
	$attempts =  $DB->get_records(constants::M_USERTABLE,array('userid'=>$USER->id, constants::M_MODNAME.'id'=>$moduleinstance->id));
	if($attempts && count($attempts)>=$moduleinstance->maxattempts){
		$canattempt=false;
	}
}

//reset our retake flag if we cant reatempt
if(!$canattempt){$retake=0;}

//display previous attempts if we have them
if($attempts && $retake==0){
		//if we are teacher we see tabs. If student we just see the quiz
		if(has_capability('mod/cpassignment:preview',$modulecontext)){
			echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view', constants::M_LANG));
		}else{
			echo $renderer->notabsheader();
		}
		$latestattempt = array_shift($attempts);

		// show results if graded
		if ($latestattempt->sessiontime==null) {
			echo $renderer->show_ungradedyet();
		} else {
			$submission = new \mod_cpassignment\submission($latestattempt->id,$modulecontext->id);
			$reviewmode =true;
			$submission->prepare_javascript($reviewmode);
			echo $submissionrenderer->render_submission($submission);
		}

		// Show  button or a label depending on attempts made.
		if ($canattempt) {
			echo $renderer->reattemptbutton($moduleinstance,
                    get_string('reattempt',constants::M_FRANKY));
		}else{
			echo $renderer->exceededattempts($moduleinstance);
		}
		echo $renderer->footer();
		return;
}

//From here we actually display the page.

//if we are teacher we see tabs. If student we just see the activity
if(has_capability('mod/cpassignment:preview',$modulecontext)){
	echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view', constants::M_LANG));
}else{
	echo $renderer->notabsheader();
}

//fetch token
$token = \mod_cpassignment\utils::fetch_token($config->apiuser,$config->apisecret);


//show all the main parts. Many will be hidden and displayed by JS

// Process plugin files for standard editor component.
$instructions = file_rewrite_pluginfile_urls($moduleinstance->instructions,
    'pluginfile.php', $modulecontext->id, constants::M_MODNAME,
    constants::M_FILEAREA_INSTRUCTIONS, $moduleinstance->id);
$completion = file_rewrite_pluginfile_urls($moduleinstance->completion,
    'pluginfile.php', $modulecontext->id, constants::M_MODNAME,
    constants::M_FILEAREA_COMPLETION, $moduleinstance->id);

echo $renderer->show_instructions($moduleinstance, $instructions);
echo $renderer->show_completion($moduleinstance, $cm, $completion);
echo $renderer->show_error($moduleinstance,$cm);
//echo $renderer->show_passage($moduleinstance,$cm);
echo $renderer->show_recorder($moduleinstance,$token);
echo $renderer->show_progress($moduleinstance,$cm);

echo $renderer->cancelbutton($cm);

//the module AMD code
echo $renderer->fetch_activity_amd($cm, $moduleinstance);

// Finish the page
echo $renderer->footer();
