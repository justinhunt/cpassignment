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
 * Defines all the backup steps that will be used by {@link backup_cpassignment_activity_task}
 *
 * @package     mod_cpassignment
 * @category    backup
 * @copyright   2015 Justin Hunt (poodllsupport@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \mod_cpassignment\constants;

/**
 * Defines the complete webquest structure for backup, with file and id annotations
 *
 */
class backup_cpassignment_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the cpassignment element inside the webquest.xml file
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // are we including userinfo?
        $userinfo = $this->get_setting_value('userinfo');

        ////////////////////////////////////////////////////////////////////////
        // XML nodes declaration - non-user data
        ////////////////////////////////////////////////////////////////////////

        // root element describing cpassignment instance
        $oneactivity = new backup_nested_element(constants::M_MODNAME, array('id'), array(
                'key','course', 'name', 'intro', 'introformat',
                'passage', 'passageformat', 'instructions',
                'instructionsformat', 'finished',
                'finishedformat', 'timelimit', 'grade',
                'gradeoptions', 'maxattempts', 'mingrade',
                'language', 'mediatype', 'recordertype',
                'transcribe', 'subtitle', 'expiredays',
                'region', 'fbaudio', 'fbvideo',
                'timecreated', 'timemodified'
			));

		//attempts
        $attempts = new backup_nested_element('attempts');
        $attempt = new backup_nested_element('attempt', array('id'),
            array("courseid", constants::M_MODNAME . "id", "userid",
            "status", "filename", "transcript", "fulltranscript",
            "subtitles", "sessionscore", "sessiontime", "feedbacktext",
            "feedbacktextformat", "feedbackaudio", "feedbackvideo",
            "customtext1","customtext2","customtext3","customtext4","customtext5",
            "timecreated","timemodified"
		));

        //keys
        $keys = new backup_nested_element('keys');
        $key = new backup_nested_element('key', array('id'),
                array(constants::M_MODNAME . "id", "userid","key",
                        "timecreated","timemodified"
                ));

		// Build the tree.
        $oneactivity->add_child($attempts);
        $attempts->add_child($attempt);
        $oneactivity->add_child($keys);
        $keys->add_child($key);


        // Define sources.
        $oneactivity->set_source_table(constants::M_TABLE, array('id' => backup::VAR_ACTIVITYID));

        //sources if including user info
        if ($userinfo) {
			$attempt->set_source_table(constants::M_USERTABLE,
			array(constants::M_MODNAME . 'id' => backup::VAR_PARENTID));
            $key->set_source_table(constants::M_KEYTABLE,
                    array(constants::M_MODNAME . 'id' => backup::VAR_PARENTID));
        }

        // Define id annotations.
        $attempt->annotate_ids('user', 'userid');


        // Define file annotations.
        // intro file area has 0 itemid.
        $oneactivity->annotate_files(constants::M_COMP, 'intro', null);
		$oneactivity->annotate_files(constants::M_COMP, 'instructions', null);
		$oneactivity->annotate_files(constants::M_COMP, 'passage', null);
		$oneactivity->annotate_files(constants::M_COMP, 'finished', null);

		//file annotation if including user info
        if ($userinfo) {
			$attempt->annotate_files(constants::M_COMP, constants::M_FILEAREA_SUBMISSIONS, 'id');
            $attempt->annotate_files(constants::M_COMP, constants::M_FILEAREA_FEEDBACKTEXT, 'id');
        }

        // Return the root element, wrapped into standard activity structure.
        return $this->prepare_activity_structure($oneactivity);


    }
}
