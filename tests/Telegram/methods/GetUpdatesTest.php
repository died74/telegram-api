<?php

namespace tests\Telegram\Methods;

use tests\Mock\MockTgLog;
use unreal4u\Telegram\Methods\GetUpdates;

class GetUpdatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockTgLog
     */
    private $tgLog;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->tgLog = new MockTgLog('TEST-TEST');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->tgLog = null;
        parent::tearDown();
    }

    /**
     * Asserts that the GetMe method ALWAYS load in a user type
     */
    public function test_bindToObjectType()
    {
        $type = GetUpdates::bindToObjectType();
        $this->assertEquals('Custom\\UpdatesArray', $type);
    }

    /**
     * Tests a private message "Hello bot" to the bot
     *
     * @depends test_bindToObjectType
     */
    public function test_getUpdatesMessageIncoming()
    {
        $getUpdates = new GetUpdates();

        $result = $this->tgLog->performApiRequest($getUpdates);
        $this->assertInstanceOf('unreal4u\\Telegram\\Types\\Custom\\UpdatesArray', $result);
        $this->assertContainsOnlyInstancesOf('unreal4u\\Telegram\\Types\\Update', $result->data);
        $this->assertCount(1, $result->data);

        $firstResult = $result->data[0];
        $this->assertEquals(12345678, $firstResult->update_id);
        $this->assertInstanceOf('unreal4u\\Telegram\\Types\\Message', $firstResult->message);

        $theMessage = $firstResult->message;
        $this->assertEquals(12, $theMessage->message_id);
        $this->assertInstanceOf('unreal4u\\Telegram\\Types\\User', $theMessage->from);
        $this->assertInstanceOf('unreal4u\\Telegram\\Types\\Chat', $theMessage->chat);
        $this->assertEquals('Hello bot', $theMessage->text);
        $this->assertEquals(12345678, $theMessage->from->id);
        $this->assertEquals('unreal4u', $theMessage->from->username);
        $this->assertEquals(98765432, $theMessage->chat->id);
        $this->assertEquals('unreal4u', $theMessage->chat->username);

        $this->assertEquals(1452120442, $theMessage->date);
        $this->assertNull($theMessage->audio);
    }

    public function test_emptyUpdates()
    {
        $getUpdates = new GetUpdates();
        $getUpdates->offset = 12345679;

        $this->tgLog->specificTest = 'emptyResponse';
        $result = $this->tgLog->performApiRequest($getUpdates);

        $this->assertInstanceOf('unreal4u\\Telegram\\Types\\Custom\\UpdatesArray', $result);
        $this->assertEquals(12345679, $getUpdates->offset);
        $this->assertCount(0, $result->data);
    }
}