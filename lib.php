<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

/**
 * List of feature supports for the photoupload module.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if yes, null if not set
 */
function photoupload_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:           return true;
        case FEATURE_SHOW_DESCRIPTION:    return true;
        default:                          return null;
    }
}

/**
 * Creates a new instance of the photoupload activity.
 *
 * @param stdClass $data Data from mod_form
 * @param mod_photoupload_mod_form $mform
 * @return int New instance ID
 */
function photoupload_add_instance($data, $mform = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();

    // Insert record into DB table photoupload
    $id = $DB->insert_record('photoupload', $data);

    return $id;
}

/**
 * Updates an existing photoupload instance.
 *
 * @param stdClass $data
 * @param object $mform
 * @return bool true
 */
function photoupload_update_instance($data, $mform = null) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('photoupload', $data);
    return true;
}

/**
 * Deletes an instance of the photoupload activity.
 *
 * @param int $id
 * @return bool
 */
function photoupload_delete_instance($id) {
    global $DB;

    if (!$record = $DB->get_record('photoupload', ['id' => $id])) {
        return false;
    }
    $DB->delete_records('photoupload', ['id' => $record->id]);

    return true;
}
