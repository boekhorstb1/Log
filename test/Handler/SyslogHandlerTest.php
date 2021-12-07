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

use Horde\Log\Handler\SyslogHandler;
use PHPUnit\Framework\TestCase;
use Horde\Log\LogHandler;
use Horde\Log\LogException;
use Horde_Log;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;

class SyslogHandlerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['timestamp' => date('c')]);
        $this->syshandler = new SyslogHandler();
    }

    # Currently, a log message needs to be formatted beforehand, should this be included in the write() function? Or should an error be thrown here indicating that the message thousl be formatted?
    public function testIfMessageIsFormatted(): void
    {
        $this->expectException('Error');
        $this->syshandler->setOption('ident', 'Message to terminal" ');
        $this->syshandler->setOption('openlogOptions', LOG_PERROR);
        $this->syshandler->write($this->logMessage1);
    }

    public function testWrite(): void
    {
        $this->logMessage1->formatMessage([]);
        $this->syshandler->setOption('ident', 'Where is this log written to? A yes, tot the terminal beacause of "LOG_PERROR" ');
        $this->syshandler->setOption('openlogOptions', LOG_PERROR);
        $this->assertTrue($this->syshandler->write($this->logMessage1));
    }

    public function testIndentErrorInitializeSyslog(): void
    {
        $this->expectException(LogException::class);
        $this->syshandler->setOption('ident', 2);
        $this->syshandler->setOption('openlogOptions', 1);
        $this->syshandler->write($this->logMessage1);
    }

    public function testOptionsErrorInitializeSyslog(): void
    {
        $this->expectException(LogException::class);
        $this->syshandler->setOption('ident', 'some error message');
        $this->syshandler->setOption('openlogOptions', 'this should be a log constant or at least an integer');
        $this->syshandler->write($this->logMessage1);
    }

    # I have not found a way to make the function syslog() within the if-satement of the write()-method... that would be needed to test the errormessages
    public function testSysLogErrorThrows()
    {
        $this->markTestSkipped('should be revisited?');
    }
}
