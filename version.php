<?php
// This file is part of Moodle - http://moodle.org/
// It is a minimal version.php for our custom mod_photoupload plugin.

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_photoupload';     // Full plugin name (frankenstyle).
$plugin->version   = 2025030600;            // YYYYMMDDHH (date + two-digit increment).
$plugin->release   = 'v1.0';
$plugin->requires  = 2021051700;            // Requires this Moodle version (3.11 = 2021051700).
$plugin->maturity  = MATURITY_ALPHA;        // This is a development release.
