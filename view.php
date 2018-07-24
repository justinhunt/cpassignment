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

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // cpassignment instance ID.

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

// If we got this far, we can consider the activity "viewed"
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

//are we a teacher or a student?
$mode= "view";

// Set up the page header.
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

// Get an admin settings.
$config = get_config(constants::M_FRANKY);

// Get our renderers.
$renderer = $PAGE->get_renderer('mod_cpassignment');
$submissionrenderer = $PAGE->get_renderer(constants::M_FRANKY,'submission');

// Student or teacher view?
if(has_capability('mod/cpassignment:preview',$modulecontext)){
    echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view',
        constants::M_LANG));
} else {
    echo $renderer->notabsheader();
}

// Do we have attempts?
$attempts = $DB->get_records(constants::M_USERTABLE,array('userid'=>$USER->id,
        constants::M_MODNAME.'id' => $moduleinstance->id), 'id DESC');
$numattempts = count($attempts);

// How many allowed?
$max = $moduleinstance->maxattempts;
$attemptsexceeded = 0;

if ( ($max != 0)  && ($numattempts >= $max) ) {
    $attemptsexceeded = 1;
  }

// Are we allowed?
$haspermission = has_capability('mod/cpassignment:preview', $modulecontext);
$canattempt = ($haspermission && ($attemptsexceeded == 0));

// Status content is added to instructions.
$status = '';
//$status = 'attempts ' . $numattempts . ' exc: ' . $attemptsexceeded .
//        ' can: ' . $canattempt . ' ret: '. $retake . ': ';
if ($canattempt) {
    // Is this a retake?
    if ($numattempts > 0) {
        // TRY page.
        // Get the latest attempt, if it exists.
        $latestattempt = array_shift($attempts);
        $submission = new \mod_cpassignment\submission($latestattempt->id, $modulecontext->id);
        $reviewmode = true;

        //We probably do not need this. Until we have a flashy submission/transcript/text reader widget
        //$submission->prepare_javascript($reviewmode);

        // Submission html (might need tweaking).
        $status .= $submissionrenderer->render_submission($submission);

        // Graded yet? TRY page awaiting grading
        // JUSTIN comment ... do we need to stop them re-attempting if ungraded ? I do not think so ..
        if ($latestattempt->sessiontime == null) {
            $status .= $renderer->show_ungradedyet();
        }
        //JUSTIN comment
        //} else {

            // TRY page with submission graded
            if (!$attemptsexceeded) {
                $status .= $renderer->startbutton($moduleinstance,
                        get_string('reattempt', constants::M_FRANKY));
            }
        //JUSTIN comment
        //}

    } else { // numattempts = 0. TOP page.
        $status .= $renderer->startbutton($moduleinstance,
                get_string('firstattempt', constants::M_FRANKY));
    }

} else {
    // Can't attempt - say why.
    if (!$haspermission) {
        $status .= 'no permission ';
        echo $renderer->cannotattempt(get_string('hasnopermission', constants::M_FRANKY));
    } else if ($attemptsexceeded) {
        $status .= 'no attempts ';
        echo $renderer->exceededattempts($moduleinstance);
    } else {
        $status .= 'unkown ';
        echo $renderer->cannotattempt(get_string('unknown', constants::M_FRANKY));
    }
}

// Fetch token.
$token = \mod_cpassignment\utils::fetch_token($config->apiuser,
        $config->apisecret);

// Process plugin files for standard editor component.
$instructions = file_rewrite_pluginfile_urls($moduleinstance->instructions,
        'pluginfile.php', $modulecontext->id, constants::M_FRANKY,
        constants::M_FILEAREA_INSTRUCTIONS, 0);
$instructions = format_text($instructions);
$finished = file_rewrite_pluginfile_urls($moduleinstance->finished,
        'pluginfile.php', $modulecontext->id, constants::M_FRANKY,
        constants::M_FILEAREA_FINISHED, 0);
$finished = format_text($finished);

// Show all the main parts. Many will be hidden and displayed by JS.
echo $renderer->show_instructions($moduleinstance, $instructions, $status);
echo $renderer->show_finished($moduleinstance, $cm, $finished);
echo $renderer->show_error($moduleinstance,$cm);
//echo $renderer->show_passage($moduleinstance,$cm);
echo $renderer->show_recorder($moduleinstance,$token);
echo $renderer->show_uploadsuccess($moduleinstance);
echo $renderer->cancelbutton($cm);

// The module AMD code.
$pagemode="summary";
echo $renderer->fetch_activity_amd($cm, $moduleinstance, $pagemode);

// Finish the page.
echo $renderer->footer();
