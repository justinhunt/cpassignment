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
 * This is the guest instance of student recording page
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
use \mod_cpassignment\utils;

$accesskey = required_param('k', PARAM_TEXT); // Course_module ID, or
$accessinfo = utils::get_accessinfo_by_accesskey($accesskey);

if ($accessinfo) {
    $moduleinstance  = $DB->get_record('cpassignment', array('id' => $accessinfo->cpassignmentid), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('cpassignment', $moduleinstance->id, $course->id, false, MUST_EXIST);
    $owner = $DB->get_record('user',array('id'=>$accessinfo->userid));
} else {
    print_error(0,'The URL that you are using is either old or incorrect.');
}

$PAGE->set_url('/mod/cpassignment/k.php', array('k' => $accesskey));
//require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);
$PAGE->set_context($modulecontext);

//are we a teacher or a student?
$mode= "view";


// Set up the page header.
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));

$PAGE->set_pagelayout('embedded');


// Get an admin settings.
$config = get_config(constants::M_COMP);

// Get our renderers.
$renderer = $PAGE->get_renderer(constants::M_COMP);

// Show our header
 echo $renderer->notabsheader($moduleinstance, $cm, $mode, null, get_string('view',
        constants::M_LANG));


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

$arecorderbutton = $renderer->js_trigger_button('listaudiorecstart', true,
        get_string('listrecaudiolabel',constants::M_LANG), 'btn-primary');


$fullname = fullname($owner);


echo $renderer->show_unauth_top($fullname,$moduleinstance->name);
echo $arecorderbutton;
echo $amodalcontainer;

//acknowledge receipts container (only for unauth users with access key)
echo $renderer->fetch_receipts_container();




//this inits the js for the grading page
//authmode = guest or normal
$opts=array('modulecssclass'=>constants::M_CLASS, 'cmid'=>$cm->id, 'moduleid'=>$moduleinstance->id, 'authmode'=>'guest', 'accesskey'=>$accesskey);
$PAGE->requires->js_call_amd("mod_cpassignment/listhelper", 'init', array($opts));



// Finish the page.
echo $renderer->footer();
