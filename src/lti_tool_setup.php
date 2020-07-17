<?php

class lti_tool_setup
{
    public static function create_tool()
    {
        $type = new stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;

        $lti_tool = new stdClass();
        $lti_tool->lti_typename = LTI_TOOL_NAME;
        $lti_tool->lti_toolurl =  TSUGI_HOST . "/mod/curriki/";
        $lti_tool->lti_description =  "";
        $lti_tool->lti_ltiversion = "1.3.0";
        $lti_tool->lti_keytype = "RSA_KEY";
        $lti_tool->lti_publickey = "";
        $lti_tool->lti_initiatelogin = TSUGI_HOST . "/lti/oidc_login";
        $lti_tool->lti_redirectionuris = TSUGI_HOST . "/lti/oidc_launch";
        $lti_tool->lti_customparameters = "";
        $lti_tool->lti_coursevisible = "2";
        $lti_tool->typeid = 0;
        $lti_tool->lti_launchcontainer =  "3";
        $lti_tool->lti_contentitem =  "0";
        $lti_tool->oldicon =  "";
        $lti_tool->lti_icon = "";
        $lti_tool->lti_secureicon =   "";
        $lti_tool->ltiservice_gradesynchronization =  "2";
        $lti_tool->ltiservice_memberships =   "1";
        $lti_tool->ltiservice_toolsettings =  "1";
        $lti_tool->lti_sendname = "1";
        $lti_tool->lti_sendemailaddr ="1";
        $lti_tool->lti_acceptgrades = "1";
        $lti_tool->lti_organizationid_default = "SITEID";
        $lti_tool->lti_organizationid =   "";
        $lti_tool->lti_organizationurl =  "";
        $lti_tool->tab =  "";
        $lti_tool->course = 1;
        $lti_tool->submitbutton = "Save changes";
        
        lti_add_type($type, $lti_tool);
    }

    public static function update_tool($data)
    {
        $type = new stdClass();
        $type->id = $data['typeid'];

        $lti_tool = new stdClass();
        $lti_tool->lti_typename = LTI_TOOL_NAME;
        $lti_tool->lti_toolurl =  TSUGI_HOST . "/mod/curriki/";
        $lti_tool->lti_description =  "";
        $lti_tool->lti_ltiversion = "1.3.0";
        $lti_tool->lti_clientid = $data['lti_clientid'];
        $lti_tool->lti_keytype = "RSA_KEY";
        $lti_tool->lti_publickey = $data['lti_publickey'];
        $lti_tool->lti_initiatelogin = TSUGI_HOST . "/lti/oidc_login/".$data['issuer_guid'];
        $lti_tool->lti_redirectionuris = TSUGI_HOST . "/lti/oidc_launch";
        $lti_tool->lti_customparameters = "";
        $lti_tool->lti_coursevisible = "2";
        $lti_tool->typeid = $data['typeid'];
        $lti_tool->lti_launchcontainer =  "3";
        $lti_tool->lti_contentitem =  "0";
        $lti_tool->oldicon =  "";
        $lti_tool->lti_icon = "";
        $lti_tool->lti_secureicon =   "";
        $lti_tool->ltiservice_gradesynchronization =  "2";
        $lti_tool->ltiservice_memberships =   "1";
        $lti_tool->ltiservice_toolsettings =  "1";
        $lti_tool->lti_sendname = "1";
        $lti_tool->lti_sendemailaddr ="1";
        $lti_tool->lti_acceptgrades = "1";
        $lti_tool->lti_organizationid_default = "SITEID";
        $lti_tool->lti_organizationid =   "";
        $lti_tool->lti_organizationurl =  "";
        $lti_tool->tab =  "";
        $lti_tool->course = 1;
        $lti_tool->submitbutton = "Save changes";
        
        lti_update_type($type, $lti_tool);
    }
}
