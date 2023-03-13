<?php

/**
 * Event listener for dispatched event
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace local_course\observers;

defined('MOODLE_INTERNAL') || die;

use core\event\base as baseevent;

class course {
    public static function viewed(baseevent $event) {
        if ($event->courseid == 1) {
            return;
        }

        if (is_enrolled($event->get_context(), $event->relateduserid)) {
            return;
        }

        // Avoid add points for teachers, admins, anyone who can edit course.
        if (has_capability('moodle/course:update', $event->get_context(), $event->relateduserid)) {
            return;
        }

        redirect(new \moodle_url('/local/course/index.php', ['id' => $event->courseid]));
    }
}
