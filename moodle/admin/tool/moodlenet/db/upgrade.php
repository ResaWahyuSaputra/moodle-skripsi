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
 * Upgrade script for tool_moodlenet.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_moodlenet_upgrade(int $oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2020030501.01) {
        // Disable the MoodleNet integration by default till further notice.
        set_config('enablemoodlenet', 0, 'tool_moodlenet');

        // Change the domain.
        $defaultmoodlenet = get_config('tool_moodlenet', 'defaultmoodlenet');

        if ($defaultmoodlenet === 'https://home.moodle.net') {
            set_config('defaultmoodlenet', 'https://moodle.net', 'tool_moodlenet');
        }

        // Change the name.
        $defaultmoodlenetname = get_config('tool_moodlenet', 'defaultmoodlenetname');

        if ($defaultmoodlenetname === 'Moodle HQ MoodleNet') {
            set_config('defaultmoodlenetname', 'MoodleNet Central', 'tool_moodlenet');
        }

        upgrade_plugin_savepoint(true, 2020030501.01, 'tool', 'moodlenet');
    }

    return true;
}
