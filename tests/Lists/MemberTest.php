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
        $email    = "jondigitalcanvas+" . time() . "@gmail.com";
        $memberid = md5(strtolower($email));
        try {
            $member = $this->api->members->create($listid, $email);
            $this->assertEquals($email, $member['email_address']);
            $this->assertEquals('subscribed', $member['status']);
            $this->assertEquals($memberid, $member['id']);
        } catch (\Exception $e) {
            $this->fail('Failed to create member');
        } finally {
            $this->api->lists->delete($listid);
        }


    }


}