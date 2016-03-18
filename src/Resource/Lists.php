<?php
namespace DigitalCanvas\Mailchimp\Resource;

use DigitalCanvas\Mailchimp\Collection\PaginatedCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Lists
 *
 * @package DigitalCanvas\Mailchimp
 */
class Lists extends AbstractResource
{

    /**
     * @param array $filter
     *
     * @return PaginatedCollection
     */
    public function getList(array $filter = [])
    {
        $method   = 'GET';
        $path     = 'lists';
        $params   = [];
        $response = $this->api->sendRequest($path, $method, $params, $filter);
        $body     = json_decode($response->getBody()->getContents(), true);

        return new PaginatedCollection($body['lists'], $body['total_items']);
    }

    /**
     * @param string $list_id
     *
     * @return array
     */
    public function getDetails($list_id)
    {
        $method   = 'GET';
        $path     = "lists/{$list_id}";
        $response = $this->api->sendRequest($path, $method);
        $body     = json_decode($response->getBody()->getContents(), true);
        return $body;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function create(array $list)
    {
        $method   = 'POST';
        $path     = 'lists';
        $response = $this->api->sendRequest($path, $method, $list);
        $body     = json_decode($response->getBody()->getContents(), true);
        return $body;
    }

    /**
     * @param string $list_id
     * @param array $list
     *
     * @return array
     */
    public function update($list_id, array $list)
    {
        $method   = 'PATCH';
        $path     = "lists/{$list_id}";
        $response = $this->api->sendRequest($path, $method, $list);
        $body     = json_decode($response->getBody()->getContents(), true);
        return $body;
    }

    /**
     * @param string $list_id
     *
     * @return array
     */
    public function delete($list_id)
    {
        $method   = 'DELETE';
        $path     = "lists/{$list_id}";
        $response = $this->api->sendRequest($path, $method);
        $body     = json_decode($response->getBody()->getContents(), true);
        return $body;
    }

}