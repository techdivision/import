<?php

/**
 * TechDivision\Import\Listeners\RenderOperationReportListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use Psr\Log\LogLevel;
use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * A listener implementation that renders the operation report.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderOperationReportListener extends AbstractListener
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The configuration instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor, ConfigurationInterface $configuration)
    {
        $this->registryProcessor = $registryProcessor;
        $this->configuration = $configuration;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface              $event       The event that triggered the listener
     * @param \TechDivision\Import\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function handle(EventInterface $event, ApplicationInterface $application = null)
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // query whether or not a status of the actual import is available
        if ($registryProcessor->hasAttribute(RegistryKeys::STATUS)) {
            // load the status from the registry
            $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

            // initialize the date with the start time
            $startedAt = new \DateTime();
            $startedAt->setTimestamp($status[RegistryKeys::STARTED_AT]);

            // initialize the date with the finish time
            $finishedAt = new \DateTime();
            $finishedAt->setTimestamp($status[RegistryKeys::FINISHED_AT]);

            // calculate the execution time
            $interval = $finishedAt->diff($startedAt);
            $executionTime = $interval->format("%H:%I:%S");

            // log a message that import has been finished
            $application->log(
                sprintf(
                    'Successfully executed command %s with serial %s in %s s',
                    $this->getConfiguration()->getCommandName(),
                    $application->getSerial(),
                    $executionTime
                ),
                LogLevel::INFO
            );
        }
    }

    /**
     * Returns the configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**s
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    protected function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }
}
