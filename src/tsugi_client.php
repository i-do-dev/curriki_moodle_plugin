<?php

class tsugi_client
{
    public static function register_platform($lti_tool_settings)
    {
        $typeconfig = get_tool_type_config($lti_tool_settings);
        $post_data = [
            "issuer_key" => $typeconfig['platformid'],
            "issuer_client" => $typeconfig['clientid'] ,
            "lti13_keyset_url" => $typeconfig['publickeyseturl'],
            "lti13_token_url" =>  $typeconfig['accesstokenurl'],
            "lti13_oidc_auth" =>  $typeconfig['authrequesturl'],
            "deploy_key" => $typeconfig['deploymentid']
        ];
    
        $request_url = TSUGI_HOST . "/mod/curriki/index.php/register-platform";
        curl_helper::prepare($request_url, $post_data);
        curl_helper::exec_post();
        $response = curl_helper::get_response();
        $response = json_decode($response);
        return $response;    
    }
}
