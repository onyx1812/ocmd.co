<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Limelight;

/**
 * Retrieve/Update/Create Customer.
 *
 * Only one aspect to this class.
 * {@inheritdoc}.In addition, the Customer class can't be inherited
 *
 * @final
 *
 * @package  Limelight
 */
final class Customer extends Limelight
{
    /**
     * Show the valid product info.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function customerFindActiveProduct($params)
    {
        $this->section = 'membership';
        $this->method  = 'customer_find_active_product';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * View data about a customer in the LimeLight platform.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function customerView($params)
    {
        $this->section = 'membership';
        $this->method  = 'customer_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve a list of customer ids that match a set of criteria.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function customerFind($params)
    {
        $this->section = 'membership';
        $this->method  = 'customer_find';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * View data about a prospect.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function prospectView($params)
    {
        $this->section = 'membership';
        $this->method  = 'prospect_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update a range of values on a prospect record.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function prospectUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'prospect_update';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve a list of prospect ids.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function prospectFind($params)
    {
        $this->section = 'membership';
        $this->method  = 'prospect_find';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Create a new prospect record.
     *
     * @param  array $params This parameters consists required fields in key value pair.
     * @return void
     */
    public function newProspect($params)
    {
        $this->section = 'transact';
        $this->method  = 'NewProspect';
        $this->fields  = $params;
        $this->__post();
    }

}
