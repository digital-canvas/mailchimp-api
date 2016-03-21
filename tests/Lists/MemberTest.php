<?php
namespace DigitalCanvas\Mailchimp\Test\Lists;

use DigitalCanvas\Mailchimp\Test\MailchimpTestTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class SubscribeTest
 *
 * @package Lists
 */
class MemberTest extends PHPUnit_Framework_TestCase
{
    use MailchimpTestTrait;

    /**
     * @test
     */
    public function subscribe_to_list()
    {
        $list     = $this->haveList();
        $listid   = $list['id'];
        $email    = $this->faker->freeEmail;
        $memberid = md5(strtolower($email));
        try {
            $member = $this->api->members->create($listid, $email);
            $this->assertEquals($email, $member['email_address']);
            $this->assertEquals('subscribed', $member['status']);
            $this->assertEquals($memberid, $member['id']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }

        }
    }

    /**
     * @test
     */
    public function list_subscribers()
    {
        $list = $this->haveList();
        try {
            $this->haveMember($list['id']);
            $this->haveMember($list['id']);
            $members = $this->api->members->getList($list['id']);
            $this->assertInstanceOf('DigitalCanvas\Mailchimp\Collection\PaginatedCollection', $members);
            $this->assertEquals(2, count($members));
            $this->assertEquals(2, $members->getTotalItems());
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }

    /**
     * @test
     */
    public function get_member_details()
    {
        $email = $this->faker->freeEmail;
        $hash  = md5(strtolower($email));
        $list  = $this->haveList();
        try {
            $this->haveMember($list['id'], $email);
            $member = $this->api->members->getDetails($list['id'], $hash);
            $this->assertEquals($email, $member['email_address']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }

    /**
     * @test
     */
    public function get_member_details_by_email()
    {
        $email = $this->faker->freeEmail;
        $list  = $this->haveList();
        try {
            $this->haveMember($list['id'], $email);
            $member = $this->api->members->getByEmail($list['id'], $email);
            $this->assertEquals($email, $member['email_address']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }

    /**
     * @test
     */
    public function unsubscribe_from_list()
    {
        $email = $this->faker->freeEmail;
        $list  = $this->haveList();
        try {
            $member = $this->haveMember($list['id'], $email, 'subscribed');
            $result = $this->api->members->unsubscribe($list['id'], $email);
            $this->assertEquals($member['email_address'], $result['email_address']);
            $this->assertEquals('unsubscribed', $result['status']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }

    /**
     * @test
     */
    public function resubscribe_to_list()
    {
        $email = $this->faker->freeEmail;
        $list  = $this->haveList();
        try {
            $member = $this->haveMember($list['id'], $email, 'unsubscribed');
            $this->assertEquals('unsubscribed', $member['status']);
            $result = $this->api->members->resubscribe($list['id'], $email);
            $this->assertEquals($member['email_address'], $result['email_address']);
            $this->assertEquals('subscribed', $result['status']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }

    /**
     * @test
     */
    public function update_member_details()
    {
        $email = $this->faker->freeEmail;
        $list  = $this->haveList();
        $updates = [
            'status' => 'unsubscribed',
            'email_type' => 'text',
        ];
        try {
            $member = $this->haveMember($list['id'], $email, 'subscribed');
            $this->assertEquals('subscribed', $member['status']);
            $this->assertEquals('html', $member['email_type']);
            $result = $this->api->members->update($list['id'], $email, $updates);
            $this->assertEquals($updates['status'], $result['status']);
            $this->assertEquals($updates['email_type'], $result['email_type']);
        } catch (\Exception $e) {
            $response = array_pop($this->requests)['response'];
            $error    = json_decode($response->getBody()->getContents(), true);
            $this->fail($error['detail']);
        } finally {
            if ($list['id']) {
                $this->api->lists->delete($list['id']);
            }
        }
    }
}