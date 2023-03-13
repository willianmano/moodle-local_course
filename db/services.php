<?php

/**
 * Game changer services definition
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_course_get_my_learning' => [
        'classname' => 'local_course\external\mylearning',
        'classpath' => 'local/course/classes/external/mylearning.php',
        'methodname' => 'get',
        'description' => 'Get user learning',
        'type' => 'read',
        'ajax' => true
    ],
];
