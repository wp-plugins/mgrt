<?php

namespace Mgrt\Tests\Model;

class ContactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider addMailingListsAndTransformToArrayProvider
     */
    public function testAddMailingListsAndTransformToArray($existing, $added, $expected)
    {
        $contact = new \Mgrt\Model\Contact();
        $contact->setMailingLists($existing);
        $contact->addMailingList($added);

        $this->assertEquals($contact->getMailingListsToArray(), $expected);
        foreach ($contact->getMailingListsToArray() as $key => $value) {
            $this->assertInternalType('integer', $value);
        }
    }

    public function addMailingListsAndTransformToArrayProvider()
    {
        $mailingList = new \Mgrt\Model\MailingList();
        return array(
            array(
                array(),
                clone $mailingList->setId(123),
                array(123),
            ),
            array(
                array(clone $mailingList->setId(123)),
                clone $mailingList->setId(456),
                array(123, 456),
            ),
            array(
                array(clone $mailingList->setId(123), clone $mailingList->setId(789)),
                clone $mailingList->setId(456),
                array(123, 789, 456),
            ),
        );
    }
}
