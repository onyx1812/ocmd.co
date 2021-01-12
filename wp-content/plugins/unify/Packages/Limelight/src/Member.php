<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Limelight;

/**
 * Checking API Credentials.
 *
 * Only one aspect to this class.
 * {@inheritdoc}.In addition, the Member class can't be inherited
 *
 * @final
 *
 * @package  Limelight
 */
final class Member extends Limelight
{
    /**
     * Checking API Credentials Valid Or Not.
     *
     * @return void
     */
    public function validateCredentials()
    {
        $this->section = 'membership';
        $this->method  = 'validate_credentials';
        $this->fields  = [];
        $this->__post();
    }

    /**
     * Create a new member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberCreate($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_create';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Delete a member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberDelete($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_delete';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * View information about a member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberView($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Log in a member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberLogin($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_login';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Log out a member.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberLogout($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_logout';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Check if a member session is active.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberCheckSession($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_check_session';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Generate a temporary password for a member that has forgotten their password.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberForgotPassword($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_forgot_password';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Reset a password from a temporary password.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberResetPassword($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_reset_password';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve the event_id for events that are attached to membership.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function memberEventIndex($params)
    {
        $this->section = 'membership';
        $this->method  = 'member_event_index';
        $this->fields  = $params;
        $this->__post();
    }

}
