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
 * @package   mod_cpassignment
 * @copyright 2014 Justin Hunt poodllsupport@gmail.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \mod_cpassignment\constants;

/**
 * Define all the restore steps that will be used by the restore_cpassignment_activity_task
 */

/**
 * Structure step to restore one cpassignment activity
 */
class restore_cpassignment_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $userinfo = $this->get_setting_value('userinfo'); // are we including userinfo?

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - non-user data
        ////////////////////////////////////////////////////////////////////////

        // root element describing cpassignment instance
        $oneactivity = new restore_path_element(constants::M_MODNAME, '/activity/cpassignment');
        $paths[] = $oneactivity;

        // End here if no-user data has been selected
        if (!$userinfo) {
            return $this->prepare_activity_structure($paths);
        }

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - user data
        ////////////////////////////////////////////////////////////////////////
		//attempts
		 $attempts= new restore_path_element(constants::M_USERTABLE,
                                            '/activity/cpassignment/attempts/attempt');
        //keys
        $keys= new restore_path_element(constants::M_KEYTABLE,
                '/activity/cpassignment/keys/key');
		$paths[] = $attempts;
        $paths[] = $keys;

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_cpassignment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);


        // insert the activity record
        $newitemid = $DB->insert_record(constants::M_TABLE, $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }


	protected function process_cpassignment_attempt($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->timecreated = $this->apply_date_offset($data->timecreated);


        $data->{constants::M_MODNAME . 'id'} = $this->get_new_parentid(constants::M_MODNAME);
        $newitemid = $DB->insert_record(constants::M_USERTABLE, $data);

		// Mapping without files
		//here we set the table name as the "key" to the mapping, but its actually arbitrary
		//'we would need to use the "key" later when calling add_related_files for the itemid in the moodle files area
		//IF we had files for this set of data. )
       $this->set_mapping(constants::M_USERTABLE, $oldid, $newitemid, true);
    }
    protected function process_cpassignment_key($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->timecreated = $this->apply_date_offset($data->timecreated);


        $data->{constants::M_MODNAME . 'id'} = $this->get_new_parentid(constants::M_MODNAME);
        $newitemid = $DB->insert_record(constants::M_KEYTABLE, $data);

        // Mapping without files
        $this->set_mapping(constants::M_KEYTABLE, $oldid, $newitemid, false);
    }


    protected function after_execute() {
        // Add module related files, no need to match by itemname (just internally handled context)
        $this->add_related_files(constants::M_COMP, 'intro', null);
		$this->add_related_files(constants::M_COMP, 'instructions', null);
		$this->add_related_files(constants::M_COMP, 'passage', null);
		$this->add_related_files(constants::M_COMP, 'completion', null);
		 $userinfo = $this->get_setting_value('userinfo'); // are we including userinfo?
		 if($userinfo){
			$this->add_related_files(constants::M_COMP, constants::M_FILEAREA_SUBMISSIONS, constants::M_USERTABLE);
             $this->add_related_files(constants::M_COMP, constants::M_FILEAREA_FEEDBACKTEXT, constants::M_USERTABLE);
		 }
    }
}
