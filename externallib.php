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
                'project_id' => new external_value(PARAM_INT, 0),
                'tool_url' => new external_value(PARAM_TEXT, 'tool url',VALUE_OPTIONAL),
                'org_name' => new external_value(PARAM_TEXT, 'organization name'),
                'grade_name' => new external_value(PARAM_TEXT, 'grade name'),
                'subject_name' => new external_value(PARAM_TEXT, 'subject name'),
                'activities' => new external_value(PARAM_TEXT, 'activities')
            )
        );
    }

    public static function create_playlist($entity_name, $entity_type, $entity_id, $parent_name, $parent_type, $project_id, $tool_url='', $org_name, $grade_name, $subject_name, $activities) {
        $syscontext = context_system::instance();
        require_capability('moodle/site:config', $syscontext);

        $params = self::validate_parameters(self::create_playlist_parameters(),
            array(
                'entity_name' => $entity_name,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'parent_name' => $parent_name,
                'parent_type' => $parent_type,
                'project_id' => $project_id,
                'tool_url' => $tool_url,
                'org_name' => $org_name,
                'grade_name' => $grade_name,
                'subject_name' => $subject_name,
                'activities' => $activities
            )
        );

        global $DB;     
        $parent_data['parent_name'] = $params['parent_name'];
        $parent_data['parent_type'] = $params['parent_type'];
        $parent_data['project_id'] = $params['project_id'];
        
        $entity_data['entity_name'] = $params['entity_name'];
        $entity_data['entity_type'] = $params['entity_type'];
        $entity_data['entity_id'] = $params['entity_id'];
        $entity_data['tool_url'] = $params['tool_url'];
        
        $category_data['org_name'] = $params['org_name'];
        $category_data['grade_name'] = $params['grade_name'];
        $category_data['subject_name'] = $params['subject_name'];

        /***** Step-1 fetc/create course against program name *****/
        $projectcourse = $DB->get_record('local_curriki_moodle_plugin', array('projectid' => trim($parent_data['project_id'])), '*');
        if ($projectcourse) {
            $course_rel = $DB->get_record('course', array('id' => $projectcourse->courseid), '*');
            if (!$course_rel) {
                $DB->delete_records('local_curriki_moodle_plugin', array('id' => $projectcourse->id));
                $projectcourse = null;
            }
        }

        if(!is_object($projectcourse)){
            /* create category */
            $org_category = new stdClass();
            $org_category->name = $category_data['org_name'];
            if (strval($org_category->name) !== '' && $DB->record_exists('course_categories', array('name' => $org_category->name, 'parent'=>0))) {
                $org_cate = $DB->get_record('course_categories', array('name' => $org_category->name, 'parent'=>0), '*');
            }else{
                $org_cate = core_course_category::create($org_category);
            }
    
            $grade_category = new stdClass();
            $grade_category->name = $category_data['grade_name'];
            $grade_category->parent = $org_cate->id;
            if (strval($grade_category->name) !== '' && $DB->record_exists('course_categories', array('name' => $grade_category->name, 'parent'=>$org_cate->id))) {
                $grade_cate = $DB->get_record('course_categories', array('name' => $grade_category->name, 'parent'=>$org_cate->id), '*');
            }else{
                $grade_cate = core_course_category::create($grade_category);
            }
            
            $subject_category = new stdClass();
            $subject_category->name = $category_data['subject_name'];
            $subject_category->parent = $grade_cate->id;
            if (strval($subject_category->name) !== '' && $DB->record_exists('course_categories', array('name' => $subject_category->name, 'parent'=>$grade_cate->id))) {
                $subject_cate = $DB->get_record('course_categories', array('name' => $subject_category->name, 'parent'=>$grade_cate->id), '*');
            }else{
                $subject_cate = core_course_category::create($subject_category);
            }            
            
            $new_course = new stdClass();
            $new_course->fullname = trim($parent_data['parent_name']);
            $new_course->shortname = strtolower( implode( "-",  explode( " ", trim($parent_data['parent_name']) ) ) )."-".time();
            $new_course->categoryid = $subject_cate->id;
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

        //=============== Setup Playlist Section ======================
        global $DB;
        $playlistSection = $DB->get_record('course_sections', array('course' => $course_id, 'name' => $entity_data['entity_name']), '*');
        if (!$playlistSection) {
            $playlistSection = course_create_section($course);
            course_update_section($course_id, $playlistSection, array('name' => $entity_data['entity_name']));
        }
        $playlist_activities = json_decode(html_entity_decode($params['activities']));
        $project_course = program_course::get_instance();        
        $course_content = $project_course->get_content($course_id);
        $lti_tool_config = $DB->get_record('lti_types', array('name' => LTI_TOOL_NAME), '*'); // Get External Tool configuration
        if(!$lti_tool_config) {
            $parsed_host = parse_url($entity_data['tool_url'])['host'];
            $parsed_path = rtrim(parse_url($entity_data['tool_url'])['path'], '/');
            $tool_parsed_url =  $parsed_host . $parsed_path;
            $lti_tool_config = $DB->get_record_select('lti_types', $DB->sql_like('baseurl', '?'), array('%'.$tool_parsed_url.'%'));
        }
        
        $entity_data_pl = array();
        $entity_data_pl['entity_name'] = $entity_data['entity_name'];
        $entity_data_pl['entity_type'] = $entity_data['entity_type'];
        $entity_data_pl['entity_id'] = $entity_data['entity_id'];
        $entity_data_pl['section'] = $entity_data['section'];
        
        $playlist_lti = new \stdClass();
        foreach ($playlist_activities as $paylist_activity) {
            
            $section_data = course_section::get_section_data($course_content, $entity_data['entity_name']);
            $section_module = course_section::get_module_by_name($section_data['modules'], $paylist_activity->title);
            $addActivity = is_null($section_module) ? true : false;
            $activity_title = $paylist_activity->title;
            
            if (!$addActivity) {
                $activity_existing_module = $section_module;
                $activities_existing_titles = preg_grep(
                    "/$paylist_activity->title \(\d+\)/i",
                    array_map(function ($module) { return html_entity_decode($module["name"]); }, $section_data['modules'])
                );
                $count = 0;
                if (is_array($activities_existing_titles) && count($activities_existing_titles)) {
                    $title_counts = array_map(function ($title) { 
                            preg_match("/\(\d+\)/i", $title, $matches);
                            $numberBracket = $matches && is_array($matches) ? $matches[0] : '(0)';
                            preg_match("/\d+/i", $numberBracket, $matchesNum);
                            return $matchesNum && is_array($matchesNum) ? intval($matchesNum[0]) : 0; 
                        }, 
                        $activities_existing_titles
                    );
                    $title_counts = array_values($title_counts);
                    rsort($title_counts);
                    $count = $title_counts && is_array($title_counts) && count($title_counts) > 0 ? ($title_counts[0] + 1) : 0;
                    $activity_title = $activity_title . " ($count)";
                    $addActivity = true;
                } else {
                    $count = 1;
                    $activity_title = $activity_title . " ($count)";
                    $addActivity = true;
                }
            }
            
            if( is_object($course) && is_object($lti_tool_config) && $addActivity ) {
                $entity_data['module'] = $DB->get_record('modules', array('name' => 'lti'), '*')->id;
                $entity_data['entity_name'] = $activity_title;
                $entity_data['entity_type'] = "activity";
                $entity_data['entity_id'] = $paylist_activity->id;
                $entity_data['section'] = $section_data["section"];
                lti_module::set_data($entity_data, $lti_tool_config);
                $lti_module = add_moduleinfo(lti_module::$data, $course);
                $entity_data['entity_name'] = $entity_data_pl['entity_name'];
                $entity_data['entity_type'] = $entity_data_pl['entity_type'];
                $entity_data['entity_id'] = $entity_data_pl['entity_id'];
                $entity_data['section'] = $entity_data_pl['section'];
            }

        }

        $playlist_lti->id = $entity_data_pl['entity_id'];
        $playlist_lti->name = $entity_data_pl['entity_name'];
        
        /*
        // Step-2 Update Playlist Name
        course_section::update_name($course_id, SECTION_ID_FOR_PLAYLIST, SECTION_NAME_FOR_PLAYLIST);

        // Step-3 Create Playlist LTI
        $program_course = program_course::get_instance();        
        $course_content = $program_course->get_content($course_id);
        $section_data = course_section::get_section_data($course_content, SECTION_NAME_FOR_PLAYLIST);
        $section_module = course_section::get_module_by_name($section_data['modules'], $entity_data['entity_name']);
        
        if(empty($tool_url))
            $lti_tool_config = $DB->get_record('lti_types', array('name' => LTI_TOOL_NAME), '*');
        else{
            $parsed_host = parse_url($entity_data['tool_url'])['host'];
            $parsed_path = rtrim(parse_url($entity_data['tool_url'])['path'], '/');
            $tool_parsed_url =  $parsed_host . $parsed_path;
            $lti_tool_config = $DB->get_record_select('lti_types', $DB->sql_like('baseurl', '?'), array('%'.$tool_parsed_url.'%'));
        }
        if( is_object($course) && is_object($lti_tool_config) && is_null($section_module) ){          
            $entity_data['module'] = $DB->get_record('modules', array('name' => 'lti'), '*')->id;
            lti_module::set_data($entity_data, $lti_tool_config);
            $lti_module = add_moduleinfo(lti_module::$data, $course);            
            $playlist_lti->id = $lti_module->id;
            $playlist_lti->name = $lti_module->name;
        }else{              
            $playlist_lti->id = $section_module['id'];
            $playlist_lti->name = $section_module['name'];
        }      
        */
        
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
    
    public static function get_user_projects_parameters() {
        return new external_function_parameters(
            array( 'user_id' => new external_value(PARAM_INT, 0) )
        );
    }

    public static function get_user_projects($user_id){
        $params = self::validate_parameters(self::get_user_projects_parameters(), array('user_id' => $user_id));         
        global $DB; 
        $sql = 'select projectid from {user_enrolments} ue 
                join {enrol} e on e.id = ue.enrolid 
                join {local_curriki_moodle_plugin} lcmp on lcmp.courseid = e.courseid
                where ue.userid=? AND ue.status=?';
        $studentprojects = $DB->get_records_sql($sql, [trim($params['user_id']), 0]);
        $stdntprojectids = array();
        //$studentprojects = $DB->get_record('local_curriki_moodle_plugin', array('courseid' => trim($params['user_id'])), '*');
        //if(is_array($studentprojects)){
            foreach ($studentprojects as $project) {
                $stdntprojectid = array();
                $stdntprojectid['projectid'] = $project->projectid;
                $stdntprojectids[] = $stdntprojectid;
            }
            
            return $stdntprojectids;
        //}
        //else{
        //    return ['projectids' => NULL];
        //}
    }

    public static function get_user_projects_returns() {         
        
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'projectid' => new external_value(PARAM_INT, 'Project ID')
                )
            )
        );
    }

}
