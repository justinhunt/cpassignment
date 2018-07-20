<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/06/26
 * Time: 13:16
 */

namespace mod_cpassignment\output;

use \mod_cpassignment\constants;

class renderer extends \plugin_renderer_base {

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
    public function reattemptbutton($moduleinstance, $buttonlabel){

        $button = $this->output->single_button(new \moodle_url(constants::M_URL . '/view.php',
            array('n'=>$moduleinstance->id,'retake'=>1)),
            $buttonlabel);

        $ret = \html_writer::div($button ,constants::M_CLASS  . '_afterattempt_cont');
        return $ret;
    }

    /**
     *
     */
    public function exceededattempts($moduleinstance){
        $message = get_string("exceededattempts",constants::M_LANG,$moduleinstance->maxattempts);
        $ret = \html_writer::div($message ,constants::M_CLASS  . '_afterattempt_cont');
        return $ret;

    }

    public function show_ungradedyet(){
        $message = get_string("notgradedyet",constants::M_LANG);
        $ret = \html_writer::div($message ,constants::M_CLASS  . '_ungraded_cont');
        return $ret;
    }

    /**
     *  Show instructions/instructions
     */
    public function show_instructions($moduleinstance, $showtext) {
        $thetitle =  $this->output->heading($moduleinstance->name, 3, 'main');
        $displaytext =  \html_writer::div($thetitle,
                constants::M_CLASS  . '_center');
        $displaytext .= $this->output->box_start();

        // Show the text according to the layout in the editor.
        $displaytext .= \html_writer::div($showtext);
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
        $modalcontent = $this->fetch_modal_content($title,$content);
        $modal_attributes = array('id'=>constants::M_CLASS  . '_uploadsuccess', 'role'=>'dialog','aria-hidden'=>'true','tab-index'=>'-1');
        $modal =  \html_writer::div($modalcontent, constants::M_CLASS  . '_uploadsuccess hidden modal fade',$modal_attributes);
        return $modal;
    }

    /**
     *  A template to hold the content of a modal
     */
    public function fetch_modal_content($title,$message) {

        $modalcontent=  '<div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">';
        $modalcontent .=  $title;
        $modalcontent .= '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">';
        $modalcontent .=  $message;
        $modalcontent .=  '</div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>';

        return $modalcontent;
    }

    /**
     *  General purpose cancel button, returns to course page.
     */
    public function cancelbutton($cm) {

        $button = $this->output->single_button(
                new \moodle_url('../../course/view.php',
                array('id' => $cm->course)),
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
     *  Show a progress circle overlay while uploading
     */
    public function show_progress($themodule,$cm){
        $hider =  \html_writer::div('',constants::M_HIDER,array('id'=>constants::M_HIDER));
        $message =  \html_writer::tag('h4',get_string('processing',constants::M_LANG),array());
        $spinner =  \html_writer::tag('i','',array('class'=>'fa fa-spinner fa-5x fa-spin'));
        $progressdiv = \html_writer::div($message . $spinner ,constants::M_PROGRESS_CONTAINER,
            array('id'=>constants::M_PROGRESS_CONTAINER));
        $ret = $hider . $progressdiv;
        return $ret;
    }

    /**
     * Show the completion message in the activity settings
     */
    public function show_completion($themodule, $cm, $showtext) {
        $thetitle =  $this->output->heading($themodule->name, 3, 'main');
        $displaytext =  \html_writer::div($thetitle,
                constants::M_CLASS  . '_center');
        $displaytext .= $this->output->box_start();
        $displaytext .=  \html_writer::div($showtext,
                 constants::M_CLASS  . '_center');
        $displaytext .= $this->output->box_end();

        // Add a button for user to nav back to view.
        // Can add a different label if required.
        $displaytext .= self::reattemptbutton($themodule,
            get_string('attempt_completed', constants::M_FRANKY));
        $ret = \html_writer::div($displaytext,constants::M_COMPLETION_CONTAINER,array('id'=>constants::M_COMPLETION_CONTAINER));

        return $ret;
    }

    /**
     * Show error (but when?)
     */
    public function show_error($themodule,$cm){
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
        $ret = "";
        $ret .=$recordingdiv;
        //return it
        return $ret;
    }


    function fetch_activity_amd($cm, $moduleinstance){
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
        $recopts['progresscontainer'] = constants::M_PROGRESS_CONTAINER;
        $recopts['completioncontainer'] = constants::M_COMPLETION_CONTAINER;
        $recopts['hider']=constants::M_HIDER;
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