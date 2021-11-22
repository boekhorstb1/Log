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
namespace Horde\Log\Test\Filter;


use \PHPUnit\Framework\TestCase;
use Horde\Log\Filter\MessageFilter;
use Horde\Log\LogFilter;
use Horde\Log\LogMessage;
use InvalidArgumentException;
use TypeError;



class MessageSrcTest extends TestCase
{

    // public function setUp(): void
    // {
        
    // }

    public function testMessageFilterRecognizesInvalidRegularExpression(){
        $this->expectException('InvalidArgumentException');
        new MessageFilter('invalid regexp');
    }
}