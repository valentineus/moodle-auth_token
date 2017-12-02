<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication Plugin: Authorization by tokens.
 *
 * @package   auth_token
 * @copyright 2017 "Valentin Popov" <info@valentineus.link>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined("MOODLE_INTERNAL") || die();

require_once($CFG->dirroot . "/admin/tool/managertokens/lib.php");
require_once($CFG->libdir  . "/authlib.php");

/**
 * Plugin user authentication plugin.
 */
class auth_plugin_token extends auth_plugin_base {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = "token";
        $this->config = get_config("auth_token");
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_token() {
        debugging("Use of class name as constructor is deprecated", DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param  string  $username The username
     * @param  string  $password The password
     * @return boolean           Authentication success or failure.
     */
    public function user_login($username, $password) {
        global $CFG, $DB;

        if ($user = $DB->get_record("user", array("username" => $username, "mnethostid" => $CFG->mnet_localhost_id))) {
            return validate_internal_user_password($user, $password);
        }

        return false;
    }

    /**
     * Updates the user's password.
     * Called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean              Password updated success or failure.
     */
    public function user_update_password($user, $password) {
        $user = get_complete_user_data("id", $user->id);
        return update_internal_user_password($user, $password);
    }

    /**
     * Indicates if password hashes should be stored in local moodle database.
     *
     * @return boolean
     */
    public function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return boolean
     */
    public function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return boolean
     */
    public function can_change_password() {
        return false;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return boolean
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return boolean
     */
    public function can_be_manually_set() {
        return true;
    }

    /**
     * Hook for overriding behaviour before going to the login page.
     */
    public function pre_loginpage_hook() {
        $this->loginpage_hook();
    }

    /**
     * Hook for overriding behaviour of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     */
    public function loginpage_hook() {
        if ($token = $this->definition_token()) {
            if ($user = $this->definition_user($token)) {
                complete_user_login($user);
                $this->additional_actions($token);
            }
        }
    }

    /**
     * Executes additional conditions and redirects the user.
     *
     * @param object $token
     */
    private function additional_actions($token) {
        if ($token->extendedaction == "redirect") {
            $this->redirect_user($token->extendedoptions);
        }

        $this->redirect_user();
    }

    /**
     * Indicates the transmitted token, if any.
     *
     * @return object
     */
    private function definition_token() {
        $key   = optional_param("token", "", PARAM_NOTAGS);
        $token = tool_managertokens_activate_token($key);
        return $token;
    }

    /**
     * Identifies the user who owns the token.
     *
     * @param  object $token
     * @return object
     */
    private function definition_user($token) {
        $user = false;

        if ($token->targettype == "user") {
            $user = core_user::get_user($token->targetid);
        }

        return $user;
    }

    /**
     * Redirects the user.
     *
     * @param string $url
     */
    private function redirect_user($url = "") {
        global $CFG, $SESSION;

        $wantsurl = optional_param("wantsurl", "", PARAM_URL);
        $redirect = $CFG->wwwroot;

        if (!empty($url)) {
            $redirect = new moodle_url($url);
        } else if (isset($SESSION->wantsurl)) {
            $redirect = $SESSION->wantsurl;
        } else if (!empty($wantsurl)) {
            $redirect = $wantsurl;
        }

        redirect($redirect);
    }
}