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
 * Plugin event observers are registered here.
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// For more information about the Events API, please visit:
// https://docs.moodle.org/dev/Event_2

$observers = array(

    array(
        'eventname' => '\mod_assign\event\submission_graded',
        'callback' => '\filter_teamsubmit\observer::update_team_memebers_grades',
        'schedule' => 'instant',
    ),
    array(
        //'eventname' => '\mod_assign\event\submission_updated',
        'eventname' => '\assignsubmission_onlinetext\event\submission_updated',
        'callback' => '\filter_teamsubmit\observer::update_team_memebers_submision_status_updated',
        'schedule' => 'instant',
    ),
    array(
        //'eventname' => '\mod_assign\event\submission_updated',
        'eventname' => '\assignsubmission_file\event\submission_updated',
        'callback' => '\filter_teamsubmit\observer::update_team_memebers_submision_status_updated',
        'schedule' => 'instant',
    ),
    array(
        'eventname' => '\mod_assign\event\submission_created',
        'callback' => '\filter_teamsubmit\observer::update_team_memebers_submision_status_created',
        'schedule' => 'instant',
    ),
);
