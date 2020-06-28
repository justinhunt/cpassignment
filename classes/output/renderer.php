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

class renderer extends \plugin_renderer_base implements renderable {

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
        if (!empty($extrapagetitle)) {
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

        }

        return $output;
    }

    /**
     * Returns the header for the module
     *
     * @param mod $instance
     * @param string $currenttab current tab that is shown.
     * @param int    $item id of the anything that needs to be displayed.
     * @param string $extrapagetitle String to append to the page title.
     * @return string
     */
    public function notabsheader($moduleinstance, $cm, $currenttab = '', $itemid = null, $extrapagetitle = null) {
        global $CFG;

        $activityname = format_string($moduleinstance->name, true, $moduleinstance->course);
        if (!empty($extrapagetitle)) {
            $title = $this->page->course->shortname.": ".$activityname.": ".$extrapagetitle;
        }

        // Build the buttons
        $context = \context_module::instance($cm->id);

        /// Header setup
        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();
        return $output;
    }



    /**
     *  Show a single button.
     */
    public function js_trigger_button($buttontag, $visible, $buttonlabel, $bootstrapclass='btn-primary'){

        $buttonclass =constants::M_CLASS  . '_' . $buttontag;
        $containerclass = $buttonclass . 'container';
        $button = \html_writer::link('#', $buttonlabel, array('class'=>'btn ' . $bootstrapclass . ' ' . $buttonclass,'type'=>'button','id'=>$buttonclass));
        $visibleclass = '';
        if(!$visible){$visibleclass = 'hide';}
        $ret = \html_writer::div($button, $containerclass . ' ' .  $visibleclass);
        return $ret;
    }

    /**
     *  Show instructions/instructions
     */
    public function show_instructions($moduleinstance, $showtext) {

        $displaytext = $this->output->box_start();

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
        $modal= $this->fetch_modalcontainer($title, $content,'uploadsuccess');
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
     * Show the introduction text is as set in the activity description
     */
    public function why_cannot_attempt($reason){
       $ret = \html_writer::div($reason, constants::M_CLASS  . '_cannot_attempt');
       return $ret;
    }

    /**
     * Show the grade, or why there is no grade
     */
    public function dont_show_grade($message){
        $ret = \html_writer::div($message, constants::M_CLASS  . '_grade_or_message');
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

    public function fetch_attempts($moduleinstance, $modulecontext, $userid) {

        $submissionrenderer = $this->page->get_renderer(constants::M_COMP,'submission');

        //get attempts (with cells formatted)
        $report = new \mod_cpassignment\report\attempts();
        //formdata should only have simple values, not objects
        //later it gets turned into urls for the export buttons
        $formdata = new \stdClass();
        $formdata->cpassignmentid = $moduleinstance->id;
        $formdata->userid = $userid;
        $formdata->modulecontextid = $modulecontext->id;
        $report->process_raw_data($formdata, $moduleinstance);
        $tabledata = new \stdClass();
        $tabledata->classes = constants::M_GRADING_MYATTEMPTS_CONTAINER;
        $tabledata->heading = get_string('myattempts',constants::M_LANG);
        $tabledata->headfields = $report->fetch_head();
        $tabledata->rows = $report->fetch_formatted_rows(false,false);

        //load data into template
        $thehtml = $this->render_from_template('mod_cpassignment/attempts', $tabledata);

        //look for submitted attempt
        $selectedattempt = false;
        $selected = get_string('submitted', constants::M_LANG);
        foreach($tabledata->rows as $therow){
            if($therow->status ==$selected){
                $selectedattempt= $therow->id;
                break;
            }
        }

        //prepare JS for the grading.php page, mainly hidden audio recorder
        $osp_opts = array();
        $osp_opts['selectedattempt'] = $selectedattempt;
        $this->page->requires->js_call_amd("mod_cpassignment/onesubmissionplayer", 'init',
            array($osp_opts));
        $this->page->requires->strings_for_js(array('submitted'),constants::M_LANG);

        return $thehtml;

    }


    function fetch_activity_amd($cm, $moduleinstance, $pagemode='summary',$selectedattemptid,$graded){
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
        $recopts['selectedattempt']=$selectedattemptid;
        $recopts['graded']=$graded;



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

    function setup_datatables($tableid){
        global $USER;

        $tableprops = array();
        $columns = array();
        //for cols .. .'itemname', 'itemtype', 'itemtags','timemodified', 'edit','delete'
        $columns[0]=null;
        $columns[1]=null;
        $columns[2]=null;
        $columns[3]=null;
        $columns[4]=array('orderable'=>false);
        $columns[5]=array('orderable'=>false);
        $tableprops['columns']=$columns;

        //default ordering
        $order = array();
        $order[0] =array(3, "desc");
        $tableprops['order']=$order;

        //here we set up any info we need to pass into javascript
        $opts =Array();
        $opts['tableid']=$tableid;
        $opts['tableprops']=$tableprops;
        $this->page->requires->js_call_amd("mod_cpassignment/datatables", 'init', array($opts));
        $this->page->requires->css( new \moodle_url('https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'));
    }

    /*
     * This is the top level function that assembles elements of the view page and displays them
     * It is called from view.php
     */
    function display_view_page($moduleinstance, $cm, $config,$modulecontext, $submissionrenderer, $attempts) {
        global $USER;

        // Show our header
        $mode= "view";
        echo $this->header($moduleinstance, $cm, $mode, null, get_string('view',
                constants::M_LANG));

        // How many allowed?
        $max = $moduleinstance->maxattempts;
        $attemptsexceeded = 0;
        $attemptcount = count($attempts);

        if ( ($max != 0)  && $attempts && ($attemptcount >= $max) ) {
            $attemptsexceeded = 1;
        }

        // Has it been graded yet?
        $graded = false;
        $gradedattemptid = 0;
        $selectedattemptid = 0;
        foreach ($attempts as $attempt) {
            switch($attempt->status){
                case constants::M_SUBMITSTATUS_GRADED:
                    $graded = true;
                    $gradedattemptid = $attempt->id;
                    $selectedattemptid = $attempt->id;
                    break;
                case constants::M_SUBMITSTATUS_SELECTED:
                    $selectedattemptid = $attempt->id;
                    break;
            }
        }

        // Status content is added to instructions.
        $status = '';

        // Is this a retake?
        if ($attemptcount > 0) {

            // Show a list of attempt data and status here.  Allow user to submit
            // until graded.
            echo $this->fetch_attempts($moduleinstance, $modulecontext, $USER->id);

            // Grade information.
            if ($graded) {
                //We probably do not need this. Until we have a flashy submission/transcript/text reader widget
                // $submission->prepare_javascript($reviewmode);
                // We don't show any grading information if dis-allowed in settings.
                $submission = new \mod_cpassignment\submission($gradedattemptid,
                        $modulecontext->id);
                $status .= $submissionrenderer->render_submission($submission,
                        $moduleinstance->showgrade);

                if (!$moduleinstance->showgrade) {
                    $status .= $this->dont_show_grade(get_string('gradeunavailable', constants::M_LANG));
                }
            } else {
                $status .= $this->dont_show_grade(get_string("notgradedyet",constants::M_LANG));
            }

            // Try again button, if applicable.
            if ( (!$attemptsexceeded) && (!$graded) ) {
                $status .= $this->js_trigger_button('startbutton', false,
                        get_string('reattempt', constants::M_LANG));
            }else{

                if($graded){
                    $reason = get_string('alreadygraded', constants::M_LANG,
                            $moduleinstance->maxattempts);
                }else if($attemptsexceeded){
                    $reason =get_string('exceededattempts', constants::M_LANG,
                            $moduleinstance->maxattempts);
                }else{
                    $reason =  get_string('otherreason', constants::M_LANG);
                }
                $status .= $this->why_cannot_attempt($reason);
            }
        } else { // numattempts = 0. TOP page.
            $status .= $this->js_trigger_button('startbutton',false,
                    get_string('firstattempt', constants::M_LANG));
        }


        // Fetch token.
        $token = \mod_cpassignment\utils::fetch_token($config->apiuser,
                $config->apisecret);

        // Process plugin files for standard editor component.
        $instructions = file_rewrite_pluginfile_urls($moduleinstance->instructions,
                'pluginfile.php', $modulecontext->id, constants::M_COMP,
                constants::M_FILEAREA_INSTRUCTIONS, 0);
        $instructions = format_text($instructions);
        $finished = file_rewrite_pluginfile_urls($moduleinstance->finished,
                'pluginfile.php', $modulecontext->id, constants::M_COMP,
                constants::M_FILEAREA_FINISHED, 0);
        $finished = format_text($finished);

        // Show all the main parts. Many will be hidden and displayed by JS.
        echo $this->show_instructions($moduleinstance, $instructions);
        echo $this->show_finished($moduleinstance, $finished);
        echo $this->show_error($moduleinstance,$cm);
        echo $this->show_recorder($moduleinstance, $token);
        echo $this->show_uploadsuccess($moduleinstance);
        //echo $this->cancelbutton($cm);

        // The module AMD code.
        $pagemode="summary";
        echo $this->fetch_activity_amd($cm, $moduleinstance, $pagemode,$selectedattemptid,$graded);

        // Finish the page.
        echo $this->footer();


    }

    /*
     * This is the top level function that assembles elements of the list page and displays them
     * Called from view.php
     */
    function display_list_page($moduleinstance, $cm, $config, $items){
        global $USER;
        //Prepare datatable(before header printed)
        $tableid = '' . constants::M_CLASS_ITEMTABLE . '_' . '_opts_9999';
        $this->setup_datatables($tableid);

        // Show our header
        $mode= "view";
        echo $this->notabsheader($moduleinstance, $cm, $mode, null, get_string('view',
                constants::M_LANG));


        // How many allowed?
        $max = $moduleinstance->maxattempts;
        $attemptsexceeded = 0;

        if ( ($max != 0)  && $items && (count($items) >= $max) ) {
            $attemptsexceeded = 1;
        }
        //do something with attempts exceeded here
        //but what?


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
        $itemformhtml = $this->fetch_itemform($itemname,$itemid,$itemfilename, $itemsubid);

        //recorder modal
        $title = get_string('listrecaudiolabel',constants::M_LANG);
        $content = $itemformhtml . $audiorecorderhtml;
        $containertag = 'arec_container';
        $amodalcontainer = $this->fetch_modalcontainer($title,$content,$containertag);

        //download modal
        $title = get_string('listrecdownloadlabel',constants::M_LANG);
        $downloadformhtml = $this->fetch_downloadform();
        $content = $downloadformhtml ;
        $containertag = 'download_container';
        $dmodalcontainer = $this->fetch_modalcontainer($title,$content,$containertag);

        //share modal
        $title = get_string('listsharelabel',constants::M_LANG);
        $accesskey = utils::fetch_accesskey($moduleinstance->id);
        $shareboxhtml = $this->fetch_sharebox($accesskey);
        $content = $shareboxhtml ;
        $containertag = 'sharebox_container';
        $smodalcontainer = $this->fetch_modalcontainer($title,$content,$containertag);

        $shareboxbutton = $this->js_trigger_button('listshareboxstart', true,
                get_string('listshareboxlabel',constants::M_LANG),'btn-success');
        $arecorderbutton = $this->js_trigger_button('listaudiorecstart', true,
                get_string('listrecaudiolabel',constants::M_LANG), 'btn-primary');

        $fullname = fullname($USER);

        echo $shareboxbutton;
        echo $this->show_list_top($fullname, $moduleinstance->name);
        echo $arecorderbutton;
        echo $amodalcontainer;
        echo $dmodalcontainer;
        echo $smodalcontainer;


        //if we have items, show em. Data tables will make it pretty
        $visible = false;
        if($items) {
            $visible = true;
        }
        echo $this->show_list_items($items,$tableid,$visible );
        echo $this->no_list_items(!$visible);

        //this inits the js for the grading page
        $opts=array('modulecssclass'=>constants::M_CLASS, 'cmid'=>$cm->id, 'moduleid'=>$moduleinstance->id,'authmode'=>'normal');
        $this->page->requires->js_call_amd("mod_cpassignment/listhelper", 'init', array($opts));
        

        // Finish the page.
        echo $this->footer();

    }

    /**
     * Return the html table of items
     * @param array homework objects
     * @param integer $courseid
     * @return string html of table
     */
    function show_list_items($items,$tableid,$visible){

        $data = [];
        $data['display'] = $visible ? 'block' : 'none';
        $data['tableid']=$tableid;
        $data['items']=[];
        //loop through the items,massage data and add to table
        //itemname itemid,filename,itemdate, id
        $currentitem=0;
        foreach ($items as $item) {
            $ditem=[];
            //item name
            //need to nest this in array to get the data to the template
            $itemname_tmpl = new \mod_cpassignment\output\itemname($item);
            $ditem['itemnames'] =  [$itemname_tmpl->export_for_template($this)];
            //$ditem['itemname']= $item->{constants::LIST_ITEM_NAME};

            //item id
            //need to nest this in array to get the data to the template
            $itemid_tmpl = new \mod_cpassignment\output\itemid($item);
            $ditem['itemids'] =  [$itemid_tmpl->export_for_template($this)];
            //$ditem['itemid'] = $item->{constants::LIST_ITEM_ID};

            //item date
            $ditem['itemdate'] = date("Y-m-d H:i:s",$item->timecreated);
            $ditem['filename']= $item->filename;
            $ditem['id']= $item->id;
            $data['items'][]=$ditem;

        }
        return $this->render_from_template('mod_cpassignment/itemtable', $data);
    }

    //this is the two textboxes
    function fetch_itemform($itemname,$itemid, $itemfilename,$itemsubid){
        $data=[];
        $data['itemname']=$itemname;
        $data['itemid']=$itemid;
        $data['itemfilename']=$itemfilename;
        $data['itemsubid']=$itemsubid;
        return $this->render_from_template('mod_cpassignment/itemform', $data);
    }

    //fetch modal content
    function fetch_modalcontent($title,$content){
        $data=[];
        $data['title']=$title;
        $data['content']=$content;
        return $this->render_from_template('mod_cpassignment/modalcontent', $data);
    }

    //fetch modal container
    function fetch_modalcontainer($title,$content,$containertag){
        $data=[];
        $data['title']=$title;
        $data['content']=$content;
        $data['containertag']=$containertag;
        return $this->render_from_template('mod_cpassignment/modalcontainer', $data);
    }


    //fetch downloadform
    function fetch_downloadform(){
        $data=[];
        return $this->render_from_template('mod_cpassignment/downloadform', $data);
    }

    //fetch sharebox
    function fetch_sharebox($accesskey){
        global $CFG;
        $data=[];
        $data['publiclink'] = $CFG->wwwroot . '/mod/cpassignment/k.php?k=' . $accesskey;
        return $this->render_from_template('mod_cpassignment/sharebox', $data);
    }

    /**
     * No items, thats too bad
     */
    public function no_list_items($visible){
        $data=[];
        $data['display'] = $visible ? 'block' : 'none';
        return $this->render_from_template('mod_cpassignment/noitemscontainer', $data);
    }

    /**
     * No items, thats too bad
     */
    public function fetch_receipts_container(){
        $data=[];
        return $this->render_from_template('mod_cpassignment/receipts', $data);
    }


    /**
     * Show list top
     */
    public function show_list_top($fullname, $modname){
        $moddetails = new \stdClass();
        $moddetails->fullname = $fullname;
        $moddetails->modname = $modname;
        $displaytext = $this->output->box_start('mod_cpassignment_allcenter center');
        $displaytext .= $this->output->heading(get_string('listtop',constants::M_LANG,$moddetails), 3, 'main center');
        $displaytext .=  \html_writer::div(get_string('listtopdetails',constants::M_LANG),'center',array());
        $displaytext .= $this->output->box_end();
        $ret= \html_writer::div($displaytext,constants::M_LISTTOP_CONTAINER,array('id'=>constants::M_LISTTOP_CONTAINER));
        return $ret;
    }

    /**
     * Show list top
     */
    public function show_unauth_top($fullname){
        $displaytext = $this->output->box_start('mod_cpassignment_allcenter center');
        $displaytext .= $this->output->heading(get_string('listtop',constants::M_LANG,$fullname), 3, 'main center');
        $displaytext .=  \html_writer::div(get_string('unauthtopdetails',constants::M_LANG),'center',array());
        $displaytext .= $this->output->box_end();
        $ret= \html_writer::div($displaytext,constants::M_LISTTOP_CONTAINER,array('id'=>constants::M_LISTTOP_CONTAINER));
        return $ret;
    }


}