<?php
class program_course
{
    private static $instance = null;

    public static function get_instance()
    {        
        if( !is_object(self::$instance) ){
            self::$instance = new static;
        }
        return self::$instance;
    }

    public function get_content($course_id)
    {
        $core_course_external = new core_course_external();        
        return $core_course_external->get_course_contents($course_id);
    }
}
