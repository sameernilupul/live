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
 * @package    mod_live
 * @copyright  2013 Sameera Nilupul
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // live instance ID - it should be named as the first character of the module

if ($id) {
    $cm = get_coursemodule_from_id('live', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $live = $DB->get_record('live', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $live = $DB->get_record('live', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $live->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('live', $live->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'live', 'view', "view.php?id={$cm->id}", $live->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/live/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($live->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

if ($live->intro) { // Conditions to show the intro can change to look for own settings
    echo $OUTPUT->box(format_module_intro('live', $live, $cm->id), 'generalbox mod_introbox', 'liveintro');
}

if (has_capability('mod/live:write', $context)) {
    if (!strcmp($live->broadcast, 'yes')) {
        echo $OUTPUT->box(get_string('link_onair', 'live').get_string('broadcasturl_top', 'live').$live->youtubeusername.get_string('broadcasturl_bottom', 'live'), 'generalbox mod_introbox', 'liveintro');
    } else {
        echo $OUTPUT->box(get_string('link_hangout', 'live'), 'generalbox mod_introbox', 'liveintro');
    }
} else {
    echo 'Dont have capability to start a lecture you can only view the hangout go to following link';
    echo '<br> http://www.youtube.com/user/sameeranilupul/live';
}

// Finish the page
echo $OUTPUT->footer();
