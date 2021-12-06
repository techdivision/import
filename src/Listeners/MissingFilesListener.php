<?php

/**
 * TechDivision\Import\Listeners\MissingFilesListener
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Exceptions\MissingFileException;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class MissingFilesListener extends AbstractListener
{

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * @var array|\ArrayObject
     */
    protected $noFileCheckNeed;

    /**
     * @param RegistryProcessorInterface $registryProcessor
     * @param \ArrayObject               $noFileCheckNeed
     */
    public function __construct(RegistryProcessorInterface $registryProcessor, $noFileCheckNeed = [])
    {
        $this->registryProcessor = $registryProcessor;
        $this->noFileCheckNeed = $noFileCheckNeed;
    }

    /**
     * @param EventInterface            $event
     * @param ApplicationInterface|null $application
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\MissingFileException
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {

        // load the validations from the registry
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // Verify whether or not the shortcut needs to be checked
        $shortcut = $application->getConfiguration()->getShortcut();
        if (in_array($shortcut, (array) $this->noFileCheckNeed)) {
            return;
        }

        // query whether or not we've no file found to import
        if (is_array($status) && (!isset($status['countImportedFiles']) || $status['countImportedFiles'] === 0)) {
            $application->missingfile('no file was found', MissingFileException::NOT_FOUND_CODE);
        }
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }
}
