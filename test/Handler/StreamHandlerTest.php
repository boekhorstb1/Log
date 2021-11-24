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

use Horde\Log\Handler\StreamHandler;

use PHPUnit\Framework\TestCase;

use Horde\Log\LogException;

class StreamHandlerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
    }

    public function testConstructorThrowsWhenResourceIsNotStream()
    {
        $this->expectException(LogException::class);
        $resource = xml_parser_create();
        $test = new StreamHandler($resource);
        xml_parser_free($resource); # closes the resource, has no other effects (this is only for pre-php8)
    }

    public function testConstructorWithValidStream()
    {
        $stream = fopen('php://memory', 'a');
        new StreamHandler($stream);
        $this->markTestSkipped('No Exception expected.');
    }

    public function testConstructorThrowsWhenModeSpecifiedForExistingStream()
    {
        $this->expectException(LogException::class);
        $stream = fopen('php://memory', 'a');
        new StreamHandler($stream, 'w');
    }
}
