<?php
namespace DigitalCanvas\Mailchimp\Resource;

use DigitalCanvas\Mailchimp\Mailchimp;

/**
 * Class AbstractResource
 *
 * @package DigitalCanvas\Mailchimp
 */
abstract class AbstractResource
{
    /**
     * @var Mailchimp
     */
    protected $api;

    /**
     * Lists constructor.
     *
     * @param Mailchimp $api
     */
    public function __construct(Mailchimp $api)
    {
        $this->api = $api;
    }
}