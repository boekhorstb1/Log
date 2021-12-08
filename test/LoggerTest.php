<?php
/**
 * Horde Log package.
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

namespace Horde\Log\Test;

use Horde\Log\Logger;
use PHPUnit\Framework\TestCase;

use Horde\Util\HordeString;
use Psr\Log\InvalidArgumentException;
use Stringable;

use Horde\Log\Filter\MessageFilter;
use Horde\Log\Handler\SyslogHandler;

use Horde\Log\LogHandler;
use Horde\Log\LogException;
use Horde_Log;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;
use Horde\Log\LogLevels;

class LoggerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['timestamp' => date('c')]);

        // not used yet but maybe later
        $this->loglevelsss = new LogLevels();
        $this->messagefilter[] = new MessageFilter('/emergency/');
        $this->sysloghandler[] = new SyslogHandler();

        $this->logging = new Logger($this->sysloghandler, null, $this->messagefilter);
    }

    public function testSerializeAndAddFilter(): void
    {
        $data = $this->logging->serialize();
        $this->assertStringContainsString('SyslogHandler', $data);
        $this->assertStringContainsString('MessageFilter', $data);
        $this->assertStringContainsString('/emergency/', $data);
    }
}
