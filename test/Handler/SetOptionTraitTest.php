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

use Horde\Log\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use Horde\Log\LogException;

class SetOptionTraitTest extends TestCase
{
    public function setUp(): void
    {
        # Own Mock class for testing the base class
        $this->mockhandler = new MockHandler();
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
}
