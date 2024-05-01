<?php

namespace local_course\external;

use \core\context;
use \core_external\external_api;
use \core_external\external_value;
use \core_external\external_single_structure;
use \core_external\external_function_parameters;

/**
 * Badge criteria external api class.
 *
 * @package     local_course
 * @copyright   2022 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class mylearning extends external_api {
    /**
     * Get my learning parameters
     *
     * @return external_function_parameters
     */
    public static function get_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id', VALUE_REQUIRED)
        ]);
    }

    /**
     * Get my learning method
     *
     * @param integer $contextid
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function get($contextid) {
        global $PAGE;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::get_parameters(), ['contextid' => $contextid]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);
        $PAGE->set_context($context);

        $mylearning = new \local_course\util\mylearning();

        $courses = $mylearning->get_last_accessed_courses(3);

        return [
            'courses' => json_encode($courses)
        ];
    }

    /**
     * Get my learning return fields
     *
     * @return external_single_structure
     */
    public static function get_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_value(PARAM_TEXT, 'Return courses')
            )
        );
    }
}
