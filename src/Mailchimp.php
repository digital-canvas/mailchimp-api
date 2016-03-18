<?php
namespace DigitalCanvas\Mailchimp;

use DigitalCanvas\Mailchimp\Entity\SubscriberList;
use DigitalCanvas\Mailchimp\Resource\Lists;
use DigitalCanvas\Mailchimp\Resource\Members;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;

/**
 * Class Mailchimp
 *
 * @package DigitalCanvas\Mailchimp
 *
 * @property Lists $lists
 * @property Members $members
 */
class Mailchimp
{
    /**
     * The Mailchimp API version
     */
    const API_VERSION = '3.0';

    /**
     * @var string
     */
    protected $api_key;

    /**
     * @var string
     */
    protected $data_center;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $base_url;

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var array
     */
    private $mapper = [
        'lists' => Lists::class,
        'members' => Members::class,
    ];

    /**
     * Mailchimp constructor.
     *
     * @param ClientInterface $client
     * @param string $api_key
     */
    public function __construct(ClientInterface $client, $api_key)
    {
        $this->setApiKey($api_key);
        $this->client = $client;
    }

    /**
     * Sets the mailchimp data center
     *
     * @param string $data_center
     *
     * @return self
     */
    protected function setDataCenter($data_center = 'us1')
    {
        $this->data_center = $data_center;
        $this->base_url    = sprintf(
            'https://%s.api.mailchimp.com/%s/',
            $data_center,
            static::API_VERSION
        );

        return $this;
    }

    /**
     * Sets the api key
     *
     * @param string $api_key The api key
     *
     * @return self
     */
    public function setApiKey($api_key)
    {
        if (preg_match("/[a-f0-9]{32}-(us\\d)/i", $api_key, $matches)) {
            $this->api_key = $api_key;
            $this->setDataCenter($matches[1]);
        } else {
            throw new \InvalidArgumentException("Not a valid Mailchimp API key.");
        }

        return $this;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendRequest($path, $method = 'GET', array $params = [], array $query = [])
    {
        $uri     = $this->base_url . $path;
        $options = [
            RequestOptions::AUTH    => ['username', $this->api_key],
            RequestOptions::HEADERS => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        if ($params) {
            $options[RequestOptions::JSON] = $params;
        }
        if ($query) {
            $options[RequestOptions::QUERY] = $query;
        }

        return $this->client->request($method, $uri, $options);
    }

    /**
     * @param string $resource
     *
     * @return mixed
     */
    public function getResource($resource)
    {
        if ( ! array_key_exists($resource, $this->mapper)) {
            throw new \BadMethodCallException("Resource does not exist");
        }
        if ( ! array_key_exists($resource, $this->resources)) {
            $classname                  = $this->mapper[$resource];
            $this->resources[$resource] = new $classname($this);
        }

        return $this->resources[$resource];
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getResource($name);
    }
}