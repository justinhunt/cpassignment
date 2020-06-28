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
    print_error(0,'You must specify a course_module ID or an instance ID');
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

// Set up the page header.
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

// Get an admin settings.
$config = get_config(constants::M_COMP);

// Get our renderers.
$renderer = $PAGE->get_renderer('mod_cpassignment');
$submissionrenderer = $PAGE->get_renderer(constants::M_COMP,'submission');



// Are we allowed?
$haspermission = (has_capability('mod/cpassignment:view', $modulecontext));
if (!$haspermission) {
    //no permission to attempt, so there you have it
    $reason =  get_string('hasnopermission', constants::M_LANG) ;
    // Show our header
    echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('view',
            constants::M_LANG));
    echo $renderer->why_cannot_attempt($reason);
    echo $renderer->footer();

}

// Do we have attempts?
$attempts = $DB->get_records(constants::M_USERTABLE,array('userid'=>$USER->id,
        constants::M_MODNAME.'id' => $moduleinstance->id), 'id DESC');


if(true) {
    echo $renderer->display_list_page($moduleinstance,$cm,$config, $attempts);
}else{
    echo $renderer->display_view_page($moduleinstance,$cm,$config,$modulecontext, $submissionrenderer, $attempts);
}