<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

/**
 * Example function to send an image (from a local file) to a third-party service.
 *
 * @param string $filepath Path to the file on the local server
 * @return bool True on success, false on failure
 */
function photoupload_send_to_thirdparty($filepath) {
    // Insert your real endpoint here:
    $endpoint = 'https://example.com/api/upload';

    if (!file_exists($filepath)) {
        mtrace("File $filepath does not exist.");
        return false;
    }

    // Using Moodle's built-in curl class or pure PHP cURL is possible.
    // Here is a quick native cURL example:
    $curlfile = new CURLFile($filepath, 'image/png', 'upload.png');
    $postfields = [
        'file' => $curlfile
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // If your service requires authentication, set headers, tokens, etc.:
    // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer <token>']);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($httpcode === 200 && !$error) {
        return true;
    } else {
        mtrace("Error sending file: $error, code: $httpcode, response: $response");
        return false;
    }
}
