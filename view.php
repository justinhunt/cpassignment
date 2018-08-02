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
use \mod_cpassignment\submission;

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

// Has it been graded yet?
$graded = false;
$gradedattemptid = 0;
foreach ($attempts as $attempt) {
    if ( (!$graded) && $attempt->status == constants::M_SUBMITSTATUS_GRADED) {
        $graded = true;
        $gradedattemptid = $attempt->id;
    }
}

// Are we allowed?
$haspermission = (has_capability('mod/cpassignment:view', $modulecontext));

// Status content is added to instructions.
$status = '';

if ($haspermission) {

    // Is this a retake?
    if ($numattempts > 0) {

        // Show a list of attempt data and status here.  Allow user to submit
        // until graded.
        // TRY page.
        echo $renderer->fetch_attempts($attempts, fullname($USER), $graded);

        // Grade information.
        if ($graded) {
            //We probably do not need this. Until we have a flashy submission/transcript/text reader widget
            // $submission->prepare_javascript($reviewmode);
            // We don't show any grading information if dis-allowed in settings.
            if ($moduleinstance->showgrade) {
                $submission = new \mod_cpassignment\submission($gradedattemptid,
                        $modulecontext->id);
                $status .= $submissionrenderer->render_submission($submission,
                        $moduleinstance->showgrade);
            } else {
                $status .= get_string('gradeunavailable', constants::M_LANG);
            }
        } else {
            $status .= get_string("notgradedyet",constants::M_LANG);
        }
        // Try again button, if applicable.
        if ( (!$attemptsexceeded) && (!$graded) ) {
            $status .= $renderer->js_trigger_button('startbutton', false,
                    get_string('reattempt', constants::M_LANG));
        }
    } else { // numattempts = 0. TOP page.
        $status .= $renderer->js_trigger_button('startbutton',false,
                get_string('firstattempt', constants::M_LANG));
    }

} else {
    // Can't attempt - say why.
    if (!$haspermission) {
        echo '<p>' . get_string('hasnopermission', constants::M_LANG) . '</p>';
    } else if ($attemptsexceeded) {
        echo '<p>' . get_string('exceededattempts', constants::M_LANG,
                $moduleinstance->maxattempts) . '</p>';
    } else if ($graded) {
        echo '<p>' . get_string('alreadygraded', constants::M_LANG,
                $moduleinstance->maxattempts) . '</p>';
    } else {
        echo '<p>' . get_string('unknown', constants::M_LANG) . '</p>';
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
echo $renderer->show_finished($moduleinstance, $finished);
echo $renderer->show_error($moduleinstance,$cm);
//echo $renderer->show_passage($moduleinstance,$cm);
echo $renderer->show_recorder($moduleinstance, $token);
echo $renderer->show_uploadsuccess($moduleinstance);
//echo $renderer->cancelbutton($cm);

// The module AMD code.
$pagemode="summary";
echo $renderer->fetch_activity_amd($cm, $moduleinstance, $pagemode);

// Finish the page.
echo $renderer->footer();
