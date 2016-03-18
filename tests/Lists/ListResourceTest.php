<?php
namespace DigitalCanvas\Mailchimp\Test\Lists;

use DigitalCanvas\Mailchimp\Mailchimp;
use DigitalCanvas\Mailchimp\Test\MailchimpTestTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class TestListResource
 *
 * @package Lists
 */
class ListResourceTest extends PHPUnit_Framework_TestCase
{
    use MailchimpTestTrait;

    /**
     * @test
     */
    public function list_lists()
    {
        $lists = $this->api->lists->getList();
        $this->assertInstanceOf('DigitalCanvas\Mailchimp\Collection\PaginatedCollection', $lists);
    }

    public function list_details()
    {
        $list = $this->haveList();
        $details = $this->api->lists->getDetails($list['id']);
        $this->assertEquals($list['id'], $details['id']);

        // Cleanup
        $this->api->lists->delete($list['id']);
    }

    /**
     * @test
     */
    public function create_list()
    {
        $list_name = $this->faker->text(20);
        $data = $this->generateList(['name' => $list_name]);
        $list = $this->api->lists->create($data);
        $this->assertEquals($list_name, $list['name']);

        // Cleanup
        $this->api->lists->delete($list['id']);
    }
}