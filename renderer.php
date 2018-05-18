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
 * Custom renderer for output of pages
 *
 * @package    mod_simplelesson
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

use mod_simplelesson\local\pages;
defined('MOODLE_INTERNAL') || die();

class mod_simplelesson_renderer extends plugin_renderer_base {

    /**
     * Returns the header for the module.
     *
     * @param string $lessontitle the module name.
     * @param string $coursename the course name.
     * @return string header output
     */
    public function header($lessontitle, $coursename) {

        // Header setup.
        $this->page->set_title($this->page->course->shortname.": ".$coursename);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();

        $output .= $this->output->heading($lessontitle);

        return $output;
    }
    /**
     * Returns the header for the module.
     *
     * @param string $simplelesson the module name.
     * @param int $simplelessonid the module instance id.
     * @return string header output
     */
    public function fetch_intro($simplelesson, $simplelessonid) {

        $output = $this->output->box(format_module_intro(
                'simplelesson', $simplelesson, $simplelessonid),
                'generalbox mod_introbox', 'simplelessonintro');

        return $output;
    }
    /**
     * Returns the editing links for the intro (home) page
     *
     * @return string editing links
     */
    public function fetch_editing_links($courseid, $simplelessonid) {

        $html = html_writer::start_div(
                'mod_simplelesson_edit_links');
        $links = array();

        // Page management.
        $url = new moodle_url('/mod/simplelesson/edit.php',
                array('courseid' => $courseid,
                'simplelessonid' => $simplelessonid));
        $links[] = html_writer::link($url,
                get_string('manage_pages', 'mod_simplelesson'));
        
        // Question management.
        $url = new moodle_url('/mod/simplelesson/edit_questions.php',
                array('courseid' => $courseid,
                      'simplelessonid' => $simplelessonid));
        $links[] = html_writer::link($url,
                get_string('manage_questions', 'mod_simplelesson'));
        
        $html = $html .= html_writer::alist($links, null, 'ul');
        
        $html .= html_writer::end_div();

        return $html;
    }
    /**
     * Returns a link to add a page
     *
     * @param int $courseid
     * @param int $simplelessonid
     * @param int $sequence the page sequence number
     * @return string add link html
     */
    public function add_first_page_link($courseid, $simplelessonid, $sequence) {

        $html = '<p>' . get_string('no_pages',
                'mod_simplelesson') . '</p>';

        $url = new moodle_url('/mod/simplelesson/add_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $simplelessonid,
                'sequence' => $sequence));

        $html .= html_writer::link($url, get_string('add_page', 'mod_simplelesson'));

        return $html;
    }
    /**
     * Returns the html to show the number of pages.
     *
     * @param int $numpages the number of pages
     * @return string html
     */
    public function fetch_num_pages($numpages) {

        return get_string('numpages', 'mod_simplelesson', $numpages);
    }

    /**
     * Show the current page.
     *
     * @param object $data object instance of current page
     * @return string html representation of page object
     */
    public function show_page($data) {

        $html = '';
        // Show page content.
        $html .= html_writer::start_div('mod_simplelesson_content');
        $html .= $this->output->heading($data->pagetitle, 4);
        $html .= $data->pagecontents;
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Returns the link to the a content page.
     *
     * @param string $courseid
     * @param string $moduleid
     * @param string $pagesequence
     * @return string
     */
    public function fetch_firstpage_link($courseid,
            $simplelessonid, $pageid) {

        $html = '';
        $html .= html_writer::start_div('mod_simplelesson_nav_links');

        $url = new moodle_url('/mod/simplelesson/showpage.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $simplelessonid,
                    'pageid' => $pageid));
        $html .= html_writer::link($url,
                    get_string('firstpagelink', 'mod_simplelesson'));

        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Show the home, previous and next links.
     *
     * @param int $courseid
     * @param object $data - the current page object
     * @return string html representation of navigation links
     */
    public function show_page_nav_links($courseid, $data) {

        $links = array();

        $html = html_writer::start_div('mod_simplelesson_page_links');
        // Home link.
        $returnview = new moodle_url('/mod/simplelesson/view.php',
                array('simplelessonid' => $data->simplelessonid));
        $links[] = html_writer::link($returnview,
                    get_string('homelink',  'mod_simplelesson'));

        // Previous page (if any).
        if ($data->prevpageid != 0) {
            $prevurl = new moodle_url('/mod/simplelesson/showpage.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $data->simplelessonid,
                    'pageid' => $data->prevpageid));
            $links[] = html_writer::link($prevurl,
                    get_string('gotoprevpage', 'mod_simplelesson'));
        } else {
            // Just put out the link text.
            $links[] = get_string('gotoprevpage', 'mod_simplelesson');
        }

        // Next page (if any).
        if ($data->nextpageid != 0) {
            $nexturl = new moodle_url('/mod/simplelesson/showpage.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $data->simplelessonid,
                    'pageid' => $data->nextpageid));
            $links[] = html_writer::link($nexturl,
                 get_string('gotonextpage', 'mod_simplelesson'));
        } else {
            // Just put out the link text.
            $links[] = get_string('gotonextpage', 'mod_simplelesson');
        }

        $html .= html_writer::alist($links, null, 'ul');
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Show the page editing links
     *
     * @param int $courseid
     * @param int $data object the page data
     * @return string html representation of editing links
     */
    public function show_page_edit_links($courseid, $data) {

        $links = array();

        $html = html_writer::start_div('mod_simplelesson_edit_links');
        
        // Add edit and delete links.
        $url = new moodle_url('/mod/simplelesson/add_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $data->simplelessonid,
                'sequence' => $data->sequence + 1));
        $links[] = html_writer::link($url,
                get_string('gotoaddpage', 'mod_simplelesson'));

        $url = new moodle_url('/mod/simplelesson/edit_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $data->simplelessonid,
                'sequence' => $data->sequence));
        $links[] = html_writer::link($url,
                get_string('gotoeditpage', 'mod_simplelesson'));

        $url = new moodle_url('/mod/simplelesson/delete_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $data->simplelessonid,
                'sequence' => $data->sequence,
                'returnto' => 'view'));
        $links[] = html_writer::link($url,
                get_string('gotodeletepage', 'mod_simplelesson'));

        // Page management.
        $url = new moodle_url('/mod/simplelesson/edit.php',
                array('courseid' => $courseid,
                      'simplelessonid' => $data->simplelessonid));
        $links[] = html_writer::link($url,
                get_string('manage_pages', 'mod_simplelesson'));

        // Question management.
        $url = new moodle_url('/mod/simplelesson/edit_questions.php',
                array('courseid' => $courseid,
                      'simplelessonid' => $data->simplelessonid));
        $links[] = html_writer::link($url,
                get_string('manage_questions', 'mod_simplelesson'));

        $html .= html_writer::alist($links, null, 'ul');

        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Returns a list of pages and editing actions
     *
     * @param string $courseid - current course
     * @param object $simplelessonid - current instance id
     * @param object $context  - module context
     * @return string html link
     */
    public function page_management($courseid, $simplelesson, $context) {

        $activityname = format_string($simplelesson->name, true);
        $this->page->set_title($activityname);

        $table = new html_table();
        $table->head = array(
                get_string('sequence', 'mod_simplelesson'),
                get_string('pagetitle', 'mod_simplelesson'),
                get_string('prevpage', 'mod_simplelesson'),
                get_string('nextpage', 'mod_simplelesson'),
                get_string('actions', 'mod_simplelesson'));
        $table->align = array('left', 'left', 'left', 'left', 'left');
        $table->wrap = array('', 'nowrap', '', 'nowrap', '');
        $table->tablealign = 'center';
        $table->cellspacing = 0;
        $table->cellpadding = '2px';
        $table->width = '80%';
        $table->data = array();
        $numpages = pages::count_pages($simplelesson->id);
        $sequence = 1;

        while ($sequence <= $numpages) {
            $pageid = pages::get_page_id_from_sequence($simplelesson->id, $sequence);
            $url = new moodle_url('/mod/lesson/edit.php', array(
                'courseid'     => $courseid,
                'simplelessonid'   => $simplelesson->id
            ));
            $data = array();
            $alldata = pages::get_page_record($pageid);
            // Change page id's to sequence numbers for display.
            $prevpage = pages::get_page_sequence_from_id($alldata->prevpageid);
            $nextpage = pages::get_page_sequence_from_id($alldata->nextpageid);
            $data[] = $alldata->sequence;
            $data[] = $alldata->pagetitle;
            $data[] = $prevpage;
            $data[] = $nextpage;

            if (has_capability('mod/simplelesson:manage', $context)) {
                $data[] = $this->page_action_links(
                        $courseid, $simplelesson->id, $alldata);
            } else {
                $data[] = '';
            }
            $table->data[] = $data;
            $sequence++;
        }

        return html_writer::table($table);
    }
    /**
     * Returns HTML to display action links for a page
     *
     * @param $courseid - current course
     * @param $simplelessonid - current module instance id
     * @param $data - a simplelesson page record
     * @return string, a set of page action links
     */
    public function page_action_links(
            $courseid, $simplelessonid, $data) {
        global $CFG;
        $actions = array();

        $url = new moodle_url('/mod/simplelesson/edit_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $simplelessonid,
                'sequence' => $data->sequence));

        $label = get_string('gotoeditpage', 'mod_simplelesson');

        // Standard Moodle icons used here.
        $img = $this->output->pix_icon('t/edit', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Preview page = show page.
        $url = new moodle_url('/mod/simplelesson/showpage.php',
                array('courseid' => $courseid,
                'simplelessonid' => $simplelessonid,
                'pageid' => $data->id));
        $label = get_string('showpage', 'mod_simplelesson');
        $img = $this->output->pix_icon('t/preview', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Delete page.
        $url = new moodle_url('/mod/simplelesson/delete_page.php',
                array('courseid' => $courseid,
                'simplelessonid' => $simplelessonid,
                'sequence' => $data->sequence,
                'returnto' => 'edit'));
        $label = get_string('gotodeletepage', 'mod_simplelesson');
        $img = $this->output->pix_icon('t/delete', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Move page up.
        if ($data->sequence != 1) {
            $url = new moodle_url('/mod/simplelesson/edit.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $simplelessonid,
                    'sequence' => $data->sequence,
                    'action' => 'move_up'));
            $label = get_string('move_up', 'mod_simplelesson');
            $img = $this->output->pix_icon('t/up', $label);
            $actions[] = html_writer::link($url, $img, array('title' => $label));
        }

        // Move down.
        if (!pages::is_last_page($data)) {
            $url = new moodle_url('/mod/simplelesson/edit.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $simplelessonid,
                    'sequence' => $data->sequence,
                    'action' => 'move_down'));
            $label = get_string('move_down', 'mod_simplelesson');
            $img = $this->output->pix_icon('t/down', $label);
            $actions[] = html_writer::link($url, $img, array('title' => $label));
        }
        return implode(' ', $actions);
    }
    /**
     * Returns HTML to display a report tab
     *
     * @param $context - module contex
     * @param $id - course module id
     * @return string, a set of tabs
     */
    public function show_reports_tab($courseid, $simplelessonid) {

        $tabs = $row = $inactive = $activated = array();
        $currenttab = '';
        $viewpage = new moodle_url('/mod/simplelesson/view.php',
        array('simplelessonid' => $simplelessonid));
        $reportspage = new moodle_url('/mod/simplelesson/reports.php',
        array('courseid' => $courseid, 'simplelessonid' => $simplelessonid));

        $row[] = new tabobject('view', $viewpage,
                get_string('viewtab', 'mod_simplelesson'));
        $row[] = new tabobject('reports', $reportspage,
                get_string('reportstab', 'mod_simplelesson'));

        $tabs[] = $row;

        print_tabs($tabs, $currenttab, $inactive, $activated);

    }
    /**
     * Returns HTML to a basic report
     *
     * @param $data - a set of module fields
     * @return string, html table
     */
    public function show_basic_report($records) {

        $table = new html_table();
        $table->head = array(
                get_string('moduleid', 'mod_simplelesson'),
                get_string('simplelessonname', 'mod_simplelesson'),
                get_string('title', 'mod_simplelesson'),
                get_string('timecreated', 'mod_simplelesson'));
        $table->align = array('left', 'left', 'left');
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->tablealign = 'left';
        $table->cellspacing = 0;
        $table->cellpadding = '2px';
        $table->width = '80%';
        foreach ($records as $record) {
            $data = array();
            $data[] = $record->id;
            $data[] = $record->name;
            $data[] = $record->title;
            $data[] = $record->timecreated;
            $table->data[] = $data;
        }

        return html_writer::table($table);
    }

    /**
     * Returns the html for the page index
     * module's instance settings page
     * @param array $page_links for the lesson page index
     * @return string
     */
    public function fetch_index($pagelinks) {

        $html = html_writer::start_div(
                'mod_simplelesson_page_index_container');
        $html .= $this->output->heading(
                get_string('page_index_header', 'mod_simplelesson'), 4, 'main');
        $html .= html_writer::start_div('mod_simplelesson_page_index');
        $html .= html_writer::alist($pagelinks, null, 'ul');
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        return $html;
    }
    /**
     * Returns a list of questions and editing actions
     *
     * @param string $courseid
     * @param int $simplelessonid
     * @param object array questions 
     * @return string html link
     */
    public function question_management($courseid, 
            $simplelessonid, $questions) {
        $table = new html_table();
        $table->head = array(
        get_string('qnumber', 'mod_simplelesson'),
        get_string('question_name', 'mod_simplelesson'),
        get_string('question_text', 'mod_simplelesson'),
        get_string('pagetitle', 'mod_simplelesson'),
        get_string('setpage', 'mod_simplelesson'));
        $table->align = 
                array('left', 'left', 'left', 'left', 'left');
        $table->wrap = array('nowrap', '', '', 'nowrap', 'nowrap');
        $table->tablealign = 'center';
        $table->cellspacing = 0;
        $table->cellpadding = '2px';
        $table->width = '80%';
        $table->data = array();
        foreach ($questions as $question) {
            $data = array();
            $data[] = $question->qid;
            $data[] = $question->name;
            if (strlen($question->questiontext) > 100) {
                $data[] = substr($question->questiontext, 
                        0, 95) . '...';
            } else {
                $data[] = $question->questiontext;        
            }
            if ($question->pageid == 0) {
                $data[] = '-';
            } else {
                $data[] = pages::get_page_title($question->pageid);       
            }
            $url = new moodle_url(
                    '/mod/simplelesson/edit_questions.php',
                    array('courseid' => $courseid,
                    'simplelessonid' => $simplelessonid,
                    'action' => 'edit',
                    'actionitem' => $question->qid));
            $data[] = html_writer::link($url, 
                    get_string('setpage',
                    'mod_simplelesson'));
            $table->data[] = $data;            
        } 
        
        return html_writer::table($table);
    }
    /**
     * Returns the html for question management page
     * @param int $simplelessonid 
     * @param int $courseid 
     * @return string, html list of links
     */
    public function fetch_question_page_links($courseid, 
        $simplelessonid) {
        
        $html = '';
        $links = array();
        
        $html .= html_writer::start_div('mod_simplelesson_edit_links');
        
        // Home link
        $url = new moodle_url('/mod/simplelesson/view.php', 
                array('simplelessonid' => $simplelessonid));
        $links[] = html_writer::link($url,get_string('homelink', 'mod_simplelesson'));
        
        // add link
        $url = new moodle_url('/mod/simplelesson/add_question.php', 
                array('courseid' => $courseid, 
                'simplelessonid' => $simplelessonid));
        $links[] = html_writer::link($url,get_string('add_question', 'mod_simplelesson'));
        
        // Page management
        $url = new moodle_url('/mod/simplelesson/edit.php', 
                array('courseid' => $courseid, 
                'simplelessonid' => $simplelessonid));
        $links[] = html_writer::link($url, 
                get_string('manage_pages', 'mod_simplelesson'));
        $html .= html_writer::alist($links, null, 'ul');
        $html .= html_writer::end_div();
        return $html;
    } 
    /**
     * Display a dummy question on the page
     * This is a placeholder for the review stage
     * @param int $qustionid - the id of the question 
     * @return string, html representing the dummy text
     */
    public function dummy_question($questionid) {
        $html = '';
        $html .= html_writer::start_div(
                MOD_SIMPLELESSON_CLASS . '_page_question');
        $html .= get_string('dummy_question', 'mod_simplelesson');
        $html .= html_writer::end_div();
        return $html;
    } 
    /**
     *
     * render the question form on a page
     *
     */
    public function render_question_form(
            $actionurl, $options, $slot, $quba, 
            $deferred, $starttime) {
        $headtags = '';
        $headtags .= $quba->render_question_head_html($slot);
        $headtags .= question_engine::initialise_js();  
        
        // Start the question form.
        $html = html_writer::start_tag('form', 
                array('method' => 'post', 'action' => $actionurl,
                'enctype' => 'multipart/form-data', 
                'id' => 'responseform'));
        $html .= html_writer::start_tag('div');
        $html .= html_writer::empty_tag('input', 
                array('type' => 'hidden', 
                'name' => 'sesskey', 'value' => sesskey()));
        $html .= html_writer::empty_tag('input', 
                array('type' => 'hidden', 
                'name' => 'slots', 'value' => $slot));
        $html .= html_writer::empty_tag('input', 
                array('type' => 'hidden', 
                'name' => 'starttime', 'value' => $starttime));
        $html .= html_writer::end_tag('div');
        
        // Output the question.
        $html .= $quba->render_question($slot, $options, $slot);
        
        // Finish the question form.
        $html .= html_writer::start_tag('div');
        
        // Action button on the form.
        // We only need this for deferred feedback I think
        // So we won't implement for now
        /*
        if ($deferred) {
            $html .= html_writer::empty_tag(
                    'input', array('type' => 'submit',
                    'name' => 'submit', 
                    'value' => 
                    get_string('submit', 'mod_simplelesson')));
        }
        // put this button option in mod form (allow terminate)
        $html .= html_writer::empty_tag(
                'input', array('type' => 'submit',
                'name' => 'finish', 'value' => 
                get_string('stoplesson', 'mod_simplelesson')));
        */
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');
        return $html;  
    }
}