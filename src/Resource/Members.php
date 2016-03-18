<?php
namespace DigitalCanvas\Mailchimp\Resource;

/**
 * Class Members
 *
 * @package DigitalCanvas\Mailchimp
 */
class Members extends AbstractResource
{
    /**
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
        $options = array_merge([
            'email_type' => 'html',
        ], $options);
        $options['status']        = $status;
        $options['email_address'] = $email;
        $response                 = $this->api->sendRequest($path, $method, $options);

        $member = json_decode($response->getBody()->getContents(), true);

        return $member;

    }
}