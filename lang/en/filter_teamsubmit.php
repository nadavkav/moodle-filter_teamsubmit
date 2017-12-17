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
/*
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['filtername'] = 'Team submission';
$string['pluginname'] = 'Team submission';
$string['configheading'] = 'Team Submit global settings';
$string['teammemberslimit'] = 'Team members limit';
$string['teammemberslimit_desc'] = 'Team members limit - a number between 1 to 10';

$string['chooseteammembers'] = 'Choose submission team members';
$string['showteammembers'] = 'Show team members';
$string['myteammembers'] = 'My team members';
$string['noteammembers'] = 'No team members';
$string['teamleader'] = 'Team leader';
$string['team'] = 'Team';
$string['teamiampartof'] = 'You are already a member in \'{$a}\' team. Only the team leader is allowed to submit';
$string['maxteammemebers'] = 'Teams are limited to {$a} members, including yourself.';
$string['config_teammemberslimit'] = 'Team members limit';
$string['teammemberediting'] = 'Select or unselect team members, and click UPDATE to save your selection.';
$string['backtoassignment'] = 'Back to assignment';
$string['listupdated'] = 'Your list of assignment team members was updated successfully';
$string['listaborted'] = 'Your list of assignment team members was aborted, exceeded team limit';
$string['teamleadergrade'] = 'Team final grade: {$a}';