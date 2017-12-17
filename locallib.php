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
 * Plugin internal classes, functions and constants are defined here.
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function filter_get_course_module_from_cmid($cmid)
{
    global $CFG, $DB;
    if (!$cmrec = $DB->get_record_sql("SELECT cm.*, md.name AS modname
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid))) {
        print_error('invalidcoursemodule');
    } elseif (!$modrec = $DB->get_record($cmrec->modname, array('id' => $cmrec->instance))) {
        print_error('invalidcoursemodule');
    }
    $modrec->instance = $modrec->id;
    $modrec->instanceid = $modrec->instance;
    $modrec->cmid = $cmrec->id;
    $cmrec->name = $modrec->name;

    return array($modrec, $cmrec);
}

function filter_get_course_module_from_contextid($contextid)
{
    global $CFG, $DB;

    if (!$contextrec = $DB->get_record('context', array('id' => $contextid))) {
        print_error('invalid module contextid');
    }

    if (!$cmrec = $DB->get_record_sql("SELECT cm.*, md.name AS modname
                               FROM {course_modules} cm
                               JOIN {modules} md ON md.id = cm.module
                               WHERE cm.id = ?", array($contextrec->instanceid))) {
        print_error('invalid course module');
    } elseif (!$modrec = $DB->get_record($cmrec->modname, array('id' => $cmrec->instance))) {
        print_error('invalid course module');
    }
    $modrec->instance = $modrec->id;
    $modrec->cmid = $cmrec->id;
    $cmrec->name = $modrec->name;

    return array($modrec, $cmrec);
}
