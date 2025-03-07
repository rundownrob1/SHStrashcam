<?php
// This file is part of Moodle - http://moodle.org/

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID
if (!$course = $DB->get_record('course', ['id' => $id])) {
    print_error('invalidcourseid');
}

require_course_login($course);
$PAGE->set_url('/mod/photoupload/index.php', ['id' => $id]);
$PAGE->set_title(get_string('modulenameplural', 'photoupload'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'photoupload'));

// Get all instances of this module in the course
if (!$photouploads = get_all_instances_in_course('photoupload', $course)) {
    notice(get_string('noinstances', 'photoupload'), new moodle_url('/course/view.php', ['id' => $course->id]));
    exit;
}

$table = new html_table();
$table->head = [
    get_string('name'),
    get_string('intro')
];
foreach ($photouploads as $pu) {
    $link = html_writer::link(
        new moodle_url('/mod/photoupload/view.php', ['id' => $pu->coursemodule]),
        format_string($pu->name)
    );
    $intro = format_module_intro('photoupload', $pu, $pu->coursemodule);
    $table->data[] = [$link, $intro];
}

echo html_writer::table($table);
echo $OUTPUT->footer();
