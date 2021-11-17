<?php
/**
 * External webservice.
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
 */

class local_curriki_moodle_plugin_external extends external_api {

    public static function fetch_course_parameters() {
        return new external_function_parameters(
            array( 'name' => new external_value(PARAM_TEXT, 'course name'),
                   'project_id' => new external_value(PARAM_INT, 0) 
                )
        );
    }

    public static function fetch_course($name, $project_id){
        $params = self::validate_parameters(self::fetch_course_parameters(), array('name' => $name, 'project_id' => $project_id));
        $section_modules = [];
        //die($project_id);
        global $DB;
        $projectcourse = $DB->get_record('local_curriki_moodle_plugin', array('projectid' => trim($params['project_id'])), '*');
        if(is_object($projectcourse)){
            //$course = $DB->get_record('course', ["fullname" => trim($params['name'])], '*');
            $course = $DB->get_record('course', array('id' => $projectcourse->courseid), '*');

            if(is_object($course)){
                $program_course = program_course::get_instance();        
                $course_content = $program_course->get_content($course->id);
                $section_data = course_section::get_section_data($course_content, SECTION_NAME_FOR_PLAYLIST);
                foreach ($section_data['modules'] as $key => $module) {
                    $section_modules[] = $module['name'];
                }
            }
        }//die($course->id);
        return ['course' => $course->fullname, 'courseid' => $course->id, 'playlists' => $section_modules];
    }

    public static function fetch_course_returns() {                

        return new external_single_structure(
            array(
                'course' => new external_value(PARAM_TEXT, 'course name'),
                'courseid' => new external_value(PARAM_INT, 0),
                'playlists' => new external_multiple_structure(new external_value(PARAM_TEXT, 'playlist name'))
            )
        );
    }

    public static function create_playlist_parameters() {
        return new external_function_parameters(
            array(
                'entity_name' => new external_value( PARAM_TEXT, 'entity name'),
                'entity_type' => new external_value(PARAM_TEXT, 'entity type like program/playlist/activity'),
                'entity_id' => new external_value(PARAM_TEXT, 'entity id'),
                'parent_name' => new external_value(PARAM_TEXT, 'parent name'),
                'parent_type' => new external_value(PARAM_TEXT, 'parent type'),
                'project_id' => new external_value(PARAM_INT, 0)
            )
        );
    }

    public static function create_playlist($entity_name, $entity_type, $entity_id, $parent_name, $parent_type, $project_id) {
        $syscontext = context_system::instance();
        require_capability('moodle/site:config', $syscontext);

        $params = self::validate_parameters(self::create_playlist_parameters(),
            array(
                'entity_name' => $entity_name,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'parent_name' => $parent_name,
                'parent_type' => $parent_type,
                'project_id' => $project_id
            )
        );

        global $DB;     
        $parent_data['parent_name'] = $params['parent_name'];
        $parent_data['parent_type'] = $params['parent_type'];
        $parent_data['project_id'] = $params['project_id'];
        
        $entity_data['entity_name'] = $params['entity_name'];
        $entity_data['entity_type'] = $params['entity_type'];
        $entity_data['entity_id']   = $params['entity_id'];

        /***** Step-1 fetc/create course against program name *****/
        $projectcourse = $DB->get_record('local_curriki_moodle_plugin', array('projectid' => trim($parent_data['project_id'])), '*');
        
        
        if(!is_object($projectcourse)){
            $new_course = new stdClass();
            $new_course->fullname = trim($parent_data['parent_name']);
            $new_course->shortname = strtolower( implode( "-",  explode( " ", trim($parent_data['parent_name']) ) ) );
            $new_course->categoryid = 1;
            $new_course_rows = core_course_external::create_courses([(array)$new_course]);
            
            //add mapping record into project course mapping table
            $add_project = new stdClass();
            $add_project->projectid = trim($parent_data['project_id']);
            $add_project->projecttitle = trim($parent_data['parent_name']);
            $add_project->courseid = $new_course_rows[0]['id'];
            $DB->insert_record('local_curriki_moodle_plugin', $add_project);
               
            
            $course = $DB->get_record('course', array('id' => $new_course_rows[0]['id']), '*');
            course_create_section($course, 0);
        }
        else{
            $course = $DB->get_record('course', array('id' => $projectcourse->courseid), '*');
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

    public static function fetch_project_parameters() {
        return new external_function_parameters(
            array( 'course_id' => new external_value(PARAM_INT, 0) )
        );
    }

    public static function fetch_project($course_id){
        $params = self::validate_parameters(self::fetch_project_parameters(), array('course_id' => $course_id));
        global $DB;
        $courseproject = $DB->get_record('local_curriki_moodle_plugin', array('courseid' => trim($params['course_id'])), '*');
        if(is_object($courseproject)){
            return ['projectid' => $courseproject->projectid];
        }
        else{
            return ['projectid' => NULL];
        }
    }

    public static function fetch_project_returns() {                

        return new external_single_structure(
            array(
                'projectid' => new external_value(PARAM_INT, 0)
            )
        );
    }    

}
