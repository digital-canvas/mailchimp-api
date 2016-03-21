<?php
namespace DigitalCanvas\Mailchimp\Resource;

use DigitalCanvas\Mailchimp\Collection\PaginatedCollection;

/**
 * Class Members
 *
 * @package DigitalCanvas\Mailchimp
 */
class Members extends AbstractResource
{
    /**
     * Returns a paginated list of members
     *
     * @param array $filter
     *
     * @return PaginatedCollection
     */
    public function getList($list_id, array $filter = [])
    {
        $method   = 'GET';
        $path     = "lists/{$list_id}/members";
        $params   = [];
        $response = $this->api->sendRequest($path, $method, $params, $filter);
        $body     = json_decode($response->getBody()->getContents(), true);

        return new PaginatedCollection($body['members'], $body['total_items']);
    }

    /**
     * Returns a member by email address
     *
     * @param string $list_id
     * @param string $email
     *
     * @return array
     */
    public function getByEmail($list_id, $email)
    {
        $subscriber_hash = $this->getSubscriberHash($email);

        return $this->getDetails($list_id, $subscriber_hash);
    }

    /**
     * Returns details for a single member
     *
     * @param string $list_id
     * @param string $subscriber_hash md5 hash of lowercase email address
     *
     * @return array
     */
    public function getDetails($list_id, $subscriber_hash)
    {
        $method   = 'GET';
        $path     = "lists/{$list_id}/members/{$subscriber_hash}";
        $response = $this->api->sendRequest($path, $method);
        $body     = json_decode($response->getBody()->getContents(), true);

        return $body;
    }

    /**
     * Creates a new member
     *
     * @param string $list_id
     * @param string $email
     * @param string $status
     * @param array $options
     *
     * @return array
     */
    public function create($list_id, $email, $status = 'subscribed', array $options = [])
    {
        $method                   = 'POST';
        $path                     = "lists/{$list_id}/members";
        $options                  = array_merge([
            'email_type' => 'html',
        ], $options);
        $options['status']        = $status;
        $options['email_address'] = $email;
        $response                 = $this->api->sendRequest($path, $method, $options);

        $member = json_decode($response->getBody()->getContents(), true);

        return $member;
    }

    /**
     * Unsubscribes a member from a list
     *
     * @param string $list_id
     * @param string $email
     *
     * @return array
     */
    public function unsubscribe($list_id, $email)
    {
        $subscriber_hash = $this->getSubscriberHash($email);
        $method          = 'PATCH';
        $path            = "lists/{$list_id}/members/{$subscriber_hash}";
        $options         = [
            'status' => 'unsubscribed'
        ];
        $response        = $this->api->sendRequest($path, $method, $options);

        $member = json_decode($response->getBody()->getContents(), true);

        return $member;
    }

    /**
     * Re-subscribe a previously unsubscribed member
     *
     * @param string $list_id
     * @param string $email
     *
     * @return array
     */
    public function resubscribe($list_id, $email)
    {
        $subscriber_hash = $this->getSubscriberHash($email);
        $method          = 'PATCH';
        $path            = "lists/{$list_id}/members/{$subscriber_hash}";
        $options         = [
            'status' => 'subscribed'
        ];
        $response        = $this->api->sendRequest($path, $method, $options);

        $member = json_decode($response->getBody()->getContents(), true);

        return $member;
    }

    /**
     * Updates a member
     *
     * @param string $list_id
     * @param string $email
     * @param array $options
     *
     * @return array
     */
    public function update($list_id, $email, array $options = [])
    {
        $subscriber_hash = $this->getSubscriberHash($email);
        $method          = 'PATCH';
        $path            = "lists/{$list_id}/members/{$subscriber_hash}";

        $response = $this->api->sendRequest($path, $method, $options);

        $member = json_decode($response->getBody()->getContents(), true);

        return $member;
    }

    /**
     * @param string $email
     *
     * @return string
     */
    public function getSubscriberHash($email)
    {
        return md5(strtolower($email));
    }

}