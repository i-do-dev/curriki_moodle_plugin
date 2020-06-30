<?php
define('SECTION_ID_FOR_PLAYLIST',1);
define('SECTION_NAME_FOR_PLAYLIST', 'Playlists');
define('LTI_TOOL_NAME', 'CurrikiStudioLTI');
define('MOODLE_WEB_SERVICE_TOKEN', '96d73cbb7e98e7932e6f247d78239793');

if( !(defined('COURSE_MAX_LOGS_PER_PAGE') && defined('COURSE_MAX_RECENT_PERIOD')) ){
    define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // Records.
    define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds.
}