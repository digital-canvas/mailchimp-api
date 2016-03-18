<?php
namespace DigitalCanvas\Mailchimp\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class PaginatedCollection
 *
 * @package DigitalCanvas\Mailchimp\Collection
 */
class PaginatedCollection extends ArrayCollection implements Collection
{
    private $total_items;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array $elements
     * @param null $total_items
     */
    public function __construct(array $elements = array(), $total_items = null)
    {
        parent::__construct($elements);
        $this->total_items = $total_items;
    }

    /**
     * @return int|null
     */
    public function getTotalItems()
    {
        return ! is_null($this->total_items) ? $this->total_items : $this->count();
    }
}