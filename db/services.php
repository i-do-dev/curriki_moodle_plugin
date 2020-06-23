<?php
/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package   local_curriki_moodle_plugin
 * @copyright 2020 CurrikiStudio <info@curriki.org> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = array(
    'local_curriki_moodle_plugin_create_playlist' => array(
        'classname'   => 'local_curriki_moodle_plugin_external',
        'methodname'  => 'create_playlist',
        'classpath'   => 'local/curriki_moodle_plugin/externallib.php',
        'description' => 'CurrikiStudio Plugin APIs to create program/playlist/activities and publish to LTI.',
        'type'        => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'CurrikiStudio' => array(
        'functions' => array ('local_curriki_moodle_plugin_create_playlist'),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);
