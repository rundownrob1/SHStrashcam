<?php
// This file is part of Moodle - http://moodle.org/

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once('locallib.php');

$action = optional_param('action', '', PARAM_ALPHA);
$contextid = optional_param('id', 0, PARAM_INT); // The coursemodule ID

// Setup a minimal PAGE context if needed:
if ($contextid) {
    $cm = get_coursemodule_from_id('photoupload', $contextid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $context = context_module::instance($cm->id);
    require_login($course, true, $cm);
} else {
    // If no ID, at least ensure the user is logged in
    require_login();
}

if ($action === 'capture') {
    // Handle Base64-encoded image
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['imageData'])) {
        echo json_encode(['status' => 'error', 'message' => 'No image data found.']);
        die();
    }

    $base64data = $input['imageData'];
    // Remove headers like "data:image/png;base64,"
    $base64data = preg_replace('#^data:image/\w+;base64,#i', '', $base64data);
    $decoded = base64_decode($base64data);

    if (!$decoded) {
        echo json_encode(['status' => 'error', 'message' => 'Decoding base64 failed.']);
        die();
    }

    // Store to a temporary file
    $tempfile = tempnam(sys_get_temp_dir(), 'photoupload_');
    file_put_contents($tempfile, $decoded);

    // Forward to third-party
    $success = photoupload_send_to_thirdparty($tempfile);

    // Clean up
    @unlink($tempfile);

    if ($success) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Third-party upload failed.']);
    }
    die();

} else if ($action === 'fallbackupload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fallback upload
    if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
        redirect(new moodle_url('/mod/photoupload/view.php', ['id' => $contextid]),
            get_string('uploaderror', 'photoupload'), null, \core\output\notification::NOTIFY_ERROR);
        die();
    }

    $file = $_FILES['userfile'];
    $tempfile = $file['tmp_name'];

    // Optionally, rename or store file in Moodle file API. For now, just forward:
    $success = photoupload_send_to_thirdparty($tempfile);

    if ($success) {
        redirect(new moodle_url('/mod/photoupload/view.php', ['id' => $contextid]),
            get_string('uploadsuccess', 'photoupload'));
    } else {
        redirect(new moodle_url('/mod/photoupload/view.php', ['id' => $contextid]),
            get_string('uploaderror', 'photoupload'), null, \core\output\notification::NOTIFY_ERROR);
    }
    die();
}

// If no recognized action, return error.
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
die();
