<?php
/**
 * Tests for the LogLevels
 *
 * @author     Rafael te Boekhorst <boekhorst@b1-systems.de>
 * @category   Horde
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Log
 * @subpackage Handlers
 */
declare(strict_types=1);

namespace Horde\Log\Test;

use Horde\Util\HordeString;
use Psr\Log\LogLevel as PsrLogLevel;


use PHPUnit\Framework\TestCase;
use Horde\Log\LogFilter;
use Horde\Log\LogHandler;
use Horde\Log\LogFormatter;
use Horde\Log\LogMessage;
use Horde\Log\LogLevel;
use Horde\Log\LogException;
use Psr\Log\LoggerInterface;
use Horde\Log\LogLevels;
use InvalidArgumentException;
use Horde_Log;

class LogLevelsTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('America/New_York');
        $this->level1 = new LogLevel(Horde_Log::ALERT, 'Alert');
        $this->message1 = 'this is an emergency! Really! ...';
        $this->logMessage1 = new LogMessage($this->level1, $this->message1, ['timestamp' => date('c')]);
        $this->loadlevels[] = $this->level1;
        $this->loglevels = new LogLevels($this->loadlevels);
    }

    public function testLogLevelsConstructorWithCustomLogLevel()
    {
        $this->level24 = new LogLevel(24, 'Weirdness warning');

        $this->loadlevels[] = $this->level24;
        $this->loadlevels[] = $this->level1;
        $this->loglevels = new LogLevels($this->loadlevels);
        $this->assertInstanceOf(LogLevels::class, $this->loglevels);
    }

    public function testLogLevelsRegisterFunctionWithGetByLevelName()
    {
        $this->level35 = new LogLevel(35, 'Strangeness warning');
        $this->loglevels->register($this->level35);
        $this->assertEquals($this->loglevels->getByLevelName('strangeness warning'), $this->level35);
    }

    public function testLogLevelsRegisterFunctionWithGetByCriticality()
    {
        $this->level36 = new LogLevel(36, 'Absurdness warning');
        $this->loglevels->register($this->level36);
        $this->assertEquals($this->loglevels->getByLevelName('Absurdness warning'), $this->level36);
    }

    public function testBothInitMethods()
    {
        $aliaslevels = $this->loglevels->initWithAliasLevels();
        $initWithCanonicalLevels = $this->loglevels->initWithCanonicalLevels();

        $loggerinterface_methods = get_class_methods(LoggerInterface::class);
        $this->count = 0;

        foreach ($loggerinterface_methods as $key => $interfacelevel) {
            if ($interfacelevel !== 'log') {
                $bynameCanonical = $initWithCanonicalLevels->getByLevelName($interfacelevel);
                $bycriticalityCannonical = $initWithCanonicalLevels->getByCriticality($key);
                $bynameAlias = $aliaslevels->getByLevelName($interfacelevel);
                $bycriticalityAlias =  $aliaslevels->getByCriticality($key);
                $this->assertEquals($bynameCanonical, $bycriticalityAlias);
                $this->assertEquals($bynameAlias, $bycriticalityCannonical);
                if ($aliaslevels->getByLevelName("emerg")->criticality() == $key) {
                    $this->count += 1;
                }
                if ($aliaslevels->getByLevelName("crit")->criticality() == $key) {
                    $this->count += 1;
                }
                if ($aliaslevels->getByLevelName("warn")->criticality() == $key) {
                    $this->count += 1;
                }
                if ($aliaslevels->getByLevelName("err")->criticality() == $key) {
                    $this->count += 1;
                }
                if ($aliaslevels->getByLevelName("information")->criticality() == $key) {
                    $this->count += 1;
                }
                if ($aliaslevels->getByLevelName("informational")->criticality() == $key) {
                    $this->count += 1;
                }
            }
        }
        $this->assertEquals($this->count, 6); // initWithAliasLevels() has 6x more values than initWithCanonicalLevels()
    }
}
