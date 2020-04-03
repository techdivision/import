<?php

/**
 * TechDivision\Import\Handlers\PidFileHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Handlers;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Exceptions\FileNotFoundException;
use TechDivision\Import\Exceptions\ImportAlreadyRunningException;

/**
 * A PID file handler implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PidFileHandler implements PidFileHandlerInterface
{

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
     * @var \TechDivision\Import\ConfigurationInterface
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
     * @param \TechDivision\Import\ConfigurationInterface               $configuration      The actual configuration instance
     * @param \TechDivision\Import\Handlers\GenericFileHandlerInterface $genericFileHandler The actual file handler instance
     */
    public function __construct(
        ConfigurationInterface $configuration,
        GenericFileHandlerInterface $genericFileHandler
    ) {
        $this->configuration = $configuration;
        $this->genericFileHandler = $genericFileHandler;
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\ConfigurationInterface The system configuration
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
            }
        } catch (FileNotFoundException $fnfe) {
            $this->getSystemLogger()->notice(sprintf('PID file %s doesn\'t exist', $this->getPidFilename()));
        } catch (LineNotFoundException $lnfe) {
            $this->getSystemLogger()->notice(sprintf('PID %s is can not be found in PID file %s', $this->pid, $this->getPidFilename()));
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Can\'t remove PID %s from PID file %s', $this->pid, $this->getPidFilename()), null, $e);
        }
    }
}
