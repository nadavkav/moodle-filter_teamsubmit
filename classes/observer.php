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
 * Event observers.
 *
 * @package    filter_team_submit
 * @copyright  2016 onwards - Weizmann institute
 * @author     Nadav Kavalerchik <nadavkav@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_teamsubmit;
defined('MOODLE_INTERNAL') || die();

class observer {

    public static function update_team_memebers_grades(\mod_assign\event\submission_graded $event) {
        global $DB, $CFG;

        // todo: check if module is using filter_teamsubmit

        $user = \core_user::get_user($event->relateduserid);
        $mainuser = $DB->get_record('filter_team_submit', array('userid' => $event->relateduserid, 'cmid' => $event->contextinstanceid));
        if (!$mainuser) {
            // Team leader user was not found
            // Problem? grade given to a user which is not leading (submitted) the team.
            return false;
        }

        // Maybe...
        //$user = \core_user::get_user($event->objectid);

        $sender = get_admin();
/*
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->dirroot . '/lib/accesslib.php');
        //require_once($CFG->dirroot . '/mod/assign/gradeform.php');
        $context = \context_module::instance($event->contextinstanceid);
        $cm = $event->get_context();//get_coursemodule_from_instance('assign', $event->contextinstanceid, 0, false, MUST_EXIST);
        $assignment = new assign($context, $cm, null);
        $gradedata = new \stdClass();
        $gradedata->id = $event->contextinstanceid;
        $gradedata->grade = 88;
        $gradedata->userid = 42;
        $gradedata->rownum = 0;
        $gradedata->attemptnumber = -1;
        $gradedata->gradingpanel = true;
        $assignment->save_grade(50, $gradedata);
*/

        $cm = $DB->get_record('course_modules', array('id'=>$event->contextinstanceid));

        require_once($CFG->dirroot.'/mod/assign/lib.php');

        //include_once($CFG->dirroot.'/filter/teamsubmit/locallib.php');
        //$module = filter_get_course_module_from_contextid($event->contextinstanceid);
        // Get main user's grade (the team member user that submitted the assignment)
        //$mainusergrades = $DB->get_record('assign_grades', array('userid'=>$mainuser->userid, 'assignment'=>$module[1]->instance));
        $mainusergrades = $DB->get_record('assign_grades', array('userid'=>$mainuser->userid, 'assignment'=>$cm->instance));

        // Set main user's grade to all other team members.
        foreach(explode(',', $mainuser->teammembers) as $memberid) {
            $grade = new \stdClass();
            $grade->assignment = $cm->instance;
            $grade->userid = $memberid;
            $grade->timecreated = time();
            $grade->timemodified = $grade->timecreated;
            $grade->grader = $event->userid;
            //$grade->grade = '100';
            $grade->grade = $mainusergrades->grade;
            $grade->locked = 0;
            $grade->mailed = 0;


            // Check if team member's grade already exists,
            // if so overwrite instead of adding a new one
            $checkIfGraded = $DB->get_record('assign_grades', array('userid'=>$memberid, 'assignment'=>$grade->assignment));
            //$grade->grade = $checkIfGraded->grade;

            if($checkIfGraded){
                $grade->id = $checkIfGraded->id;
                $result = $DB->update_record('assign_grades', $grade);
            } else {
                $result = $DB->insert_record('assign_grades', $grade);
            }

            if ($result) {
                $grade2 = new \stdClass();
                $grade2->userid   = $grade->userid;
                $grade2->rawgrade = $grade->grade;
                $grade2->usermodified = $grade->grader;
                $grade2->datesubmitted = NULL;
                $grade2->dategraded = $grade->timemodified;
                //$grade2->feedbackformat = '';
                //$grade2->feedback = '';
                $assign = $DB->get_record('assign', array('id' => $cm->instance));
                $assign->cmidnumber = $cm->id;
                assign_grade_item_update($assign, $grade2);

            }
        }

        // testing
        //email_to_user($developer, $sender, 'user was graded on assignment', 'userid='.$user->id, 'userid='.$user->id);
        return true;
    }

}