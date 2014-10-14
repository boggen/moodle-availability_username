<?php

/**
 * User username field condition.
 *
 * @package availability_username
 * @copyright 2014 Newcastle University, based on work by The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_username;

defined('MOODLE_INTERNAL') || die();

/**
 * Username field condition.
 *
 * @package availability_username
 * @copyright 2014 Newcastle University, based on work by The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var string Expected username */
    protected $username = '';

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
	public function __construct($structure) {
        if (isset($structure->v) && is_string($structure->v)) {
            $this->username = $structure->v;
        } else {
            throw new \coding_exception('Missing or invalid ->v for username condition');
        }
    }

    public function save() {
        $result = (object)array('v' => $this->username);
        return $result;
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
		$username = $this->get_cached_username($userid);
        $allow = self::is_condition_met($username, $this->username);
        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        return get_string('requires_username','availability_username');
    }

    protected function get_debug_string() {
        return "username = " . $this->username;
    }

    /**
     * Returns true if the username is the required one, false otherwise.
     *
     * @param string $uservalue the user's value
     * @param string $value the value required
     * @return boolean True if values equal
     */
    protected static function is_condition_met($uservalue, $value) {
        if ($uservalue === false) {
            // If the user value is false this is an instant fail.
            // All user values come from the database as either data or the default.
            // They will always be a string.
            return false;
        }
        $fieldconditionmet = true;
        // Just to be doubly sure it is a string.
		$uservalue = (string)$uservalue;
		$names = preg_split('/\s*,\s*/',$value);
		error_log(json_encode($names));
        if( !in_array($uservalue,$names) ) {
            $fieldconditionmet = false;
        }
        return $fieldconditionmet;
    }

    /**
     * Wipes the static cache (for use in unit tests).
     */
    public static function wipe_static_cache() {
    }

    /**
     * Return a user's username
     *
     * @param int $userid User ID
     * @return string|bool Username, or false if user does not have a username
     */
    protected function get_cached_username($userid) {
        global $USER, $DB, $CFG;
        $iscurrentuser = $USER->id == $userid;
        if (isguestuser($userid) || ($iscurrentuser && !isloggedin())) {
            // Must be logged in and can't be the guest.
            return false;
        }

        // If its the current user than most likely we will be able to get this information from $USER.
        // If its a regular profile field then it should already be available, if not then we have a mega problem.
        // If its a custom profile field then it should be available but may not be. If it is then we use the value
        // available, otherwise we load all custom profile fields into a temp object and refer to that.
        // Noting its not going be great for performance if we have to use the temp object as it involves loading the
        // custom profile field API and classes.
		if ($iscurrentuser) {
			return $USER->username;
        } else {
			return $DB->get_field('user', 'username', array('id' => $userid), MUST_EXIST);
        }
        return false;
    }


    public function is_applied_to_user_lists() {
        // Profile conditions are assumed to be 'permanent', so they affect the
        // display of user lists for activities.
        return true;
    }

    public function filter_user_list(array $users, $not, \core_availability\info $info,
            \core_availability\capability_checker $checker) {
        global $CFG, $DB;

        // Get all users from the list who match the condition.
        list ($sql, $params) = $DB->get_in_or_equal(array_keys($users));

        $values = $DB->get_records_select('user', 'id ' . $sql, $params,
            '', 'id, username');
        $default = '';

        // Filter the user list.
        $result = array();
        foreach ($users as $id => $user) {
            // Get value for user.
            if (array_key_exists($id, $values)) {
                $value = $values[$id]->username;
            } else {
                $value = $default;
            }

            // Check value.
            $allow = $this->is_condition_met($value, $this->username);
            if ($not) {
                $allow = !$allow;
            }
            if ($allow) {
                $result[$id] = $user;
            }
        }
        return $result;
    }
}
