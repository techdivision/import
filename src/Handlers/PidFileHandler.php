<?php

/**
 * TechDivision\Import\Handlers\PidFileHandler
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

use Doctrine\Common\Collections\Collection;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Exceptions\FileNotFoundException;
use TechDivision\Import\Exceptions\ImportAlreadyRunningException;
use TechDivision\Import\SystemLoggerTrait;

/**
 * A PID file handler implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PidFileHandler implements PidFileHandlerInterface
{

    use SystemLoggerTrait;

    /**
     * The filehandle for the PID file.
     *
     * @var resource
     */
    private $fh;

    /**
     * The PID for the running processes.
     *
     * @var array
     */
    private $pid;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * The generic file handler instance.
     *
     * @var \TechDivision\Import\Handlers\GenericFileHandlerInterface
     */
    private $genericFileHandler;

    /**
     * Initializes the file handler instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration      The actual configuration instance
     * @param \TechDivision\Import\Handlers\GenericFileHandlerInterface $genericFileHandler The actual file handler instance
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers      The array with the system logger instances
     */
    public function __construct(
        ConfigurationInterface $configuration,
        GenericFileHandlerInterface $genericFileHandler,
        Collection $systemLoggers = null
    ) {
        $this->configuration = $configuration;
        $this->genericFileHandler = $genericFileHandler;
        if ($systemLoggers) {
            $this->setSystemLoggers($systemLoggers);
        }
    }

    /**
     * The array with the system loggers.
     *
     * @param \Doctrine\Common\Collections\Collection $systemLoggers The system logger instances
     *
     * @return void
     */
    public function setSystemLoggers(Collection $systemLoggers)
    {
        $this->systemLoggers = $systemLoggers;
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The system configuration
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's the generic file handler instance.
     *
     * @return \TechDivision\Import\Handlers\GenericFileHandlerInterface The generic file handler instance
     */
    protected function getGenericFileHandler()
    {
        return $this->genericFileHandler;
    }

    /**
     * Return's the PID filename to use.
     *
     * @return string The PID filename
     */
    protected function getPidFilename()
    {
        return $this->getConfiguration()->getPidFilename();
    }

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    protected function getSerial()
    {
        return $this->getConfiguration()->getSerial();
    }

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string   $line The line to be removed
     * @param resource $fh   The file handle of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    protected function removeLineFromFile($line, $fh)
    {
        return $this->getGenericFileHandler()->removeLineFromFile($line, $fh);
    }

    /**
     * Persist the UUID of the actual import process to the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be locked or the PID can not be added
     * @throws \TechDivision\Import\Exceptions\ImportAlreadyRunningException Is thrown, if a import process is already running
     */
    public function lock()
    {

        // query whether or not, the PID has already been set
        if ($this->pid === $this->getSerial()) {
            return;
        }

        // if not, initialize the PID
        $this->pid = $this->getSerial();

        // open the PID file
        $this->fh = fopen($filename = $this->getPidFilename(), 'a+');

        // try to lock the PID file exclusive
        if (!flock($this->fh, LOCK_EX|LOCK_NB)) {
            throw new ImportAlreadyRunningException(sprintf('PID file %s is already in use', $filename));
        }

        // append the PID to the PID file
        if (fwrite($this->fh, $this->pid . PHP_EOL) === false) {
            throw new \Exception(sprintf('Can\'t write PID %s to PID file %s', $this->pid, $filename));
        }
    }

    /**
     * Remove's the UUID of the actual import process from the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be removed
     */
    public function unlock()
    {
        try {
            // remove the PID from the PID file if set
            if ($this->pid === $this->getSerial() && is_resource($this->fh)) {
                // remove the PID from the file
                $this->removeLineFromFile($this->pid, $this->fh);

                // finally unlock/close the PID file
                flock($this->fh, LOCK_UN);
                fclose($this->fh);

                // if the PID file is empty, delete the file
                if (filesize($filename = $this->getPidFilename()) === 0) {
                    unlink($filename);
                }

                // reset resource
                $this->pid = null;
                $this->fh = null;
            }
        } catch (FileNotFoundException $fnfe) {
            $this->getSystemLogger()->notice(sprintf('PID file %s doesn\'t exist', $this->getPidFilename()));
        } catch (LineNotFoundException $lnfe) {
            $this->getSystemLogger()->notice(sprintf('PID %s can not be found in PID file %s', $this->pid, $this->getPidFilename()));
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Can\'t remove PID %s from PID file %s', $this->pid, $this->getPidFilename()), 0, $e);
        }
    }
}
