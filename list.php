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
use \mod_cpassignment\utils;

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

$PAGE->set_url('/mod/cpassignment/list.php', array('id' => $cm->id));
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
//$PAGE->set_pagelayout('embedded');
$PAGE->set_pagelayout('course');

// Get an admin settings.
$config = get_config(constants::M_COMP);

// Get our renderers.
$renderer = $PAGE->get_renderer(constants::M_COMP);
$submissionrenderer = $PAGE->get_renderer(constants::M_COMP,'submission');

// Do we have items?
$items = $DB->get_records(constants::M_USERTABLE,array('userid'=>$USER->id,
        constants::M_MODNAME.'id' => $moduleinstance->id), 'id DESC');
$itemcount = count($items);

//Prepare datatable(before header printed)
$tableid = '' . constants::M_CLASS_ITEMTABLE . '_' . '_opts_9999';
$renderer->setup_datatables($tableid);

// Show our header
 echo $renderer->notabsheader($moduleinstance, $cm, $mode, null, get_string('view',
        constants::M_LANG));


// How many allowed?
$max = $moduleinstance->maxattempts;
$attemptsexceeded = 0;

if ( ($max != 0)  && ($numattempts >= $max) ) {
    $attemptsexceeded = 1;
}


//Start Embed Recorder
$token = \mod_cpassignment\utils::fetch_token($config->apiuser,
        $config->apisecret);

$audiorecid = constants::M_RECORDERID . '_' .
        constants::M_LIST_AUDIOREC;


//prepare audio feedback recorder, modal and trigger button
$timelimit=0;
$audiorecorderhtml = \mod_cpassignment\utils::fetch_recorder(
        $moduleinstance,$audiorecid, $token,
        constants::M_LIST_AUDIOREC,
        $timelimit,'audio','fresh');


//text boxes
$itemname='';
$itemid='';
$itemfilename ='';
$itemsubid =0;
$itemformhtml = $renderer->fetch_itemform($itemname,$itemid,$itemfilename, $itemsubid);

//recorder modal
$title = get_string('listrecaudiolabel',constants::M_LANG);
$content = $itemformhtml . $audiorecorderhtml;
$containertag = 'arec_container';
$amodalcontainer = $renderer->fetch_modalcontainer($title,$content,$containertag);

//download modal
$title = get_string('listrecdownloadlabel',constants::M_LANG);
$downloadformhtml = $renderer->fetch_downloadform();
$content = $downloadformhtml ;
$containertag = 'download_container';
$dmodalcontainer = $renderer->fetch_modalcontainer($title,$content,$containertag);

//share modal
$title = get_string('listsharelabel',constants::M_LANG);
$accesskey = utils::fetch_accesskey($moduleinstance->id);
$shareboxhtml = $renderer->fetch_sharebox($accesskey);
$content = $shareboxhtml ;
$containertag = 'sharebox_container';
$smodalcontainer = $renderer->fetch_modalcontainer($title,$content,$containertag);

$shareboxbutton = $renderer->js_trigger_button('listshareboxstart', true,
        get_string('listshareboxlabel',constants::M_LANG),'btn-success');
$arecorderbutton = $renderer->js_trigger_button('listaudiorecstart', true,
        get_string('listrecaudiolabel',constants::M_LANG), 'btn-primary');

$fullname = fullname($USER);

echo $shareboxbutton;
echo $renderer->show_list_top($fullname);
echo $arecorderbutton;
echo $amodalcontainer;
echo $dmodalcontainer;
echo $smodalcontainer;


//if we have items, show em. Data tables will make it pretty
$visible = false;
if($items) {
    $visible = true;
}
echo $renderer->show_list_items($items,$tableid,$visible );
echo $renderer->no_list_items(!$visible);

//this inits the js for the grading page
$opts=array('modulecssclass'=>constants::M_CLASS, 'cmid'=>$cm->id, 'moduleid'=>$moduleinstance->id,'authmode'=>'normal');
$PAGE->requires->js_call_amd("mod_cpassignment/listhelper", 'init', array($opts));



// Finish the page.
echo $renderer->footer();
