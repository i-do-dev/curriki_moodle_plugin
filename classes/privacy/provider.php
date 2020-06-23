<?php
/**
 * Privacy Subsystem implementation for local_curriki_moodle_plugin
 *
 * @package    local_curriki_moodle_plugin
 * @copyright  2020 CurrikiStudio <info@curriki.org> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_curriki_moodle_plugin\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for local_curriki_moodle_plugin implementing null_provider.
 *
 * @package    local_curriki_moodle_plugin
 * @copyright  2020 CurrikiStudio <info@curriki.org> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin does not store any personal user data.
    \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
