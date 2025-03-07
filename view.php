<script>
// Basic example of getUserMedia usage
function initCamera() {
    const video = document.getElementById('cameraVideo');
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(stream => {
            video.srcObject = stream;
            video.play();
        })
        .catch(err => {
            console.error("Camera access error: ", err);
            // Fallback logic or user alert if camera is not accessible
        });
}

function capturePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('photoCanvas');
    const context = canvas.getContext('2d');
    // Draw current frame from video onto canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert canvas to data URL
    const imageData = canvas.toDataURL('image/png');

    // Send imageData to Moodle or your 3rd party endpoint
    sendToThirdParty(imageData);
}

function sendToThirdParty(imageData) {
    // Example using fetch to send to your plugin code or external service
    fetch('ajax_endpoint.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ imageData: imageData })
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error uploading photo:', error));
}

// On page load
window.addEventListener('load', function() {
    initCamera();
});
</script>

<video id="cameraVideo" width="320" height="240" autoplay></video>
<canvas id="photoCanvas" width="320" height="240" style="display:none;"></canvas>
<button onclick="capturePhoto()">Capture</button>
