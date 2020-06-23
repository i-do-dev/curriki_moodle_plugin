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
 * External webservice template.
 *
 * @package   local_curriki_moodle_plugin
 * @copyright 2020 CurrikiStudio <info@curriki.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once "includes.php";

/**
 * External webservice functions.
 *
 * @package   local_curriki_moodle_plugin
 * @copyright 2020 CurrikiStudio <info@curriki.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_curriki_moodle_plugin_external extends external_api {

    public static function create_playlist_parameters() {
        return new external_function_parameters(
            array(
                'entity_name' => new external_value( PARAM_TEXT, 'entity name'),
                'entity_type' => new external_value(PARAM_TEXT, 'entity type like program/playlist/activity'),
                'entity_id' => new external_value(PARAM_TEXT, 'entity id'),
                'parent_name' => new external_value(PARAM_TEXT, 'parent name'),
                'parent_type' => new external_value(PARAM_TEXT, 'parent type')
            )
        );
    }

    public static function create_playlist($entity_name, $entity_type, $entity_id, $parent_name, $parent_type) {
        $syscontext = context_system::instance();
        require_capability('moodle/site:config', $syscontext);

        $params = self::validate_parameters(self::create_playlist_parameters(),
            array(
                'entity_name' => $entity_name,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'parent_name' => $parent_name,
                'parent_type' => $parent_type
            )
        );

        global $DB;     
        $parent_data['parent_name'] = $params['parent_name'];
        $parent_data['parent_type'] = $params['parent_type'];
        
        $entity_data['entity_name'] = $params['entity_name'];
        $entity_data['entity_type'] = $params['entity_type'];
        $entity_data['entity_id']   = $params['entity_id'];

        /***** Step-1 fetc/create course against program name *****/
        $course = $DB->get_record('course', array('fullname' => trim($parent_data['parent_name'])), '*');
        if(!is_object($course)){
            $new_course = new stdClass();
            $new_course->fullname = trim($parent_data['parent_name']);
            $new_course->shortname = strtolower( implode( "-",  explode( " ", trim($parent_data['parent_name']) ) ) );
            $new_course->categoryid = 1;
            $new_course_rows = core_course_external::create_courses([(array)$new_course]);
            $course = $DB->get_record('course', array('id' => $new_course_rows[0]['id']), '*');
            course_create_section($course, 0);
        }        
        
        $course_id = $course->id;

        /***** Step-2 Update Playlist Name *****/
        course_section::update_name($course_id, SECTION_ID_FOR_PLAYLIST, SECTION_NAME_FOR_PLAYLIST);

        /***** Step-3 Create Playlist LTI *****/
        $program_course = program_course::get_instance();        
        $course_content = $program_course->get_content($course_id);
        $section_data = course_section::get_section_data($course_content, SECTION_NAME_FOR_PLAYLIST);
        $section_module = course_section::get_module_by_name($section_data['modules'], $entity_data['entity_name']);
        
        $lti_tool_config = $DB->get_record('lti_types', array('name' => LTI_TOOL_NAME), '*');
        if( is_object($course) && is_object($lti_tool_config) && is_null($section_module) ){
            lti_module::set_data($entity_data, $lti_tool_config);
            $lti_module = add_moduleinfo(lti_module::$data, $course);            
            $playlist_lti->id = $lti_module->id;
            $playlist_lti->name = $lti_module->name;
        }else{            
            $playlist_lti->id = $section_module['id'];
            $playlist_lti->name = $section_module['name'];
        }      
        
        $obj = new stdClass();
        $obj->status = "success";        
        $obj->data = $playlist_lti;

        $result[] = $obj;        
        return $result;
    }

    public static function create_playlist_returns() {                
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'status' => new external_value(PARAM_TEXT, 'success'),
                    'data' => new external_single_structure([
                            'id' => new external_value(PARAM_INT, 0),
                            'name' => new external_value(PARAM_TEXT, 'none')
                        ])
                )
            )
        );
    }

}
