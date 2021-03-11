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
 * Wordcloud view
 *
 * @package    mod_wordcloud
 * @copyright  2020 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');

global $PAGE, $OUTPUT, $DB;

$id = required_param('id', PARAM_INT);
list ($course, $cm) = get_course_and_cm_from_cmid($id, 'wordcloud');

$context = context_module::instance($cm->id);

require_login($course, false, $cm);
require_capability('mod/wordcloud:view', $context);

$wordcloud = $DB->get_record('wordcloud', array('id' => $cm->instance));

$PAGE->set_url(new moodle_url("/mod/wordcloud/view.php", ['id' => $id]));
$node = $PAGE->settingsnav->find('mod_wordcloud', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}

$pagetitle = get_string('pagetitle', 'wordcloud');
$PAGE->set_title($wordcloud->name);
$PAGE->set_heading($course->shortname);

echo $OUTPUT->header();
//echo $OUTPUT->heading($wordcloud->name);

if (trim(strip_tags($wordcloud->intro))) {
    $formatoptions = new stdClass();
    $formatoptions->noclean = true;
    echo $OUTPUT->box(format_text($wordcloud->intro, $wordcloud->introformat, $formatoptions),
            'generalbox', 'intro');
}
$wordcloudconfig = get_config('wordcloud');

$templatecontext['wordcloudname'] = $wordcloud->name;
$templatecontext['cloudhtml'] = mod_wordcloud_get_cloudhtml($wordcloud->id);
$templatecontext['exportlink'] = new moodle_url("/mod/wordcloud/export.php", ['id' => $id]);

if (has_capability('mod/wordcloud:submit', $context)) {
    $PAGE->requires->js_call_amd('mod_wordcloud/addwordtowordcloud', 'init', [$wordcloudconfig->refresh, $wordcloud->id, time()]);
    $templatecontext['writeaccess'] = true;
} else {
    $templatecontext['writeaccess'] = false;
}

$renderer = $PAGE->get_renderer('core');

echo $renderer->render_from_template('mod_wordcloud/wordcloud', $templatecontext);
echo $OUTPUT->footer();
