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
 * cpassignment module admin settings and defaults
 *
 * @package    mod
 * @subpackage cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/mod/cpassignment/lib.php');

use \mod_cpassignment\constants;

if ($ADMIN->fulltree) {

    // let's use the welcome area as the activity page for
    // instructions and resources.  Perhaps rename later.
	 $settings->add(new admin_setting_configtextarea('mod_cpassignment/defaultwelcome',
        get_string('welcomelabel', 'cpassignment'), get_string('welcomelabel_details', constants::M_LANG), get_string('defaultwelcome',constants::M_LANG), PARAM_TEXT));
	 $settings->add(new admin_setting_configtextarea('mod_cpassignment/defaultfeedback',
        get_string('feedbacklabel', 'cpassignment'), get_string('feedbacklabel_details', constants::M_LANG), get_string('defaultfeedback',constants::M_LANG), PARAM_TEXT));


    $settings->add(new admin_setting_configtext('mod_cpassignment/apiuser',
        get_string('apiuser', constants::M_LANG), get_string('apiuser_details', constants::M_LANG), '', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_cpassignment/apisecret',
        get_string('apisecret', constants::M_LANG), get_string('apisecret_details', constants::M_LANG), '', PARAM_TEXT));

    $settings->add(new admin_setting_configcheckbox('mod_cpassignment/transcribe',
        get_string('transcribe', constants::M_LANG), get_string('transcribe_details',constants::M_LANG), 0));

    $regions = \mod_cpassignment\utils::get_region_options();
    $settings->add(new admin_setting_configselect('mod_cpassignment/awsregion', get_string('awsregion', constants::M_LANG), '', 'useast1', $regions));

    $expiredays = \mod_cpassignment\utils::get_expiredays_options();
    $settings->add(new admin_setting_configselect('mod_cpassignment/expiredays', get_string('expiredays', constants::M_LANG), '', '365', $expiredays));

	 $langoptions = \mod_cpassignment\utils::get_lang_options();
	 $settings->add(new admin_setting_configselect('mod_cpassignment/language', get_string('language', constants::M_LANG), '', 'en', $langoptions));

    $mediaoptions = \mod_cpassignment\utils::get_mediatype_options();
    $settings->add(new admin_setting_configselect('mod_cpassignment/mediatype', get_string('mediatype', constants::M_LANG), '', 'audio', $mediaoptions));

    $recordertypeoptions = \mod_cpassignment\utils::get_recordertype_options();
    $settings->add(new admin_setting_configselect('mod_cpassignment/recordertype', get_string('recordertype', constants::M_LANG), '', 'bmr', $recordertypeoptions));

	 $settings->add(new admin_setting_configtext('mod_cpassignment/itemsperpage',
        get_string('itemsperpage', constants::M_LANG), get_string('itemsperpage_details', constants::M_LANG), 40, PARAM_INT));

}
