<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * User support class.
 *
 * @package    local_course
 * @copyright  2020 Willian Mano <willianmano@conecti.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course\util;

defined('MOODLE_INTERNAL') || die();

/**
 * User class.
 *
 * @copyright  2020 Willian Mano <willianmano@conecti.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
    /**
     * Returns the user picture
     *
     * @param null $user
     * @param int $imgsize
     *
     * @return \moodle_url
     * @throws \coding_exception
     */
    public function get_user_picture($user = null, $imgsize = 100) {
        global $USER, $PAGE;

        if (!$user) {
            $user = $USER;
        }

        $userimg = new \user_picture($user);

        $userimg->size = $imgsize;

        return $userimg->get_url($PAGE);
    }
}
