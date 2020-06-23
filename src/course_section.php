<?php
class course_section
{
    public static function update_name($course_id, $section_id, $section_name)
    {
        global $DB;
        $section = $DB->get_record('course_sections', array('course' => $course_id, 'section' => $section_id), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
        $data = [
            "name" => $section_name,
            "id" => $section_row->id,
            "mform_isexpanded_id_availabilityconditions" => 0,
            "submitbutton" => "Save changes",
            "summarytrust" => 0,
            "summary" => "",
            "summaryformat" => "1",
            "availability" => "{\"op\":\"&\",\"c\":[],\"showc\":[]}"
        ];
        course_update_section($course, $section, $data);
    }

    public static function get_section_data($course_content, $section_name)
    {
        $section_content = null;
        foreach ($course_content as $data) {
            if($data['name'] == $section_name){
                $section_content['id'] = $data['id'];
                $section_content['name'] = $data['name'];
                $section_content['section'] = $data['section'];
                $section_content['modules'] = self::filter_modules($data['modules']);                
            }
        }
        return $section_content;
    }

    public static function filter_modules($modules)
    {
        global $DB;
        $mods = [];        
        if ( is_array($modules) && count($modules) > 0 ) {
            foreach ($modules as $module) {                                
                $m['id'] = $module['id'];
                $m['name'] = trim($module['name']);                    
                $mods[] = $m;
            }
        }
        return $mods;
    }

    public static function get_module_by_name($modules, $module_name)
    {
        $module = null;
        foreach ($modules as $mod) {
            if (trim($mod['name']) == trim($module_name)) {
                $module = $mod;                
            }
        }
        return $module;
    }
}
