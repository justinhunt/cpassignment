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
 * Reports for cpassignment
 *
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

use \mod_cpassignment\constants;
use \mod_cpassignment\utils;
use \mod_cpassignment\submission;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // cpassignment instance ID
$format = optional_param('format', 'datatables', PARAM_TEXT); //export format csv or html
$action = optional_param('action', 'grading', PARAM_TEXT); // report type
$userid = optional_param('userid', 0, PARAM_INT); // user id
$attemptid = optional_param('attemptid', 0, PARAM_INT); // attemptid

//paging details
$paging = new stdClass();
$paging->perpage = optional_param('perpage',-1, PARAM_INT);
$paging->pageno = optional_param('pageno',0, PARAM_INT);
$paging->sort  = optional_param('sort','iddsc', PARAM_TEXT);


if ($id) {
    $cm         = get_coursemodule_from_id(constants::M_MODNAME, $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance  = $DB->get_record(constants::M_TABLE, array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $moduleinstance  = $DB->get_record(constants::M_TABLE, array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance(constants::M_TABLE, $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$PAGE->set_url(constants::M_URL . '/grading.php',
	array('id' => $cm->id,'format'=>$format,'action'=>$action,'userid'=>$userid,'attemptid'=>$attemptid));
require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);


//Get an admin settings
$config = get_config(constants::M_COMP);

//set per page according to admin setting
if($paging->perpage==-1){
	$paging->perpage = $config->itemsperpage;
}

//Diverge logging logic at Moodle 2.7
if($CFG->version<2014051200){
	add_to_log($course->id, constants::M_MODNAME, 'reports', "reports.php?id={$cm->id}", $moduleinstance->name, $cm->id);
}else{
	// Trigger module viewed event.
	$event = \mod_cpassignment\event\course_module_viewed::create(array(
	   'objectid' => $moduleinstance->id,
	   'context' => $modulecontext
	));
	$event->add_record_snapshot('course_modules', $cm);
	$event->add_record_snapshot('course', $course);
	$event->add_record_snapshot(constants::M_MODNAME, $moduleinstance);
	$event->trigger();
}

//process form submission
switch($action){
	case 'gradenowsubmit':
		$mform = new \mod_cpassignment\gradenowform();
		if ($mform->is_cancelled()) {
			$action = 'grading';
			break;
		} else {
			$data = $mform->get_data();

            if (!empty($data->btn_fbaudio)) {
                // Do something with audio
                $title = get_string('audio',constants::M_LANG);
                $content = 'audio button clicked';
                echo $renderer->fetch_modalcontent($title,$content);
            }

            if (!empty($data->btn_fbvideo)) {
                $title = get_string('video',constants::M_LANG);
                $content = 'video button clicked';
                echo $renderer->fetch_modalcontent($title,$content);
            }

			$submission = new \mod_cpassignment\submission($attemptid,
                $cm->id);
			$submission->update($data);

			//update gradebook
			cpassignment_update_grades($moduleinstance, $submission->fetch('userid'));

			//move on or return to grading
			if(property_exists($data,'submit2')){
				$attemptid = $submission->get_next_ungraded_id();
				if($attemptid){
					$action='gradenow';
				}else{
					$action='grading';
				}
			}else{
				$action='grading';
			}
		}
		break;
}

// Set up the page header.
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->requires->jquery();


//This puts all our display logic into the renderer.php files in this plugin
$renderer = $PAGE->get_renderer(constants::M_COMP);
$reportrenderer = $PAGE->get_renderer(constants::M_COMP,'report');
$submissionrenderer = $PAGE->get_renderer(constants::M_COMP,'submission');

//From here we actually display the page.
$mode = "grading";
$extraheader="";
switch ($action){

	case 'gradenow':

		$submission = new \mod_cpassignment\submission($attemptid, $modulecontext->id);
        //this inits the js for the grading page
        $opts=array('modulecssclass'=>constants::M_CLASS);
        $PAGE->requires->js_call_amd("mod_cpassignment/gradenowhelper", 'init', array($opts));

        //load data
		$data=array(
			'action'=>'gradenowsubmit',
			'attemptid'=>$attemptid,
			'n'=>$moduleinstance->id,
            'feedbackaudio'=>$submission->fetch('feedbackaudio'),
            'feedbackvideo'=>$submission->fetch('feedbackvideo'),
			'feedbacktext'=>$submission->fetch('feedbacktext'),
            'feedbacktextformat'=>$submission->fetch('feedbacktextformat'),
			'sessiontime'=>$submission->fetch('sessiontime'),
			'sessionscore'=>$submission->fetch('sessionscore'));

        // get next id, not required, we have only one now.
        // $nextid = $submission->get_next_ungraded_id();

        // Fetch recorders
        $token = \mod_cpassignment\utils::fetch_token($config->apiuser,$config->apisecret);

        $timelimit = 0;

        // Check selected feedback options has been disabled.
        // To enable, add code back here and pass params to gradenow form (below).

        $audiorecid = constants::M_RECORDERID . '_' .
                constants::M_GRADING_FORM_FEEDBACKAUDIO;

        $videorecid = constants::M_RECORDERID . '_' .
            constants::M_GRADING_FORM_FEEDBACKVIDEO;

        //prepare audio feedback recorder, modal and trigger button
        $audiorecorderhtml = \mod_cpassignment\utils::fetch_recorder(
                $moduleinstance,$audiorecid, $token,
                constants::M_GRADING_FORM_FEEDBACKAUDIO,
                $timelimit,'audio','bmr');

        $title = get_string('feedbackaudiolabel',constants::M_LANG);
        $content = $audiorecorderhtml;
        $amodalcontainer = $renderer->fetch_modalcontainer($title,$content, 'arec_feedback_container');
        $afeedbackbutton = $renderer->js_trigger_button('afeedbackstart', true, get_string('feedbackaudiolabel',constants::M_LANG));

        //prepare video feedback recorder, modal and trigger button
        $videorecorderhtml = \mod_cpassignment\utils::fetch_recorder(
                $moduleinstance,$videorecid, $token,
                constants::M_GRADING_FORM_FEEDBACKVIDEO,
                $timelimit,'video','bmr');

        $title = get_string('feedbackvideolabel',constants::M_LANG);
        $content = $videorecorderhtml;
        $vmodalcontainer = $renderer->fetch_modalcontainer($title,$content,$vmodalcontent,'vrec_feedback_container');
        $vfeedbackbutton = $renderer->js_trigger_button('vfeedbackstart',true, get_string('feedbackvideolabel',constants::M_LANG));

        // Create form.  No next any more.
		$gradenowform = new \mod_cpassignment\gradenowform(null,
                array(/*'shownext'=>$nextid !== */false,
                'context' => $modulecontext,'token' => $token,
                'audiorecorderhtml' => $amodalcontainer . $afeedbackbutton,
                'videorecorderhtml' => $vmodalcontainer .  $vfeedbackbutton,
                'maxgrade' => $moduleinstance->grade));

		// Prepare text editor.
		$edfileoptions = \mod_cpassignment\utils::editor_with_files_options($modulecontext);
        $editor = "feedbacktext";
        $data = file_prepare_standard_editor( (object)$data, $editor,
                $edfileoptions, $modulecontext, constants::M_COMP,
                $editor, $attemptid);

		$gradenowform->set_data($data);

		echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('grading', constants::M_LANG));

        echo $submissionrenderer->render_attempt_data($submission);
        //  Form will display recorders according to
        //  recorderhtml content.

        // Require mechansim to if recorder(s) should be displayed
        // or previous recordings should be shown for playback.

		$gradenowform->display();

		echo $renderer->footer();
		return;

	case 'grading':
		$report = new \mod_cpassignment\report\grading();
		//formdata should only have simple values, not objects
		//later it gets turned into urls for the export buttons
		$formdata = new stdClass();
		$formdata->cpassignmentid = $moduleinstance->id;
		$formdata->modulecontextid = $modulecontext->id;
        $formdata->returnpage = 'grading';
		break;

	case 'attempts':
		$report = new \mod_cpassignment\report\attempts();
		//formdata should only have simple values, not objects
		//later it gets turned into urls for the export buttons
		$formdata = new stdClass();
		$formdata->cpassignmentid = $moduleinstance->id;
		$formdata->userid = $userid;
		$formdata->modulecontextid = $modulecontext->id;
        $formdata->returnpage = 'view';
		break;

	default:
		echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('grading', constants::M_LANG));
		echo "unknown action.";
		echo $renderer->footer();
		return;
}

//if we got to here we are loading the report on screen
//so we need our audio player loaded
//here we set up any info we need to pass into javascript
$aph_opts =Array();
$aph_opts['mediatype'] = $moduleinstance->mediatype;
$aph_opts['hiddenplayerclass'] = constants::M_HIDDEN_PLAYER;
$aph_opts['hiddenplayerbuttonclass'] = constants::M_HIDDEN_PLAYER_BUTTON;
$aph_opts['hiddenplayerbuttonactiveclass'] =constants::M_HIDDEN_PLAYER_BUTTON_ACTIVE;
$aph_opts['hiddenplayerbuttonplayingclass'] =constants::M_HIDDEN_PLAYER_BUTTON_PLAYING;
$aph_opts['hiddenplayerbuttonpausedclass'] =constants::M_HIDDEN_PLAYER_BUTTON_PAUSED;

//prepare JS for the grading.php page, mainly hidden audio recorder
$PAGE->requires->js_call_amd("mod_cpassignment/hiddenplayer", 'init', array($aph_opts));


/*
1) load the class
2) call report->process_raw_data
3) call $rows=report->fetch_formatted_records($withlinks=true(html) false(print/excel))
5) call $reportrenderer->render_section_html($sectiontitle, $report->name, $report->get_head, $rows, $report->fields);
*/

$report->process_raw_data($formdata, $moduleinstance);
$reportheading = $report->fetch_formatted_heading();

switch($format){

	case 'html':


		$reportrows = $report->fetch_formatted_rows(true,$paging);
		$allrowscount = $report->fetch_all_rows_count();
		$pagingbar = $reportrenderer->show_paging_bar($allrowscount, $paging,$PAGE->url);
		echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('grading', constants::M_LANG));
        switch($moduleinstance->mediatype)
        {
            case 'video':
                echo $submissionrenderer->render_hiddenvideoplayer();
                break;
            case 'audio':
            default:
                echo $submissionrenderer->render_hiddenaudioplayer();
        }
		echo $extraheader;
		echo $pagingbar;
		echo $reportrenderer->render_section_html($reportheading, $report->fetch_name(), $report->fetch_head(), $reportrows, $report->fetch_fields());
		echo $pagingbar;
		echo $reportrenderer->show_grading_footer($moduleinstance, $cm, $formdata);
		echo $renderer->footer();
		break;


    case 'datatables':
    default:
        $tableid = \html_writer::random_id(constants::M_COMP);

        //apply data table, order by date desc
        $filtercolumn=false;
        $filterlabel=false;
        $order=array();
        $order[0] =array(1, "desc"); //lastdate desc
        $reportrenderer->setup_datatables($tableid,$filtercolumn, $filterlabel, $order);
        $paging = false;
        $reportrows = $report->fetch_formatted_rows(true,$paging);
        $allrowscount = $report->fetch_all_rows_count();
        echo $renderer->header($moduleinstance, $cm, $mode, null, get_string('grading', constants::M_LANG));
        switch($moduleinstance->mediatype)
        {
            case 'video':
                echo $submissionrenderer->render_hiddenvideoplayer();
                break;
            case 'audio':
            default:
                echo $submissionrenderer->render_hiddenaudioplayer();
        }
        echo $extraheader;
        echo $reportrenderer->render_table_for_datatables($tableid, $reportheading, $report->fetch_name(), $report->fetch_head(), $reportrows, $report->fetch_fields());
        echo $reportrenderer->show_grading_footer($moduleinstance, $cm, $formdata);
        echo $renderer->footer();


        break;
}