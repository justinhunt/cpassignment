<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/06/26
 * Time: 13:16
 */

namespace mod_cpassignment\output;

use \mod_cpassignment\constants;
use \mod_cpassignment\utils;
use renderable;
use renderer_base;
use templatable;

class renderer extends \plugin_renderer_base implements templatable, renderable {

    /**
     * Returns the header for the module
     *
     * @param mod $instance
     * @param string $currenttab current tab that is shown.
     * @param int    $item id of the anything that needs to be displayed.
     * @param string $extrapagetitle String to append to the page title.
     * @return string
     */
    public function header($moduleinstance, $cm, $currenttab = '', $itemid = null, $extrapagetitle = null) {
        global $CFG;

        $activityname = format_string($moduleinstance->name, true, $moduleinstance->course);
        if (empty($extrapagetitle)) {
            $title = $this->page->course->shortname.": ".$activityname;
        } else {
            $title = $this->page->course->shortname.": ".$activityname.": ".$extrapagetitle;
        }

        // Build the buttons
        $context = \context_module::instance($cm->id);

        /// Header setup
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();

        if (has_capability('mod/cpassignment:manage', $context)) {
            //   $output .= $this->output->heading_with_help($activityname, 'overview', constants::M_LANG);

            if (!empty($currenttab)) {
                ob_start();
                include($CFG->dirroot.'/mod/cpassignment/tabs.php');
                $output .= ob_get_contents();
                ob_end_clean();
            }
        } else {
            $output .= $this->output->heading($activityname);
        }


        return $output;
    }

    /**
     * Return HTML to display limited header
     */
    public function notabsheader(){
        return $this->output->header();
    }

    /**
     *  Show a single button.
     */
    public function js_trigger_button($buttontag, $visible, $buttonlabel){

        $buttonclass =constants::M_CLASS  . '_' . $buttontag;
        $containerclass = $buttonclass . 'container';
        $button = \html_writer::link('#', $buttonlabel, array('class'=>'btn btn-primary ' . $buttonclass,'type'=>'button','id'=>$buttonclass));
        $visibleclass = '';
        if(!$visible){$visibleclass = 'hide';}
        $ret = \html_writer::div($button, $containerclass . ' ' .  $visibleclass);
        return $ret;
    }

    /**
     *  Show instructions/instructions
     */
    public function show_instructions($moduleinstance, $showtext, $status) {
        $thetitle =  $this->output->heading($moduleinstance->name, 3, 'main');
        $displaytext =  \html_writer::div($thetitle,
                constants::M_CLASS  . '_center');
        $displaytext .= $this->output->box_start();

        // Show the text according to the layout in the editor.
        $displaytext .= \html_writer::div($showtext);

        // Add html string to show where we are in the activity.
        // Also will contain buttons.
        $displaytext .= '<br>' . $status . '<br>';

        $displaytext .= $this->output->box_end();

        $ret = \html_writer::div($displaytext,
                constants::M_INSTRUCTIONS_CONTAINER,
                array('id'=>constants::M_INSTRUCTIONS_CONTAINER));

        return $ret;
    }

    /**
     *  An upload successmessage displayed in modal
     */
    public function show_uploadsuccess($moduleinstance) {
        $title = '';
        $content=get_string('uploadsuccessmessage',constants::M_LANG);
        $modalcontent = utils::fetch_modal_content($title,$content);
        $modal= utils::fetch_modal_container($modalcontent,'uploadsuccess');
        return $modal;
    }

    /**
     *  General purpose cancel button, returns to activity top page.
     */
    public function cancelbutton($cm) {

        $button = $this->output->single_button(
                new \moodle_url('/mod/cpassignment/view.php',
                array('id' => $cm->id)),
                get_string('cancel'));
        $ret = \html_writer::div($button, constants::M_CLASS  . '_cancelbutton');
        return $ret;
    }

    /**
     * Show the introduction text is as set in the activity description
     */
    public function show_intro($themodule,$cm){
        $ret = "";
        if (trim(strip_tags($themodule->intro))) {
            $ret .= $this->output->box_start('mod_introbox');
            $ret .= format_module_intro('cpassignment', $themodule, $cm->id);
            $ret .= $this->output->box_end();
        }
        return $ret;
    }

    /**
     * Show the reading passage
     */
    public function show_passage($themodule,$cm){

        $ret = "";
        $ret .= \html_writer::div( $themodule->passage ,constants::M_PASSAGE_CONTAINER,
            array('id'=>constants::M_PASSAGE_CONTAINER));
        return $ret;
    }

    /**
     * Show the completion message in the activity settings
     */
    public function show_finished($themodule, $showtext) {
        $thetitle =  $this->output->heading($themodule->name, 3, 'main');
        $displaytext =  \html_writer::div($thetitle,
                constants::M_CLASS  . '_center');
        $displaytext .= $this->output->box_start();
        $displaytext .=  \html_writer::div($showtext,
                 constants::M_CLASS  . '_center');

        // Add a button for user to nav back to view.
        $displaytext .= $this->output->single_button(new \moodle_url(constants::M_URL .
                '/view.php', array('n' => $themodule->id)),
                get_string('attempt_completed', constants::M_LANG));

        $displaytext .= $this->output->box_end();

        $ret = \html_writer::div($displaytext,constants::M_FINISHED_CONTAINER,array('id'=>constants::M_FINISHED_CONTAINER));

        return $ret;
    }

    /**
     * Show error (but when?)
     */
    public function show_error($themodule, $cm){
        $displaytext = $this->output->box_start();
        $displaytext .= $this->output->heading(get_string('errorheader',constants::M_LANG), 3, 'main');
        $displaytext .=  \html_writer::div(get_string('uploadconverterror',constants::M_LANG),'',array());
        $displaytext .= $this->output->box_end();
        $ret= \html_writer::div($displaytext,constants::M_ERROR_CONTAINER,array('id'=>constants::M_ERROR_CONTAINER));
        return $ret;
    }

    /**
     * The html part of the recorder (js is in the fetch_activity_amd)
     */
    public function show_recorder($moduleinstance, $token){
        $updatecontrol = constants::M_UPDATE_CONTROL;
        $timelimit = $moduleinstance->timelimit;
        $mediatype= $moduleinstance->mediatype;
        $recordertype= $moduleinstance->recordertype;
        $recorderid= constants::M_RECORDERID;

        $recorderdiv= \mod_cpassignment\utils::fetch_recorder($moduleinstance, $recorderid, $token, $updatecontrol,$timelimit,$mediatype,$recordertype);
        $containerdiv= \html_writer::div($recorderdiv,constants::M_RECORDER_CONTAINER . " " . constants::M_CLASS  . '_center',
            array('id'=>constants::M_RECORDER_CONTAINER));
        $recordingdiv = \html_writer::div($containerdiv ,constants::M_RECORDING_CONTAINER);

        //prepare output
        $ret =$recordingdiv;
        //return it
        return $ret;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

       // I'm not sure how to use this function.
    }

    public function fetch_attempts($attempts, $username, $graded) {

        $table = new \stdClass();
        $table->classes = constants::M_GRADING_ATTEMPT_CONTAINER;
        $table->caption = get_string('submitbyuserheading', constants::M_LANG, $username);
        $table->tableheaders = array(
                get_string('id', constants::M_LANG),
                get_string('mediafile', constants::M_LANG),
                get_string('status', constants::M_LANG),
                get_string('timecreated', constants::M_LANG),
                get_string('action', constants::M_LANG));

        $table->tabledata = array();

        foreach ($attempts as $attempt) {
            $data = array();
            $data[] = $attempt->id;
            $data[] = 'mediafile';
            switch($attempt->status) {

                case constants::M_SUBMITSTATUS_SELECTED:
                    $status = get_string('submitted', constants::M_LANG);
                    break;

                case constants::M_SUBMITSTATUS_GRADED:
                    $status = get_string('graded', constants::M_LANG);
                    break;

                default : $status = ' - ';

            }
            $data[] = $status;
            $data[] = $attempt->timecreated;
            if (!$graded) {
                $url = new \moodle_url(constants::M_URL . '/manageattempts.php',
                        array('action' => 'submitbyuser', 'n' => $attempt->cpassignmentid,
                        'attemptid' => $attempt->id));
                        $btn = new \single_button($url, get_string('submit'), 'post');
                        $btn->add_confirm_action(get_string('submitbyuserconfirm',
                            constants::M_LANG));
                $data[] = $this->output->render($btn);
            } else {
                $data[] = get_string('alreadygraded', constants::M_LANG);
            }
            $table->tabledata[] = $data;
        }

        return $this->render_from_template('mod_cpassignment/attempts', $table);

    }

    public function fetch_attempts_old($moduleinstance, $modulecontext, $userid) {
        $reportrenderer = $this->page->get_renderer(constants::M_FRANKY,'report');
        $submissionrenderer = $this->page->get_renderer(constants::M_FRANKY,'submission');
        $report = new \mod_cpassignment\report\submitbyuser();
        //formdata should only have simple values, not objects
        //later it gets turned into urls for the export buttons
        $formdata = new \stdClass();
        $formdata->cpassignmentid = $moduleinstance->id;
        $formdata->userid = $userid;
        $formdata->modulecontextid = $modulecontext->id;
        $formdata->returnpage = 'view';

        //if we got to here we are loading the report on screen
        //so we need our audio player loaded
        //here we set up any info we need to pass into javascript
        $aph_opts =Array();
        $aph_opts['mediatype'] = $moduleinstance->mediatype;
        $aph_opts['hiddenplayerclass'] = constants::M_HIDDEN_PLAYER;
        $aph_opts['hiddenplayerbuttonclass'] = constants::M_HIDDEN_PLAYER_BUTTON;
        $aph_opts['hiddenplayerbuttonactiveclass'] =
                constants::M_HIDDEN_PLAYER_BUTTON_ACTIVE;
        $aph_opts['hiddenplayerbuttonplayingclass'] =
                constants::M_HIDDEN_PLAYER_BUTTON_PLAYING;
        $aph_opts['hiddenplayerbuttonpausedclass'] =
                constants::M_HIDDEN_PLAYER_BUTTON_PAUSED;

        $report->process_raw_data($formdata, $moduleinstance);
        $reportheading = $report->fetch_formatted_heading();

        $reportrows = $report->fetch_formatted_rows();
        $allrowscount = $report->fetch_all_rows_count();

        switch($moduleinstance->mediatype)
        {
            case 'video':
                echo $submissionrenderer->render_hiddenvideoplayer();
                break;
            case 'audio':
            default:
                echo $submissionrenderer->render_hiddenaudioplayer();
        }

        echo $reportrenderer->render_section_html($reportheading, $report->fetch_name(),
                $report->fetch_head(), $reportrows, $report->fetch_fields());

        //prepare JS for the grading.php page, mainly hidden audio recorder
        $this->page->requires->js_call_amd("mod_cpassignment/gradinghelper", 'init',
                array($aph_opts));
    }

    function fetch_activity_amd($cm, $moduleinstance, $pagemode='summary'){
        global $USER;
        //any html we want to return to be sent to the page
        $ret_html = '';

        //here we set up any info we need to pass into javascript

        $recopts =Array();
        //recorder html ids
        $recopts['recorderid'] = constants::M_RECORDERID;
        $recopts['recordingcontainer'] = constants::M_RECORDING_CONTAINER;
        $recopts['recordercontainer'] = constants::M_RECORDER_CONTAINER;

        //activity html ids
        //$recopts['passagecontainer'] = constants::M_PASSAGE_CONTAINER;
        $recopts['instructionscontainer'] = constants::M_INSTRUCTIONS_CONTAINER;
        $recopts['finishedcontainer'] = constants::M_FINISHED_CONTAINER;
        $recopts['pagemode']=$pagemode;
        $recopts['moduleclass']=constants::M_CLASS;



        //we need an update control tp hold the recorded filename,
        $ret_html = $ret_html . \html_writer::tag('input', '', array('id' => constants::M_UPDATE_CONTROL, 'type' => 'hidden'));


        //this inits the M.mod_cpassignment thingy, after the page has loaded.
        //we put the opts in html on the page because moodle/AMD doesn't like lots of opts in js
        //convert opts to json
        $jsonstring = json_encode($recopts);
        $widgetid = constants::M_RECORDERID . '_opts_9999';
        $opts_html = \html_writer::tag('input', '', array('id' => 'amdopts_' . $widgetid, 'type' => 'hidden', 'value' => $jsonstring));

        //the recorder div
        $ret_html = $ret_html . $opts_html;

        $opts=array('cmid'=>$cm->id,'widgetid'=>$widgetid);
        $this->page->requires->js_call_amd("mod_cpassignment/activitycontroller", 'init', array($opts));
        $this->page->requires->strings_for_js(array('gotnosound','done','beginreading'),constants::M_LANG);

        //these need to be returned and echo'ed to the page
        return $ret_html;
    }

}