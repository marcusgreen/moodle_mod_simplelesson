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
 * simplelesson module admin settings and defaults
 *
 * @package    mod
 * @subpackage simplelesson
 * @copyright 2015 Justin Hunt, modified 2018 Richard Jones https://richardnz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/mod/simplelesson/lib.php');

if ($ADMIN->fulltree) {
  
    $settings->add(new admin_setting_configcheckbox(MOD_SIMPLELESSON_FRANKY . 
            '/enablereports',
            get_string('enablereports', MOD_SIMPLELESSON_LANG), 
            get_string('enablereports_desc',MOD_SIMPLELESSON_LANG),'0'));
}
