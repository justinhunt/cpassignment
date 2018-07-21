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
 * The main cpassignment configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

use \mod_cpassignment\constants;

/**
 * Module instance settings form
 */
class mod_cpassignment_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        //----------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('cpassignmentname', constants::M_LANG), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'cpassignmentname', constants::M_LANG);

         // Adding the standard "intro" and "introformat" fields
        if($CFG->version < 2015051100){
            $this->add_intro_editor();
        }else{
            $this->standard_intro_elements();
        }

        // Add other editors.  HTML format for media files.
        $config = get_config(constants::M_FRANKY);

        //The passage area is not used but hasn't been removed yet.
        //$edfileoptions = \mod_cpassignment\utils::editor_with_files_options($this->context);

        // $opts = array('rows'=>'15', 'columns'=>'80');
        //$mform->addElement('editor','passage_editor',get_string('passagelabel',constants::M_LANG),$opts, $ednofileoptions);

        // instructions area is an html format page content area
        // It is for activity instructions and resources.
        $editorstandard = \mod_cpassignment\utils::editor_with_files_options($this->context);
        $mform->addElement('editor','instructions_editor',
                get_string('instructionslabel', constants::M_LANG), null,
                $editorstandard);
        $mform->addElement('static', 'instructionslabel', null,
                get_string('instructionslabel_details', constants::M_LANG));

         // Post-assignment completion.
        $mform->addElement('editor','finished_editor',
                get_string('finishedlabel', constants::M_LANG),
                null, $editorstandard);
        $mform->addElement('static', 'finishedlabel', null,
                get_string('finishedlabel_details', constants::M_LANG));

        //defaults
        //$mform->setDefault('passage_editor',array('text'=>'', 'format'=>FORMAT_MOODLE));
        $mform->setDefault('instructions_editor', array('text'=>$config->defaultinstructions, 'format'=>FORMAT_HTML));
        $mform->setDefault('finished_editor', array('text'=>$config->defaultfinished, 'format'=>FORMAT_HTML));

        //types
        //$mform->setType('passage_editor',PARAM_RAW);
        $mform->setType('instructions_editor', PARAM_RAW);
        $mform->setType('finished_editor',PARAM_RAW);

        // Editor rules and help
        $mform->addRule('instructions_editor', get_string('required'),
                 'required', null, 'client');
        $mform->addHelpButton('instructions_editor', 'instructions_editor',
                 constants::M_MODNAME);
        $mform->addHelpButton('finished_editor', 'finished_editor',
                 constants::M_MODNAME);

        //Enable AI
        $mform->addElement('advcheckbox', 'transcribe', get_string('transcribe', constants::M_LANG), get_string('transcribe_details', constants::M_LANG));
        $mform->setDefault('transcribe', $config->transcribe);

        //Media Type options
        $mediatypeoptions = \mod_cpassignment\utils::get_mediatype_options();
        $mform->addElement('select', 'mediatype', get_string('mediatype', constants::M_LANG), $mediatypeoptions);
        $mform->setDefault('mediatype',$config->mediatype);

        //Recorder options
        $recordertypeoptions = \mod_cpassignment\utils::get_recordertype_options();
        $mform->addElement('select', 'recordertype', get_string('recordertype', constants::M_LANG), $recordertypeoptions);
        $mform->setDefault('recordertype',$config->recordertype);

        // Language options, not required.
        /*
        $langoptions = \mod_cpassignment\utils::get_lang_options();
        $mform->addElement('select', 'language', get_string('language', constants::M_LANG), $langoptions);
        $mform->setDefault('language',$config->language);
        */
        //region
        $regionoptions = \mod_cpassignment\utils::get_region_options();
        $mform->addElement('select', 'region', get_string('region', constants::M_LANG), $regionoptions);
        $mform->setDefault('region',$config->awsregion);

        //expiredays
        $expiredaysoptions = \mod_cpassignment\utils::get_expiredays_options();
        $mform->addElement('select', 'expiredays', get_string('expiredays', constants::M_LANG), $expiredaysoptions);
        $mform->setDefault('expiredays',$config->expiredays);

        // Attempts.
        $mform->addElement('header', 'attemptsettings',
                get_string('attemptsettings', constants::M_LANG));

        // Time target (if any).
        $mform->addElement('duration', 'timelimit', get_string('timelimit',
                constants::M_LANG));
        $mform->setDefault('timelimit', 0);
        $mform->addElement('static', 'timelimitdetails', ' ',
                get_string('timelimitdetails', constants::M_LANG));

        // Permitted attempts.
        $attemptoptions = array(0 => get_string('unlimited', constants::M_LANG),
                            1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',);
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', constants::M_LANG), $attemptoptions);

        // Show grade to students?
        $mform->addElement('advcheckbox', 'showgrade',
                get_string('showgradelabel', constants::M_LANG),
                get_string('showgrade_details', constants::M_LANG),
                null, array(0, 1));
        $mform->setDefault('showgrade', $config->showgrade);

        /* Feedback options.  These are disabled for now
        $mform->addElement('header', 'feedbacksettings',
                get_string('feedbacksettings', constants::M_LANG));

        $mform->addElement('static', 'feedbackdescription',
                get_string('fblabel', constants::M_LANG),
                get_string('fbdescription', constants::M_LANG));

        $mform->addElement('advcheckbox', 'fbaudio',
                get_string('feedbackaudiolabel', constants::M_LANG),
                get_string('fbaudio_details', constants::M_LANG),
                null, array(0, 1));
        $mform->setDefault('fbaudio',$config->fbaudio);

        $mform->addElement('advcheckbox', 'fbvideo',
                get_string('feedbackvideolabel', constants::M_LANG),
                get_string('fbvideo_details', constants::M_LANG),
                null, array(0, 1));
        $mform->setDefault('fbvideo',$config->fbvideo);
    */
        // Grade.
        $this->standard_grading_coursemodule_elements();

        // Grade options.
        $gradeoptions = array(constants::M_GRADEHIGHEST => get_string(
                'gradehighest',constants::M_LANG),
                constants::M_GRADELOWEST => get_string('gradelowest',
                constants::M_LANG), constants::M_GRADELATEST =>
                get_string('gradelatest', constants::M_LANG),
                constants::M_GRADEAVERAGE => get_string('gradeaverage',
                constants::M_LANG), constants::M_GRADENONE =>
                get_string('gradenone', constants::M_LANG));

        $mform->addElement('select', 'gradeoptions', get_string('gradeoptions', constants::M_LANG), $gradeoptions);
        $mform->setDefault('gradeoptions',constants::M_GRADELATEST);



        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }


    /**
     * This adds completion rules
     * The values here are just dummies. They won't work until you implement some sort of grading
     * See lib.php cpassignment_get_completion_state()
     */
     function add_completion_rules() {
        $mform =& $this->_form;
        $config = get_config(constants::M_FRANKY);

        //timer options
        //Add a place to set a mimumum time after which the activity is recorded complete
       $mform->addElement('static', 'mingradedetails', '',get_string('mingradedetails', constants::M_LANG));
       $options= array(0=>get_string('none'),20=>'20%',30=>'30%',40=>'40%',50=>'50%',60=>'60%',70=>'70%',80=>'80%',90=>'90%',100=>'40%');
       $mform->addElement('select', 'mingrade', get_string('mingrade', constants::M_LANG), $options);

        return array('mingrade');
    }

    function completion_rule_enabled($data) {
        return ($data['mingrade']>0);
    }

    public function data_preprocessing(&$form_data) {
        $ednofileoptions = \mod_cpassignment\utils::editor_with_files_options($this->context);
        $editors  = cpassignment_get_editornames();
         if ($this->current->instance) {
            $itemid = 0;
            foreach($editors as $editor) {
                $form_data = file_prepare_standard_editor((object)$form_data,
                        $editor, $ednofileoptions, $this->context,
                        constants::M_FRANKY, $editor, $itemid);
            }
        }
    }
}

