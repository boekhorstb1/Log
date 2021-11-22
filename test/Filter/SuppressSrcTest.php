<?php
/**
 * Horde Log package
 *
 * This package is based on Zend_Log from the Zend Framework
 * (http://framework.zend.com).  Both that package and this
 * one were written by Mike Naberezny and Chuck Hagenbuch.
 *
 * @author     Rafael te Boekhorst <boekhorstb1@b1-sytstems.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Log
 * @subpackage UnitTests
 */
namespace Horde\Log\Test\Filter;

use \PHPUnit\Framework\TestCase;
use Horde\Log\Filter\SuppressFilter;
use Horde\Log\LogFilter;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;
use TypeError;




class SuppressSrcTest extends TestCase
{
    public function setUp(): void
    {
        $this->filter = new SuppressFilter();
    }

    
    public function testSuppressIsInitiallyOff()
    {
        $level1 = new LogLevel(1, 'testName1');
        $message1 = "test";
        $logMessage1 = new LogMessage($level1, $message1);
        
        $this->assertTrue($this->filter->accept($logMessage1));
    }
}