<?php
// This file is part of Moodle - http://moodle.org/

require_once('../../config.php');
require_once('locallib.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT); // The course_module ID
$cm = get_coursemodule_from_id('photoupload', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$photoupload = $DB->get_record('photoupload', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$PAGE->set_url('/mod/photoupload/view.php', ['id' => $id]);
$PAGE->set_title(format_string($photoupload->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($photoupload->name));

// Show intro text if available
if (!empty($photoupload->intro)) {
    echo $OUTPUT->box(format_module_intro('photoupload', $photoupload, $cm->id), 'generalbox mod_introbox');
}
?>

<p><?php echo get_string('introtext', 'photoupload'); ?></p>

<div id="cameraContainer" style="margin-bottom: 1em;">
    <video id="cameraVideo" width="320" height="240" autoplay playsinline></video>
    <br>
    <button id="captureBtn"><?php echo get_string('capturebutton', 'photoupload'); ?></button>
</div>

<form id="uploadFallback" method="POST" action="ajax.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="fallbackupload">
    <label for="userfile"><?php echo get_string('fallbackuploadlabel', 'photoupload'); ?></label>
    <input type="file" id="userfile" name="userfile" accept="image/*" />
    <button type="submit"><?php echo get_string('submitbutton', 'photoupload'); ?></button>
</form>

<!-- Hidden canvas to capture the frame from the video -->
<canvas id="photoCanvas" width="320" height="240" style="display:none;"></canvas>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video   = document.getElementById('cameraVideo');
    const canvas  = document.getElementById('photoCanvas');
    const context = canvas.getContext('2d');
    const captureBtn = document.getElementById('captureBtn');

    // Attempt to init camera
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(stream => {
                video.srcObject = stream;
                video.play();
            })
            .catch(err => {
                console.log("Camera not accessible. Error:", err);
                // Hide camera container or show a message? Let user fallback to upload.
                document.getElementById('cameraContainer').style.display = 'none';
            });
    } else {
        // No camera support
        document.getElementById('cameraContainer').style.display = 'none';
    }

    captureBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // Draw the current frame from video onto canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = canvas.toDataURL('image/png');

        // Send via AJAX to the server
        fetch('ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'capture',
                imageData: imageData,
                id: <?php echo $id; ?> // Pass the coursemodule ID
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                alert("Photo captured and sent successfully!");
            } else {
                alert("Error sending photo: " + (res.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error("Upload error:", error);
            alert("Error uploading photo.");
        });
    });
});
</script>

<?php
echo $OUTPUT->footer();
