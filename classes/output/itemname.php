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
 * Contains class core_cohort\output\cohortname
 *
 * @package   block_readseedteacher
 * @copyright 2019 Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_cpassignment\output;

use \mod_cpassignment\constants;
use lang_string;

/**
 * Class to prepare a klass name for display.
 *
 * @package   block_readseedteacher
 * @copyright 2019 Justin Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class itemname extends \core\output\inplace_editable {
    /**
     * Constructor.
     *
     * @param stdClass $klass
     */
    public function __construct($item) {
        $coursecontext = \context_course::instance($item->courseid);
        $editable = has_capability('mod/cpassignment:itemedit', $coursecontext);
        $displayvalue = format_string($item->{constants::LIST_ITEM_NAME} , true, array('context' => $coursecontext));
        parent::__construct(constants::M_COMP, 'itemname', $item->id, $editable,
            $displayvalue,
                $item->{constants::LIST_ITEM_NAME},
            new lang_string('edititemname', constants::M_COMP),
            new lang_string('newnamefor', constants::M_COMP, $displayvalue));
    }

    /**
     * Updates klass name and returns instance of this object
     *
     * @param int $klassid
     * @param string $newvalue
     * @return static
     */
    public static function update($itemid, $newvalue) {
        global $DB;
        $item = $DB->get_record(constants::M_USERTABLE, array('id' => $itemid), '*', MUST_EXIST);
        if($item){
            $item->itemname=$item->{constants::LIST_ITEM_NAME};
        }
        $coursecontext = \context_course::instance($item->courseid);
        require_capability('mod/cpassignment:itemedit', $coursecontext);
        $newvalue = clean_param($newvalue, PARAM_TEXT);
        if (strval($newvalue) !== '') {
            $upd = new \stdClass();
            $upd->id=$itemid;
            $upd->{constants::LIST_ITEM_NAME} = $newvalue;
            $DB->update_record(constants::M_USERTABLE, $upd);
            $item->{constants::LIST_ITEM_NAME} =$newvalue;
            $item->itemname=$newvalue;
        }
        $tmpl = new self($item);
        return $tmpl;
    }
}
