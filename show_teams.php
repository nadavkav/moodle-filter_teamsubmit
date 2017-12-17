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
 * team_submission filter - manage submission team members.
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once('../../config.php');
include_once('locallib.php');

global $DB;

$cmid = required_param('cmid', PARAM_INT);
$filterinstanceid = required_param('instanceid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$teamid = optional_param('teamid', -1, PARAM_INT);
$memberid = optional_param('memberid', -1, PARAM_INT);

require_login();

$module = filter_get_course_module_from_cmid($cmid);

if ($module[1]->modname == 'assign') {
    //$assign = $DB->get_record('assign', array('id' => $module->instanceid));
    $html = '';

    // Remove a member from a team (will not remove primary team leader)
    if ($action == 'removemember' && $teamid > 0 && $memberid > 0) {
        $team = $DB->get_record('filter_team_submit', array('id' => $teamid));

        if ($team) {
            $teammembers = explode(',', $team->teammembers);
            unset($teammembers[array_search($memberid, $teammembers)]);
            $team->teammembers = implode(',', $teammembers);
            $DB->update_record('filter_team_submit', $team);
            $html .= \html_writer::tag('div', get_string('success')); //'Member X successfuly removed'
        } else {
            // not found and not removed error
            $html .= \html_writer::tag('div', get_string('error')); //Error - removing member X from team Y
        }
    }

    //$records = $DB->get_records('filter_team_submit', array('userid' => $userid, 'cmid' => $cmid));
    $teams = $DB->get_records('filter_team_submit', array('cmid' => $cmid));


    foreach ($teams as $team) {
        if ($team->teammembers != '') {
            $mainuser = core_user::get_user($team->userid);
            $html .= \html_writer::tag('h3', get_string('team', 'filter_teamsubmit') . ' (' . $team->id . ') ' .
                get_string('teamleader', 'filter_teamsubmit') . ': ' . fullname($mainuser));

            // Dsiplay team leader grade, which is also distributed to all team members.
            $teamleadergrade = $DB->get_record('assign_grades', array('userid' => $team->userid, 'assignment' => $module[1]->instance));
            $leadergrade = (!empty($teamleadergrade->grade)) ? $teamleadergrade->grade : '';
            $leadergrade = floor($leadergrade);
            if ($leadergrade) {
                $html .= \html_writer::tag('div', get_string('teamleadergrade', 'filter_teamsubmit', $leadergrade));
            }

            $teammembers = explode(',', $team->teammembers);
            $html .= \html_writer::start_tag('ul', array('id' => 'teammembers'));
            foreach ($teammembers as $teammemberid) {
                $member = core_user::get_user($teammemberid);
                $membergrades = $DB->get_record('assign_grades', array('userid' => $teammemberid, 'assignment' => $module[1]->instance));
                $removemember = \html_writer::link(new moodle_url('show_teams.php',
                    array('action' => 'removemember', 'teamid' => $team->id, 'memberid' => $teammemberid, 'cmid' => $cmid,
                        'instanceid' => $filterinstanceid)), '<i class="fa fa-times" aria-hidden="true"></i>'); //get_string('remove'));
                $html .= \html_writer::tag('li', fullname($member) . ' [' . $removemember . ']');
            }
            $html .= \html_writer::end_tag('ul');
        } else {
            $html .= '';//\html_writer::div(get_string('noteammembers', 'filter_team_submit'));
        }


    }
    $urlparams = array('cmid' => $cmid, 'instanceid' => $filterinstanceid);
    $PAGE->set_url('/filter/teamsubmit/show_teams.php', $urlparams);
    $context = context_course::instance($module[1]->course);
    $PAGE->set_context($context);
    $strshowteammembers = get_string('showteammembers', 'filter_teamsubmit');
    $PAGE->set_title($strshowteammembers);
    $PAGE->set_heading($strshowteammembers);
    $course = get_course($module[1]->course);
    $PAGE->navbar->add($course->shortname, new \moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $module[1]->course)));
    $PAGE->navbar->add($module[1]->name, new \moodle_url($CFG->wwwroot . "/mod/{$module[1]->modname}/view.php", array('id' => $module[1]->id)));
    $PAGE->navbar->add($strshowteammembers);
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
    echo $html;
    echo \html_writer::link(new moodle_url($CFG->wwwroot . '/mod/assign/view.php', array('id' => $cmid)),
        get_string('backtoassignment', 'filter_teamsubmit'), array('class' => 'btn'));
    echo $OUTPUT->footer();
}
