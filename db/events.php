<?php

/**
 * Local course preview events definition
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_viewed',
        'callback' => '\local_course\observers\course::viewed',
        'internal' => false
    ],
];
