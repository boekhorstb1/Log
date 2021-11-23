<?php
/**
 * Horde Log package
 *
 * This package is based on Zend_Log from the Zend Framework
 * (http://framework.zend.com).  Both that package and this
 * one were written by Mike Naberezny and Chuck Hagenbuch.
 *
 * @author   Rafael te Boekhorst <boekhorstb1@b1-systems.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/bsd BSD
 * @package  Log
 */
namespace Horde\Log\Formatter\Test;
use \PHPUnit\Framework\TestCase;

use \Horde_Cli;
use Horde\Log\Formatter\CliFormatter;

use Horde\Log\LogMessage;
use Horde\Log\LogLevel;

class CliFormatterTest extends TestCase {

    public function setUp(): void
    {
        $this->cli = new Horde_Cli();

        $this->level1 = new LogLevel(1, 'Emergency');
        $this->level2 = new LogLevel(2, 'warning');
        $this->level3 = new LogLevel(3, 'info');
        $this->message1 = "this is an emergency!";
        $this->message2 = "this is a warning!";
        $this->message3 = "some info here!";
        $this->logMessage1 = new LogMessage($this->level1, $this->message1);
        $this->logMessage2 = new LogMessage($this->level2, $this->message2);
        $this->logMessage3 = new LogMessage($this->level3, $this->message3);

    }

    public function testDefaultFormat(){

        $f = new CliFormatter($this->cli);
        $line = $f->format($this->logMessage1);

        $loglevel = $this->logMessage1->level();
        $name = $loglevel->name();

        # Note: the cliformatter does not output the value of "Criticallity"
        // $criticality = $loglevel->criticality();
        
        $this->assertStringContainsString($this->message1 , $line);
        $this->assertStringContainsString($name, $line);    
    }

    public function testColorSettings(){

        $f = new CliFormatter($this->cli);

        $logsarray = [$this->logMessage1, $this->logMessage2, $this->logMessage3];
        // dd($logsarray);

        foreach($logsarray as $key => $value){

            $line = $f->format($value);

            $loglevel = $value->level();
            $name = $loglevel->name();

            switch ($name) {
                case 'emergency':
                    $this->assertStringContainsString("\e[31m", $line);  
                    break;
                case 'warning':
                    $this->assertStringContainsString("\e[33m", $line);  
                    break;
                case 'info':
                    $this->assertStringContainsString("\e[34m", $line);  
                    break;
                default:
                    $this->assertStringContainsString("\e[39m", $line);
                    break;
            }
        }
        

    }

    

}