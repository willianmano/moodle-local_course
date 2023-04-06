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

function local_course_dom_additional_header() {
    return local_course_moove_additional_header();
}

function local_course_before_standard_html_head() {
    global $PAGE, $COURSE, $DB;

    $seocontent = '';

    $tagssql = 'SELECT t.id, t.name
                FROM {tag_instance} ti
                INNER JOIN {tag} t ON t.id = ti.tagid
                WHERE ti.itemtype = :itemtype AND itemid = :courseid AND contextid = :contextid';
    $params = [
        'itemtype' => 'course',
        'courseid' => $COURSE->id,
        'contextid' => $PAGE->context->id
    ];

    $coursetags = 'lms,ava,ead,ensinoadistancia';

    $dbcoursetags = $DB->get_records_sql($tagssql, $params);

    if ($dbcoursetags) {
        $tagsarr = [];
        foreach ($dbcoursetags as $tag) {
            $tagsarr[] = $tag->name;
        }

        $coursetags = implode(',', $tagsarr);
    }

    $courselistelement = new \core_course_list_element($COURSE);

    $coursesupport = new \local_course\util\course($courselistelement);

    $coursename = format_text($COURSE->fullname);

    $courseimg = $coursesupport->get_courseimage();

    $coursesummary = $coursesupport->get_summary();

    // Geral.
    $seocontent .= "<meta name='description' content='{$coursesummary}'>";
    $seocontent .= "<meta name='keywords' content={$coursetags}'>";

    // Open graph.
    $seocontent .= "<meta property='og:locale' content='pt_br'>";
    $seocontent .= "<meta property='og:url' content='{$PAGE->url->out()}' />";
    $seocontent .= "<meta property='og:title' content='{$coursename}' />";
    $seocontent .= "<meta property='og:site_name' content='{$coursename}' />";
    $seocontent .= "<meta property='og:description' content='{$coursesummary}' />";
    $seocontent .= "<meta property='og:image' content='{$courseimg}' />";
    $seocontent .= "<meta property='og:image:type' content='image/jpeg'>";
    $seocontent .= "<meta property='og:image:width' content='1200'>";
    $seocontent .= "<meta property='og:image:height' content='630'>";
    $seocontent .= "<meta property='og:type' content='website'>";

    // Twitter.
    $seocontent .= "<meta name='twitter:title' content='{$coursename}' />";
    $seocontent .= "<meta name='twitter:card' content='summary_large_image' />";
    $seocontent .= "<meta name='twitter:description' content='{$coursesummary}' />";
    $seocontent .= "<meta name='twitter:image' content='{$courseimg}' />";

    return $seocontent;
}
