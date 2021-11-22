<?php
/**
 * Horde Log package
 *
 * @author     Rafael te Boekhorst <boekhorstb1s@b1-systems.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Log
 * @subpackage UnitTests
 */
namespace Horde\Log\Test\Filter;
use \PHPUnit\Framework\TestCase;

use Horde\Log\Filter\ConstraintFilter;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;


class ConstraintFilterTest extends TestCase {


    public function setUp(): void
    {
        $this->filterator = new ConstraintFilter();

        $this->level1 = new LogLevel(1, 'testName1');
        $this->level2 = new LogLevel(2, 'testName2');
        $this->message1 = 'testMessage1';
        $this->message2 = 'required_field';
        $this->context2 = ['required_field'];
        $this->logMessage1 = new LogMessage($this->level1, $this->message1);
        $this->logMessage2 = new LogMessage($this->level2, $this->message2);
    }

    public function testFilterDoesNotAcceptWhenRequiredFieldIsMissing(){

        $this->filterator->addRequiredField('required_field');

        $this->assertFalse($this->filterator->accept($this->logMessage1));

    }


    public function testFilterAcceptsWhenRequiredFieldisPresent()
    {
        //NB: I did not found a way to add a field called "required field" as has been done in ConstraintTest.php
        $filterator = $this->filterator;
        $filterator->addRequiredField('message');
        $this->assertTrue($this->filterator->accept($this->logMessage2));
    }

}