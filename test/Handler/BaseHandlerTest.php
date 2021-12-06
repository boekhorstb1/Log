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

class BaseHandlerTest extends TestCase
{
    public function setUp(): void
    {
        # Bult in Mock for abstract classes (in phpunit)
        $this->stub = $this->getMockForAbstractClass(BaseHandler::class);

        # Own Mock class for testing the base class
        $this->mockhandler = new MockHandler();

        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['randomfield' => 'stuff']);

        $this->constraint_filter = new ConstraintFilter();
    }

    /**
     * Note: This tests the following with the Phpunit mock of abstract classes:
     * - that log() passes the logMessage1 to the write() function
     * - that write() returns a boolean
     *
     * NB: the write() function is teste more directly by the testmethod testAbstractWriteFunctionsWithOwnMockClass() and by NullHandlerTest.php
     */
    public function testAbstractWriteFunctionsMustReturnBoolWithPHPunitAbstractClass(): void
    {
        $this->expectException('TypeError');

        $stub = $this->stub;

        $stub->expects($this->once())
                 ->method('write')
                 ->will($this->returnValue(null));

        $stub->log($this->logMessage1);
    }

    public function testAbstractWriteFunctionsWithOwnMockClass()
    {
        $this->assertTrue($this->mockhandler->write($this->logMessage1));
    }

    public function testSetOptionReturnsErrorWhenWrongParams(): void
    {
        $this->expectException(LogException::class);

        $this->stub->setOption('foo', 'bar');
    }

    public function testSetOptionReturnsTrueWhenCorrectParams(): void
    {
        $this->assertTrue($this->stub->setOption('ident', 'test'));
    }


    /**
     * @doesNotPerformAssertions
     */
    public function testFiltersAreAdded(): void
    {
        $this->stub->addFilter($this->constraint_filter);
    }
}
