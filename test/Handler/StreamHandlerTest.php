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

use PHPUnit\Framework\TestCase;

class StreamHandlerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
    }

    public function testConstructorThrowsWhenResourceIsNotStream()
    {
        $this->expectException('Horde_Log_Exception');
        $resource = xml_parser_create();
        new Horde_Log_Handler_Stream($resource);
        xml_parser_free($resource);
    }
}
