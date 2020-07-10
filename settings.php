<?php
require_once "includes.php";

// handle create External tool
if ( isset($_GET['action']) && $_GET['action'] == 'setup_lti_tool') {
            
    lti_tool_setup::create_tool();
    $lti_types = lti_filter_tool_types(lti_get_lti_types(), LTI_TOOL_STATE_CONFIGURED);
    $lti_tool_settings = lti_module::get_type_by_name($lti_types, LTI_TOOL_NAME);    
    if (!is_null($lti_tool_settings)) {
        $tsugi_data = tsugi_client::register_platform($lti_tool_settings);        
        $data['typeid'] = $lti_tool_settings->id;
        $data['lti_clientid'] = $lti_tool_settings->clientid;
        $data['lti_publickey'] = $tsugi_data->issure->lti13_pubkey;
        lti_tool_setup::update_tool($data);
    }
    
    $url = $CFG->wwwroot . '/admin/settings.php?section=local_curriki_moodle_plugin';
    header("Location: $url");
    
}


// Ensure the configurations for this site are set
if( $hassiteconfig ){

    // Create the new settings page
    $curriki_settings = new admin_settingpage( 'local_curriki_moodle_plugin', 'CurrikiGo Settings' );

    // Create 
    $ADMIN->add( 'localplugins', $curriki_settings );    
    
    $lti_types = lti_filter_tool_types(lti_get_lti_types(), LTI_TOOL_STATE_CONFIGURED);
    $lti_tool_settings = lti_module::get_type_by_name($lti_types, LTI_TOOL_NAME);    
    
    if (!is_null($lti_tool_settings)) {
        global $CFG;        
        $external_tool_link = '<a href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=update&id='.$lti_tool_settings->id.'&sesskey='.sesskey().'"><i class="icon fa fa-cog fa-fw"></i>'. $lti_tool_settings->name .'</a>';
        $curriki_settings->add( new admin_setting_heading('currikitoolinfo', 'Configured External Tool', $external_tool_link ) );
    }else {
        $external_tool_link = '<a class="btn btn-primary" href="'.$CFG->wwwroot.'/admin/settings.php?section=local_curriki_moodle_plugin&action=setup_lti_tool">'. 'Setup CurrikiStudioLTI Tool' .'</a>';
        $curriki_settings->add( new admin_setting_heading('currikitoolinfo', 'External Tool Configuration', $external_tool_link ) );
    }
    
}
