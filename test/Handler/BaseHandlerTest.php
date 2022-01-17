<?php
/**
 * Horde Log package
 *
 * This package is based on Zend_Log from the Zend Framework
 * (http://framework.zend.com).  Both that package and this
 * one were written by Mike Naberezny and Chuck Hagenbuch.
 *
 * @author     Rafael te Boekhorst <boekhorstb1@b1-systems.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Log
 * @subpackage UnitTests
 */

namespace Horde\Log\Test\Handler;

use Horde\Log\Handler\BaseHandler;
use Horde\Log\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use Horde\Log\LogException;
use Horde_Log;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;
use Horde\Log\Filter\ConstraintFilter;
use Horde\Log\Filter\MessageFilter;
use Horde\Log\Logger;

class BaseHandlerTest extends TestCase
{
    public function setUp(): void
    {
        # Bult in Mock for abstract classes (in phpunit)
        $this->baseHandlerMock = $this->getMockForAbstractClass(BaseHandler::class);

        # Own Mock class for testing the base class
        $this->mockhandler = new MockHandler();

        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['randomfield' => 'stuff']);

        $this->constraintFilter = new ConstraintFilter();
    }

    /**
     * Note: This tests the following with the Phpunit mock of abstract classes:
     * - that log() passes the logMessage1 to the write() function
     * - that write() returns a boolean
     *
     * NB: the write() function is tested more directly by the testmethod testAbstractWriteFunctionsWithOwnMockClass() and by NullHandlerTest.php
     */
    public function testAbstractWriteFunctionsMustReturnBoolWithPHPunitAbstractClass(): void
    {
        $this->expectException('TypeError');

        $baseHandlerMock = $this->baseHandlerMock;

        $baseHandlerMock->expects($this->once())
                 ->method('write')
                 ->will($this->returnValue(null));

        $baseHandlerMock->log($this->logMessage1);
    }

    public function testAbstractWriteFunctionsWithOwnMockClass()
    {
        $this->assertTrue($this->mockhandler->write($this->logMessage1));
    }

    public function testSetOptionReturnsErrorWhenWrongParams(): void
    {
        $this->expectException(LogException::class);

        $this->mockhandler->setOption('foo', 'bar');
    }

    public function testSetOptionReturnsTrueWhenCorrectParams(): void
    {
        $this->assertTrue($this->mockhandler->setOption('ident', 'test'));
    }


    /**
     * @doesNotPerformAssertions
     */
    public function testFiltersAreAdded(): void
    {
        $this->baseHandlerMock->addFilter($this->constraintFilter);
    }

    public function testIfLogMethodUsesFiltersByUsingMockhandler(): void
    {
        // reassigning defaul mockhandler to a local variable
        $mockhandler = $this->mockhandler;

        // creating a message that IS going to be logged
        $level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $message1 = 'this is an emergency!';
        $logMessage1 = new LogMessage($level1, $message1, ['randomfield' => 'stuff']);

        // creating a message that is NOT going to be logged
        $level2 = new LogLevel(Horde_Log::CRITICAL, 'Critical');
        $message2 = 'this is not going to be logged!';
        $logMessage2 = new LogMessage($level2, $message2, ['randomfield' => 'stuff']);

        // creating a message filter for the BaseHandler
        $messageFilter = new MessageFilter('/emergency/');
        $mockhandler->addFilter($messageFilter);

        // Check that this will filter message1
        $mockhandler->log($logMessage1);
        $this->assertEquals($mockhandler->check, $logMessage1);

        // Check that this will not filter message2
        $mockhandler->log($logMessage2);
        $this->assertEquals($mockhandler->check, 'filtered out');
        //$this->assertEquals($check, $logMessage1);
    }
}
