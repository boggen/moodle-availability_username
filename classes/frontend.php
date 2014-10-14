<?php
/**
 * Front-end class.
 *
 * @package availability_username
 * @copyright 2014 Newcastle University, based on work by The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_username;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_username
 * @copyright 2014 Newcastle University, based on work by The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    protected function get_javascript_strings() {
        return array('label_username','title_username','comma_separated');
    }

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {

        return array();
    }
}
