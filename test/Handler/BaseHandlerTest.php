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
use PHPUnit\Framework\TestCase;
use Horde\Log\LogHandler;
use Horde\Log\LogException;
use Horde_Log;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;

class BaseHandlerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['timestamp' => date('c')]);

        $this->stub = $this->getMockForAbstractClass(BaseHandler::class);
    }

    public function testAbstractWriteFunctionsMustReturnBool(): void 
    {
        $this->expectException('TypeError');
        
        $stub = $this->stub; 

        $stub->expects($this->any())
                 ->method('write')
                 ->will($this->returnValue(null));

        $stub->log($this->logMessage1);
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


    


}