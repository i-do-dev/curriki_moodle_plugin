<?php

class moodle_rest_clientasdfasf
{
    private $moodle_rest = null;

    public function __construct() {        
        global $CFG;        
        //$moodle_web_service_url = $CFG->wwwroot. "/webservice/rest/server.php";        
        $moodle_web_service_url = "http://host.docker.internal". "/webservice/rest/server.php";        
        $this->moodle_rest = new MoodleRest($moodle_web_service_url, MOODLE_WEB_SERVICE_TOKEN);
        //$this->moodle_rest->setReturnFormat($this->moodle_rest::RETURN_ARRAY);        
        $this->moodle_rest->setDebug();        
        $this->core_course_get_contents(2);
    }

    public function core_course_get_contents($courseid)
    {
        $arr = $this->moodle_rest->request('core_course_get_contents', array('courseid' => $courseid) ,  $this->moodle_rest::METHOD_POST);        
        //var_dump($arr);
        die;
    }
}
