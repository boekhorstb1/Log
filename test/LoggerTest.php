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

use Psr\Log\LoggerInterface;

use Horde\Util\HordeString;
use Psr\Log\InvalidArgumentException;
use Stringable;

use Horde\Log\Filter\MessageFilter;
use Horde\Log\Handler\MockHandler;
use Horde\Log\Handler\SyslogHandler;
use ReflectionClass;

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
        $this->message2 = 'this is a polar bear!';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['timestamp' => date('c')]);
        $this->logMessage2 = new LogMessage($this->level1, $this->message2);

        $this->loglevelsss = new LogLevels();
        $this->messagefilter[] = new MessageFilter('/emergency/');

        $this->mockhandler1 = new MockHandler();
        $this->mockhandler2 = new MockHandler();
        $this->mockhandler2->addFilter(new MessageFilter('/notwokringregexpattern/'));
        $this->handlers[] = $this->mockhandler1;
        $this->handlers[] = $this->mockhandler2;


        $this->logging = new Logger($this->handlers, null, $this->messagefilter);
    }

    public function testSerializeAndAddFilter(): void
    {
        $data = $this->logging->serialize();
        $this->assertStringContainsString('MockHandler', $data);
        $this->assertStringContainsString('MessageFilter', $data);
        $this->assertStringContainsString('/emergency/', $data);
    }

    public function testSerializeAndAddHandler(): void
    {
        $mockhandler3 = new MockHandler();
        $mockhandler3->addFilter(new MessageFilter('/whazzaaa/'));
        $this->logging->addHandler($mockhandler3);
        $data = $this->logging->serialize();
        $this->assertStringContainsString('/whazzaaa/', $data);
    }

    public function testUnserialize(): void
    {
        // message that should be filtered out
        $level = new LogLevel(Horde_Log::ALERT, 'Alert');
        $message1 = 'this message will not be logged because the MessageFilter will remove it';
        $message2 = 'buzzword testUnserialize, this message will be logged successfully';
        $logger = new Logger();

        // test serializing if default logger (the one set in SeTup()) contains filter and handler
        $mockhandler4 = new MockHandler();
        $mockhandler4->addFilter(new MessageFilter('/testUnserialize/'));
        $logger->addHandler($mockhandler4);
        $data = $logger->serialize();
        $this->assertStringContainsString('/testUnserialize/', $data);

        // unserialize data, serialize it again and check if message1 is filtered out correctly and message2 is logged correctly
        $newlogger = new Logger();
        $newlogger->unserialize($data);
        $newlogger->log($level, $message1);
        $newdata = $newlogger->serialize();
        $this->assertStringContainsString('filtered out', $newdata);
        $newlogger->log($level, $message2);
        $newdata = $newlogger->serialize();
        $this->assertStringContainsString('successfully', $newdata);
    }

    public function testErrorsUnserialize(): void
    {
        // list of wrong data that is serialized
        $data[] = ['this is not a string'];
        $data[] = serialize('blabla');
        $data[] = serialize([]);
        $data[] = serialize(['another version']);
        $count = 0;

        // test that all wrong data will throw errors
        foreach ($data as $value) {
            try {
                $this->logging->unserialize($value);
            } catch (\Throwable $th) {
                $count++;
                $this->assertInstanceOf(LogException::class, $th);
            }
        }

        // the amount of throws needs the equal the lenght of the array data[]
        $this->assertEquals(count($data), $count);
    }

    public function testLogMethodsByCheckingTheirClassAndIfMockHandlerWorks()
    {
        $methods = get_class_methods(Logger::class);

        $loggerinterface_methods = array_flip(get_class_methods(LoggerInterface::class));

        foreach ($methods as $key => $method) {
            if (array_key_exists($method, $loggerinterface_methods)) {
                if ($method == 'log') {
                    $this->logging->$method($this->level1, $this->message1);
                    $this->assertEquals($this->mockhandler1->check->message(), $this->logMessage1->message());
                    $this->assertEquals($this->mockhandler1->check->level()->name(), $this->logMessage1->level()->name());
                } else {
                    $this->logging->$method($this->message1);
                    $this->assertEquals($this->mockhandler1->check->message(), $this->logMessage1->message());
                    $this->assertEquals($this->mockhandler1->check->level()->name(), $method);
                }
            }
        }
    }

    public function testStringAsMessageForLog()
    {
        $this->assertNull($this->logging->log($this->level1, "righty o, this should be a message for an alert warrrninggg"));
    }

    public function testFiltersOfLoggerWorkWithoutMockHandlerFilter()
    {
        $this->mockhandler2->log($this->logMessage1);
        $this->assertEquals('filtered out', $this->mockhandler2->check);
        $this->logging->log($this->level1, $this->message1);
        $this->assertEquals('filtered out', $this->mockhandler2->check);
    }

    public function testLogMethodThrowsErrorIfWrongLogCode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->logging->log(22, $this->message1);
    }

    public function testLogMethodThrowsErrorIfWrongLogLevelString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->logging->log("nonexistant level", $this->message1);
    }
}
