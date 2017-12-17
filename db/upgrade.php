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
 * teamsubmit filter plugin upgrade code
 *
 * @package    filter_teamsubmit
 * @copyright  2016 onwards - Davidson institute (Weizmann institute)
 * @author     Nadav Kavalerchik <nadav.kavalerchik@weizmann.ac.il>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_teamsubmit_upgrade($oldversion) {
    global $DB, $CFG;

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016071801) {
        if (file_exists($CFG->dirroot.'/filter/teamsubmit/db/install.xml')) {
            $DB->get_manager()->install_from_xmldb_file($CFG->dirroot.'/filter/teamsubmit/db/install.xml');
        }

        /// store version
        upgrade_plugin_savepoint(true, 2016071801, 'filter', 'teamsubmit');
    }



    return true;
}
