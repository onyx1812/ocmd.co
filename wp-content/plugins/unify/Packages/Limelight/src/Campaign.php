<?php

namespace CodeClouds\Limelight;

use CodeClouds\Limelight\Limelight;

/**
 * Retrieve/Update/Create Campaign.
 *
 * Only one aspect to this class.
 * {@inheritdoc}.In addition, the Campaign class can't be inherited
 *
 * @final
 *
 * @package  Limelight
 */
final class Campaign extends Limelight
{

    /**
     * Show the important data about a campaign,
     * based on the campaign_id submitted.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function campaignView($params)
    {
        $this->section = 'membership';
        $this->method  = 'campaign_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Display all currently active campaigns.
     *
     * @return void
     */
    public function campaignFindActive()
    {
        $this->section = 'membership';
        $this->method  = 'campaign_find_active';
        $this->fields  = [];
        $this->__post();
    }

    /**
     * Retrieve product details.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productIndex($params)
    {

        $this->section = 'membership';
        $this->method  = 'product_index';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve attribute information related to several products.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productAttributeIndex($params)
    {

        $this->section = 'membership';
        $this->method  = 'product_attribute_index';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Retrieve information about a specific bundle.
     *
     * @return void
     */
    public function productBundleIndex()
    {
        $this->section = 'membership';
        $this->method  = 'product_bundle_index';
        $this->fields  = [];
        $this->__post();
    }

    /**
     * Retrieve information about a retrieve
     * information about a specific bundle.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productBundleView($params)
    {
        $this->section = 'membership';
        $this->method  = 'product_bundle_view';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Get started taking template products that are existing
     * in your offers and slightly tweaking them  through the API.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productCopy($params)
    {
        $this->section = 'membership';
        $this->method  = 'product_copy';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Create new products.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productCreate($params)
    {
        $this->section = 'membership';
        $this->method  = 'product_create';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Delete a product.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productDelete($params)
    {
        $this->section = 'membership';
        $this->method  = 'product_delete';
        $this->fields  = $params;
        $this->__post();
    }

    /**
     * Update the information on a product.
     *
     * @param  array $params
     * This parameters consists required fields in key value pair.
     *
     * @return void
     */
    public function productUpdate($params)
    {
        $this->section = 'membership';
        $this->method  = 'product_update';
        $this->fields  = $params;
        $this->__post();
    }

}
