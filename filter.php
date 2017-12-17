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
 * Filter "teamsubmit"
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_teamsubmit extends moodle_text_filter
{

    /*
     * This main filter function replaces the [[teamsubmit]] syntax with team submit HTML UI code.
     *
     * @param string $text The text to filter.
     * @param array $options The filter options.
     */
    public function filter($text, array $options = array())
    {
        global $CFG, $DB, $USER;

        include_once($CFG->dirroot . '/filter/teamsubmit/locallib.php');

        // Get config
        $filter_teamsubmit = get_config('filter_teamsubmit');

        // {GENERICO:type="team_submit",LIMIT="5"}
        // Or...
        // [[teamsubmit,5]] (5=group memebers limit including leader which submit)
        $pattern_generico = '/{GENERICO:type="team_submit",LIMIT="(.*)"}/i';
        $pattern = "/\[\[teamsubmit,(.*)\]\]/s";
        // Search filter placeholder
        preg_match_all($pattern, $text, $matches);
        preg_match_all($pattern_generico, $text, $matches_generico);

        // Prepare newtext variable
        $teamsubmittext = '';

        // Do if placeholder is found
        if ((!empty($matches) && count($matches[0]) > 0)
            OR (!empty($matches_generico) && count($matches_generico[0]) > 0)) {

            //$teamsubmittext = \html_writer::div('team submit instructions', 'teamsubmit');

            if ($matches[1]) {
                $filter_teamsubmit->teammemberslimit = $matches[1][0];
            }

            if ($matches_generico[1]) {
                $filter_teamsubmit->teammemberslimit = $matches_generico[1][0];
            }

            /////////////////////////////

            $contextid = (string)$this->context->id;
            $currentcontext = $this->context; //$this->page->context->get_course_context(false);
            $module = filter_get_course_module_from_contextid($contextid);

            $blockinstanceid = 1;//$this->instance->id;

            // Teacher management settings.
            if (has_capability('moodle/course:create', $currentcontext)) {
                $showteammembers = get_string('showteammembers', 'filter_teamsubmit');
                // todo: fix me
                //$blockinstanceid = 1;//$this->instance->id;
                $params = array('cmid' => $module[1]->id, 'instanceid' => $blockinstanceid);
                $showteammembersurl = new \moodle_url($CFG->wwwroot . '/filter/teamsubmit/show_teams.php', $params);
                //$this->content->text .= \html_writer::empty_tag('hr');
                $teamsubmittext = \html_writer::link($showteammembersurl, $showteammembers);

                $teamsubmittext .= \html_writer::div(get_string('maxteammemebers', 'filter_teamsubmit',
                    $filter_teamsubmit->teammemberslimit));
                /*
                $inputlimitmembers = "<LABEL>".get_string('teammemberslimit', 'filter_teamsubmit').
                    " <INPUT name='teammemberslimit' value='{$filter_teamsubmit->teammemberslimit}' SIZE='3'></LABEL>";
                //$inputlimitmembers .= "<INPUT TYPE='hidden' name='memberlimit' value='{$blockinstanceid}'>";
                $inputlimitmembers .= "<INPUT TYPE='hidden' name='action' value='teammemberslimit'>";
                $inputlimitmembers .= "<INPUT TYPE='hidden' name='cmid' value='{$module[1]->id}'>";
                $inputlimitmembers .= '<BR><INPUT TYPE="submit" name="submit" value="'.get_string('update').'">';
                $formlimitmembers = "<FORM action='$CFG->wwwroot/filter/teamsubmit/manage_settings.php' type='GET'>$inputlimitmembers</FORM>";
                $teamsubmittext .= $formlimitmembers;
                */
                //return preg_replace($pattern, $teamsubmittext, $text);
                if ($matches_generico[1]) {
                    return preg_replace($pattern_generico, $teamsubmittext, $text);
                } else {
                    return preg_replace($pattern, $teamsubmittext, $text);
                }
            }

            // Probably not a teacher, continue with Student's UI...

            // Get all already members in teams into an array
            // we use that array to filter out the already members
            // from the list of available "to choose from" users
            $alreadymemebersonotherteams = array();
            $sql = 'SELECT * FROM {filter_team_submit} WHERE cmid = ?';
            $allalreadyteammemebers = $DB->get_records_sql($sql, array($module[1]->id));
            foreach ($allalreadyteammemebers as $members) {
                // Do not add my team members to the list.
                if ($USER->id == $members->userid) continue;
                $alreadymemebersonotherteams = array_merge($alreadymemebersonotherteams, explode(',', $members->teammembers));
            }

            // Do not allow me (student) to choose a team members if I am already a part of a team.
            $sql = 'SELECT * FROM {filter_team_submit} WHERE cmid = ? AND FIND_IN_SET(?, teammembers) LIMIT 1';
            $teamiampartof = $DB->get_record_sql($sql, array($module[1]->id, $USER->id));
            if ($teamiampartof) {
                $teamleader = \core_user::get_user($teamiampartof->userid);
                $html_teamiampartof = \html_writer::div(get_string('teamiampartof', 'filter_teamsubmit', fullname($teamleader)));
                $html_teamiampartof .= \html_writer::nonempty_tag('style', '.path-mod-assign .submissionaction {display:none;}');
                $teamsubmittext = $html_teamiampartof;
                //return preg_replace($pattern, $teamsubmittext, $text);
                if ($matches_generico[1]) {
                    return preg_replace($pattern_generico, $teamsubmittext, $text);
                } else {
                    return preg_replace($pattern, $teamsubmittext, $text);
                }
            }

//            $html_teammemberediting = \html_writer::div(get_string('teammemberediting', 'filter_teamsubmit'));
//            $html_teammemberediting .= \html_writer::div(get_string('maxteammemebers', 'filter_teamsubmit', $filter_teamsubmit->teammemberslimit));
//            $html_teammemberediting .= \html_writer::empty_tag('br');

            // My team members.
            $myteam = $DB->get_record('filter_team_submit', array('userid' => $USER->id, 'cmid' => $module[1]->id));
            $myteammembers = explode(',', $myteam->teammembers);
            $html_myteammemebers = '';

            $html_myteammemebers .= \html_writer::tag('h3', get_string('myteammembers', 'filter_teamsubmit'));
            if (!empty($myteam->teammembers) && $myteam->teammembers != '') {
                $html_myteammemebers .= \html_writer::start_tag('ul', array('id' => 'teammembers'));
                foreach ($myteammembers as $teammemberid) {
                    $member = core_user::get_user($teammemberid);
                    $html_myteammemebers .= \html_writer::tag('li', fullname($member));
                }
                $html_myteammemebers .= \html_writer::end_tag('ul');
            } else {
                $html_myteammemebers .= \html_writer::div(get_string('noteammembers', 'filter_teamsubmit'));
            }
            $html_myteammemebers .= \html_writer::empty_tag('hr');
            $html_myteammemebers .= \html_writer::div(get_string('teammemberediting', 'filter_teamsubmit'));
            $html_myteammemebers .= \html_writer::div(get_string('maxteammemebers', 'filter_teamsubmit', $filter_teamsubmit->teammemberslimit));
            $html_myteammemebers .= \html_writer::empty_tag('br');
            // Team members chooser.
            //$currentcontext = $this->page->context->get_course_context(false);
            $users = get_enrolled_users($currentcontext, 'moodle/grade:view');

            //$userlist = get_string('chooseteammembers', 'filter_teamsubmit');
            $userlist = '';
            foreach ($users as $user) {
                // Remove current user from team members list.
                if ($USER->id == $user->id) continue;

                // Remove user from available users if already a member of other team.
                if (in_array($user->id, $alreadymemebersonotherteams)) continue;

                // Pre select team members.
                if (in_array($user->id, $myteammembers)) {
                    $checked = 'checked=checked';
                } else {
                    $checked = '';
                }
                $fullusername = $user->firstname . ' ' . $user->lastname;
                $userlist .= "<label><input type='checkbox' name='memberids[]' $checked value='$user->id' $checked> $fullusername</label>";
            }

            $hidden = "<INPUT TYPE='hidden' name='userid' value='$USER->id'>";
            $hidden .= "<INPUT TYPE='hidden' name='contextid' value='$contextid'>";
            $hidden .= "<INPUT TYPE='hidden' name='memberlimit' value='{$filter_teamsubmit->teammemberslimit}'>";
            $submit = '<BR><INPUT TYPE="submit" name="submit" value="' . get_string('update') . '">';

            $html_teammemberediting = $html_myteammemebers .
                "<FORM action='$CFG->wwwroot/filter/teamsubmit/manage_members.php'>$userlist $hidden $submit</FORM>";
        }

        if (!empty($html_teammemberediting)) {
            if ($matches_generico[1]) {
                return preg_replace($pattern_generico, $html_teammemberediting, $text);
            } else {
                return preg_replace($pattern, $html_teammemberediting, $text);
            }
        } else {
            return $text;
        }

    }
}
