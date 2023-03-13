<?php

/**
 * Plugin lib.
 *
 * @package     local_course
 * @copyright   2022 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

function local_course_moove_additional_header() {
    global $PAGE;

    if (isguestuser() || !isloggedin()) {
        return false;
    }

    $renderer = $PAGE->get_renderer('local_course');

    $contentrenderable = new \local_course\output\mylearning($PAGE->context);

    return $renderer->render($contentrenderable);
}
