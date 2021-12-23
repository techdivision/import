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
use TechDivision\Import\Exceptions\InvalidDataException;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class InvalidDataListener extends AbstractListener
{

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;


    /**
     * @param RegistryProcessorInterface $registryProcessor The processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * @param EventInterface            $event       The event that triggered the listener
     * @param ApplicationInterface|null $application The application instance
     *
     * @return void
     * @throws \TechDivision\Import\Exceptions\MissingFileException
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {
        // load the validations from the registry
        $noStrictValidations = $this->getRegistryProcessor()->getAttribute(RegistryKeys::NO_STRICT_VALIDATIONS);

        // query whether or not we've validation errors
        if (is_array($noStrictValidations) && sizeof($noStrictValidations) > 0) {
            $application->invalidDataNoStrict('Invalid Data Please Check your Validation.json file' , InvalidDataException::INVALID_DATA_CODE);
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
