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
use \Horde_Constraint_AlwaysFalse;


class ConstraintFilterTest extends TestCase {


    public function setUp(): void
    {
        $this->level1 = new LogLevel(1, 'testName1');
        $this->level2 = new LogLevel(2, 'testName2');
        $this->level3 = new LogLevel(3, 'testName3');
        $this->message1 = 'testMessage1';
        $this->message2 = 'required_field';
        $this->message3 = 'somevalue';
        $this->context3 = ['somecontext'];
        $this->logMessage1 = new LogMessage($this->level1, $this->message1);
        $this->logMessage2 = new LogMessage($this->level2, $this->message2);
        $this->logMessage3 = new LogMessage($this->level3, $this->message3, $this->context3);
    }

    public function testFilterDoesNotAcceptWhenRequiredFieldIsMissing(){
        $filterator = new ConstraintFilter();
        $filterator->addRequiredField('required_field');
        $this->assertFalse($filterator->accept($this->logMessage1));

    }


    public function testFilterAcceptsWhenRequiredFieldisPresent()
    {
        //NB: I did not found a way to add a field called "required field" as has been done in ConstraintTest.php
        // Because one cannot manually define a field (right?), I am not sure if "addRequriedField" is usefull in its current form
        $filterator = new ConstraintFilter();
        $filterator->addRequiredField('message'); //NB: this seems only to be working for 'message', it is not wokring for 'level' which is also a field that is present
        $this->assertTrue($filterator->accept($this->logMessage2));
    }

    public function testFilterAcceptsWhenRegexMatchesField()
    {
        $filterator = new ConstraintFilter();
        $filterator->addRegex('message', '/some*/'); // again only the field message seems to be searchable with regex, none of the others

        $this->assertTrue($filterator->accept($this->logMessage3));
    }

    public function testFilterAcceptsWhenRegex_DOESNOT_MatcheField()
    {

        $filterator = new ConstraintFilter();
        $filterator->addRegex('message', '/this value does not exist/'); 

        $this->assertFalse($filterator->accept($this->logMessage3));
    }

    private function getConstraintMock($returnVal)
    {
        $const = $this->getMockBuilder('Horde_Constraint', array('evaluate'))->getMock();
        $const->expects($this->once())
            ->method('evaluate')
            ->will($this->returnValue($returnVal));
        return $const;
    }

    public function testFilterCallsEvalOnAllConstraintsWhenTheyAreAllTrue()
    {
        $filterator = new ConstraintFilter();
        $filterator->addConstraint('level', $this->getConstraintMock(true));
        $filterator->addConstraint('message', $this->getConstraintMock(true)); // again only the field message seems to be "constraintable"

        $filterator->accept($this->logMessage3);
    }

    public function testFilterStopsWhenItFindsAFalseCondition()
    {
        $filterator = new ConstraintFilter();
        $filterator->addConstraint('message', $this->getConstraintMock(true));
        $filterator->addConstraint('message', $this->getConstraintMock(true));
        $filterator->addConstraint('message', new Horde_Constraint_AlwaysFalse()); // this test only worlds for the field message 

        $const = $this->getMockBuilder('Horde_Constraint', array('evaluate'))->getMock();
        $const->expects($this->never())
            ->method('evaluate');
        $filterator->addConstraint('message', $const);
        $filterator->accept($this->logMessage3);
    }

    public function testFilterAcceptCallsConstraintOnNullWhenFieldDoesnotExist()
    {
        $filterator = new ConstraintFilter();
        $const = $this->getMockBuilder('Horde_Constraint', array('evaluate'))->getMock();
        $const->expects($this->once())
            ->method('evaluate')
            ->with(null);
        $filterator->addConstraint('non existant field', $const);
        $filterator->accept($this->logMessage2);
    }

}