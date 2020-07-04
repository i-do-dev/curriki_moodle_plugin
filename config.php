<?php

// Please set TSUGI_HOST and TSUGI_HOST_API for your Tsugi server. Mostly both constant would be the same.
define('TSUGI_HOST', 'http://localhost/tsugi');
define('TSUGI_HOST_API', 'http://host.docker.internal/tsugi');

define('SECTION_ID_FOR_PLAYLIST',1);
define('SECTION_NAME_FOR_PLAYLIST', 'Playlists');
define('LTI_TOOL_NAME', 'CurrikiStudioLTI');

if( !(defined('COURSE_MAX_LOGS_PER_PAGE') && defined('COURSE_MAX_RECENT_PERIOD')) ){
    define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // Records.
    define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds.
}