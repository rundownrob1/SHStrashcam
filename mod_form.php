<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_photoupload_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // Name
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Intro
        $this->standard_intro_elements();

        // Add standard course module elements (availability, completion, etc.)
        $this->standard_coursemodule_elements();

        // Add standard buttons
        $this->add_action_buttons();
    }
}
