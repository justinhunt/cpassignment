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

class submission_renderer extends \plugin_renderer_base {

    protected $submission=null;

    public function render_submission($submission) {

        $this->submission = $submission;
        $ret = $this->render_attempt_data($submission);
        $ret .= $this->render_attempt_grade($submission);
        $ret .= $this->render_current_feedback($submission);

        return $ret;
    }
    /**
     * Renders the attempt section of the grading page
     *
     */
    public function render_attempt_data ($submission) {

        $ret = \html_writer::start_div(
                constants::M_GRADING_ATTEMPTS_CONTAINER);

        $ret .= $this->output->heading(
                get_string('showingattempt',
                constants::M_LANG), 3);

        $ret .= '<p>' . get_string('attemptname', constants::M_LANG,
                $submission->fetch('userfullname')) . '</p>';

        // Get the submission attempt.
        $mediatype = $submission->fetch('mediatype');
        $mediaurl = $submission->fetch('mediaurl');
        $submissionplayer = self::render_submissionplayer($mediaurl,
                $mediatype);
        $ret .= $submissionplayer;

        // Get the transcript (if available).
        $transcript = $submission->fetch('transcript');
        $ret .= '<br>' . get_string('showingtranscript',
                constants::M_LANG);
        if ($transcript) {
            $ret .= $transcript;
        } else {
            $ret .= get_string('nodataavailable', constants::M_LANG);
        }
        $ret .= '<br>' . \html_writer::end_div(); // Attempt container.

        return $ret;
    }
    public function render_attempt_grade($submission) {

        $showgrade = $submission->fetch('showgrade');
        if ($showgrade) {
            $maxgrade = $submission->fetch('grade');
            $currentgrade = $submission->fetch('sessionscore');
            $displaygrade = $currentgrade . ' / ' . $maxgrade;
            return get_string('currentgrade', constants::M_LANG) . $displaygrade;
        }

        return '';
    }
    /**
     * renders the user's media submission
     */
    public function render_submissionplayer($mediaurl, $submissiontype) {
        switch ($submissiontype){
            case 'video':
                $tag = 'video';
                break;
            case 'audio':
            default:
                $tag = 'audio';
        }

        $header = get_string('thesubmission', constants::M_LANG);

        $audioplayer = \html_writer::tag($tag, '',
                array('controls'=>'', 'src'=>$mediaurl,
                'id'=>constants::M_GRADING_PLAYER));

        return \html_writer::div($header . '<br>' . $audioplayer,
                constants::M_GRADING_PLAYER_CONTAINER,
                array('id'=>constants::M_GRADING_PLAYER_CONTAINER));
    }
    public function render_current_feedback($submission) {

        $ret = \html_writer::start_div(
                constants::M_GRADING_CURRENTFB_CONTAINER);
        $ret .= $this->output->heading(get_string('showcurrentfb',
                constants::M_LANG), 3);

        $ret .= $this->render_feedbacktext(
                $submission->fetch('feedbacktext'));
        $ret .= $this->render_feedbackaudio($submission->fetch('feedbackaudio'));
        $ret .= $this->render_feedbackvideo($submission->fetch('feedbackvideo'));

        $ret .= \html_writer::end_div(); // Current feedback container.

        return $ret;

    }


    public function render_hiddenaudioplayer(){
        $audioplayer = \html_writer::tag('audio','',array('src'=>'','id'=>constants::M_HIDDEN_PLAYER,'class'=>constants::M_HIDDEN_PLAYER));
        return $audioplayer;
    }

    public function render_hiddenvideoplayer(){
        $title = 'Someones Video';
        $videoplayer = \html_writer::tag('video','',array('src'=>'','id'=>constants::M_HIDDEN_VIDEO_PLAYER,'class'=>constants::M_HIDDEN_VIDEO_PLAYER, 'controls'=>'true'));
        $content=$videoplayer;
        $modal = $this->fetch_modalcontainer($title,$content,'hiddenvideocontainer');
        return $modal;
    }

    // Render any existing feedback.
    public function render_feedbackaudio($mediaurl){

        $ret = '<br>' . $this->output->heading(
                get_string('feedbackaudiolabel',
                constants::M_LANG), 5);

        if ($mediaurl == '') {
            $ret .= get_string('nodataavailable',
                    constants::M_LANG);
        } else {

            $audioplayer = \html_writer::tag('audio','',
                    array('controls'=>'',
                    'src'=>$mediaurl,'id'=>constants::
                    M_GRADING_PLAYER));

            $ret .= \html_writer::div($audioplayer,
                    constants::M_GRADING_PLAYER_CONTAINER,
                    array('id'=>constants::
                    M_GRADING_PLAYER_CONTAINER));
        }
        return $ret;
    }
    public function render_feedbackvideo($mediaurl){

        $ret = '<br>' . $this->output->heading(
                get_string('feedbackvideolabel',
                constants::M_LANG), 5);

        if ($mediaurl == '') {
            $ret .= get_string('nodataavailable',constants::M_LANG);
        } else {
            $videoplayer = \html_writer::tag('video','',
                    array('controls'=>'','src'=>$mediaurl,
                    'id'=>constants::M_GRADING_PLAYER));

            $ret .= \html_writer::div($videoplayer,
                    constants::M_GRADING_PLAYER_CONTAINER,
                    array('id'=>constants::
                    M_GRADING_PLAYER_CONTAINER));
        }
        return $ret;
    }

    public function render_feedbacktext($text) {

        $ret = $this->output->heading(get_string('feedbacktextlabel',
                constants::M_LANG), 5);

        if ($text == '') {
            $ret .= get_string('nodataavailable',constants::M_LANG);
        } else {

            $contextid = $this->submission->fetch('modulecontextid');
            $attemptid = $this->submission->fetch('attemptid');

            $text = file_rewrite_pluginfile_urls($text,
                    'pluginfile.php', $contextid, constants::M_COMP,
                    'feedbacktext', $attemptid);

            $ret .= \html_writer::div(format_text($text),
                    constants::M_GRADING_PLAYER_CONTAINER,
                    array('id'=>constants::M_GRADING_PLAYER_CONTAINER));
        }

        return $ret;
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
}