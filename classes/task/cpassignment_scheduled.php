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
 * The mod_cpassignment scheduled task
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cpassignment\task;
defined('MOODLE_INTERNAL') || die();

use \mod_cpassignment\constants;

/**
 * The mod_cpassignment scheduled task.
 *
 * @package    mod_cpassignment
 * @since      Moodle 2.7
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cpassignment_scheduled extends \core\task\scheduled_task {

	public function get_name() {
        // Shown in admin screens
        return get_string('cpassignmenttask', constants::M_LANG);
    }

	 /**
     *  Run all the tasks
     */
	 public function execute(){
		$trace = new \text_progress_trace();
        	//cpassignment_dotask($trace);
	}

}

