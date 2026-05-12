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

namespace local_course\output;

use core_course_category;
use core_course_list_element;
use local_course\util\course as courseutil;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Catalog renderable.
 *
 * @package     local_course
 * @copyright   2025 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class catalog implements renderable, templatable {
    protected string $search;
    protected int $categoryid;

    public function __construct(string $search, int $categoryid) {
        $this->search = trim($search);
        $this->categoryid = $categoryid;
    }

    public function export_for_template(renderer_base $output): array {
        $baseurl = new moodle_url('/local/course/catalog.php');

        $categories = $this->build_category_filters();
        $rawcourses = $this->fetch_courses();
        $courses = $this->build_course_data($rawcourses);

        return [
            'search'       => $this->search,
            'hassearch'    => !empty($this->search),
            'categoryid'   => $this->categoryid,
            'categories'   => $categories,
            'courses'      => $courses,
            'hascourses'   => !empty($courses),
            'totalcount'   => count($courses),
            'baseurl'      => $baseurl->out(),
            'searchaction' => $baseurl->out(false),
        ];
    }

    private function build_category_filters(): array {
        $filters = [
            [
                'id'     => 0,
                'name'   => get_string('allcourses', 'local_course'),
                'active' => ($this->categoryid === 0),
                'url'    => (new moodle_url('/local/course/catalog.php', ['search' => $this->search]))->out(),
            ],
        ];

        $rootcategory = core_course_category::get(2);

        foreach ($rootcategory->get_children() as $cat) {
            if (!$cat->visible) {
                continue;
            }

            $filters[] = [
                'id'     => $cat->id,
                'name'   => $cat->get_formatted_name(),
                'active' => ($this->categoryid === (int)$cat->id),
                'url'    => (new moodle_url('/local/course/catalog.php', [
                    'search'     => $this->search,
                    'category' => $cat->id,
                ]))->out(),
            ];
        }

        return $filters;
    }

    private function fetch_courses(): array {
        global $DB;

        $params = ['siteid' => SITEID];
        $where = 'c.id != :siteid AND c.visible = 1';

        if (!empty($this->search)) {
            $where .= ' AND (' . $DB->sql_like('c.fullname', ':search', false)
                    . ' OR ' . $DB->sql_like('c.shortname', ':search2', false) . ')';
            $params['search']  = '%' . $DB->sql_like_escape($this->search) . '%';
            $params['search2'] = '%' . $DB->sql_like_escape($this->search) . '%';
        }

        if ($this->categoryid > 0) {
            $categoryids = $this->get_descendant_category_ids($this->categoryid);
            [$insql, $inparams] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'catid');
            $where .= " AND c.category {$insql}";
            $params = array_merge($params, $inparams);
        }

        $sql = "SELECT c.* FROM {course} c WHERE {$where} ORDER BY c.fullname ASC";

        return $DB->get_records_sql($sql, $params, 0, 60);
    }

    private function get_descendant_category_ids(int $categoryid): array {
        $category = core_course_category::get($categoryid, IGNORE_MISSING);
        if (!$category) {
            return [$categoryid];
        }

        $ids = [$categoryid];
        foreach ($category->get_children() as $child) {
            $ids = array_merge($ids, $this->get_descendant_category_ids((int)$child->id));
        }

        return $ids;
    }

    private function build_course_data(array $rawcourses): array {
        $courses = [];

        foreach ($rawcourses as $rawcourse) {
            $element = new core_course_list_element($rawcourse);
            $util    = new courseutil($element);

            $summary = shorten_text(strip_tags($rawcourse->summary ?? ''), 110);
            $url     = new moodle_url('/local/course/index.php', ['id' => $rawcourse->id]);

            $courses[] = [
                'id'           => $rawcourse->id,
                'fullname'     => $element->get_formatted_fullname(),
                'categoryname' => $util->get_category_name(),
                'image'        => $util->get_courseimage(),
                'url'          => $url->out(),
                'summary'      => $summary,
                'hassummary'   => !empty($summary),
            ];
        }

        return $courses;
    }
}
