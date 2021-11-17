<?php
/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package   local_curriki_moodle_plugin
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = array(
    'local_curriki_moodle_plugin_create_playlist' => array(
        'classname'   => 'local_curriki_moodle_plugin_external',
        'methodname'  => 'create_playlist',
        'classpath'   => 'local/curriki_moodle_plugin/externallib.php',
        'description' => 'CurrikiStudio - Create a playlist under Playlists topic.',
        'type'        => 'read',
    ),
    'local_curriki_moodle_plugin_fetch_course' => array(
        'classname'   => 'local_curriki_moodle_plugin_external',
        'methodname'  => 'fetch_course',
        'classpath'   => 'local/curriki_moodle_plugin/externallib.php',
        'description' => 'CurrikiStudio - Fetch a course for playlists.',
        'type'        => 'read',
    ),
    'local_curriki_moodle_plugin_fetch_project' => array(
        'classname'   => 'local_curriki_moodle_plugin_external',
        'methodname'  => 'fetch_project',
        'classpath'   => 'local/curriki_moodle_plugin/externallib.php',
        'description' => 'CurrikiStudio - Fetch a project ID using Moodle course ID.',
        'type'        => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'CurrikiStudio' => array(
        'functions' => array ('local_curriki_moodle_plugin_create_playlist', 'local_curriki_moodle_plugin_fetch_course', 'local_curriki_moodle_plugin_fetch_project'),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);
