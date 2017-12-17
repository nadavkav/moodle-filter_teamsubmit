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
 * team_submission block - manage submission team members.
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once('../../config.php');
include_once('locallib.php');

global $DB, $CFG;

$contextid = required_param('contextid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$memberlimit = required_param('memberlimit', PARAM_INT);
$memberids = optional_param_array('memberids', array(), PARAM_INT);

require_login();

$module = filter_get_course_module_from_contextid($contextid);

$listaborted = get_string('listaborted', 'filter_teamsubmit');
if (count($memberids) > $memberlimit - 1) {
    echo "Member list updated aborted";
    redirect($CFG->wwwroot . '/mod/assign/view.php?id=' . $module[1]->id, $listaborted, 3);
//    redirect($CFG->wwwroot.'/mod/assign/view.php?id='.$module[1]->id, 'Your list of assignment team members was aborted, exceeded team limit', 3);
}

if ($module[1]->modname == 'assign') {
    //$assign = $DB->get_record('assign', array('id' => $module->instanceid));
    $record = $DB->get_record('filter_team_submit', array('userid' => $userid, 'cmid' => $module[1]->id));

    if ($record) {
        $record->teammembers = implode(',', $memberids);
        $DB->update_record('filter_team_submit', $record);
    } else {
        $record = new stdClass();
        $record->cmid = $module[1]->id;
        $record->userid = $userid;
        $record->teammembers = implode(',', $memberids);
        $DB->insert_record('filter_team_submit', $record);
    }

}

echo "Member list updated";
$listupdated = get_string('listupdated', 'filter_teamsubmit');
redirect($CFG->wwwroot . '/mod/assign/view.php?id=' . $module[1]->id, $listupdated, 3);
//redirect($CFG->wwwroot.'/mod/assign/view.php?id='.$module[1]->id, 'Your list of assignment team members was updated successfully', 3);
die;

// TODO: Display a Moodle page with list of updated team memebers confirmation
$context = context_course::instance($courseid);
$PAGE->set_context($context);

