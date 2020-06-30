<?php

require_once "includes.php";

// Ensure the configurations for this site are set
if( $hassiteconfig ){

    // Create the new settings page
    $curriki_settings = new admin_settingpage( 'local_curriki_moodle_plugin', 'CurrikiStudio Settings' );

    // Create 
    $ADMIN->add( 'localplugins', $curriki_settings );    
    
    $lti_types = lti_get_lti_types();    
    $lti_tool_settings = lti_module::get_type_by_name($lti_types, LTI_TOOL_NAME);
    $lti_tool_settings = is_array($lti_tool_settings) && count($lti_tool_settings) > 0 ? array_values($lti_tool_settings)[0] : null;
    
    if (!is_null($lti_tool_settings)) {
        global $CFG;        
        $external_tool_link = '<a href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=update&id='.$lti_tool_settings->id.'&sesskey='.sesskey().'">'. $lti_tool_settings->name .'</a>';
        $curriki_settings->add( new admin_setting_heading('currikitoolinfo', 'Configured External Tool', $external_tool_link ) );
    }else {
        $curriki_settings->add( new admin_setting_heading('currikitoolinfo', 'Configured External Tool', "Unable to find External Tool. Please Uninstall and Install plugin again." ) );
    }
    
}
