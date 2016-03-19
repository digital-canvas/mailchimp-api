<?php
namespace DigitalCanvas\Mailchimp\Test;

use DigitalCanvas\Mailchimp\Mailchimp;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

trait MailchimpTestTrait
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Mailchimp
     */
    protected $api;

    protected $requests = [];

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @before
     */
    public function initClient()
    {
        $this->requests = [];
        $stack          = HandlerStack::create();
        $stack->push(Middleware::history($this->requests));
        $this->client = new Client(['handler' => $stack]);
        $this->api    = new Mailchimp($this->client, getenv('MAILCHIMP_API_KEY'));
        $this->faker  = Factory::create();
    }

    protected function generateList(array $data = [])
    {
        $data = array_merge([
            'name'                  => $this->faker->text(20),
            'contact'               => [
                'company'  => $this->faker->company,
                'address1' => $this->faker->streetAddress,
                'address2' => $this->faker->secondaryAddress,
                'city'     => $this->faker->city,
                'state'    => $this->faker->state,
                'zip'      => $this->faker->postcode,
                'country'  => 'US',
                'phone'    => $this->faker->phoneNumber,
            ],
            'permission_reminder'   => $this->faker->text(50),
            'use_archive_bar'       => false,
            'campaign_defaults'     => [
                'from_name'  => $this->faker->name,
                'from_email' => $this->faker->email,
                'subject'    => $this->faker->sentence(5),
                'language'   => 'en',
            ],
            'notify_on_subscribe'   => '',
            'notify_on_unsubscribe' => '',
            'email_type_option'     => false,
            'visibility'            => 'pub',
        ], $data);

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function haveList(array $data = [])
    {
        $data = $this->generateList($data);

        return $this->api->lists->create($data);

        return $list;
    }

    /**
     * @param $listid
     * @param null $email
     * @param string $status
     *
     * @return array
     */
    public function haveMember($listid, $email = null, $status = 'subscribed')
    {
        if ( ! $email) {
            $email = $this->faker->email;
        }

        return $this->api->members->create($listid, $email, $status);
    }
}