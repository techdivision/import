<?php

/**
 * TechDivision\Import\Listeners\PrepareDebugDumpListener
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Utils\DebugUtilInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Listener that creates a debug dump from the given artefacts.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PrepareDebugDumpListener extends AbstractListener
{

    /**
     * The debug util instance.
     *
     * @var \TechDivision\Import\Utils\DebugUtilInterface
     */
    private $debugUtil;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * Initializes the listeners with the debug util and configuration instances.
     *
     * @param \TechDivision\Import\Utils\DebugUtilInterface         $debugUtil     The debug util instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(
        DebugUtilInterface $debugUtil,
        ConfigurationInterface $configuration
    ) {

        // set the passed instances
        $this->debugUtil = $debugUtil;
        $this->configuration = $configuration;
    }

    /**
     * Return's the debug util instance.
     *
     * @return \TechDivision\Import\Utils\DebugUtilInterface The debug util instance
     */
    private function getDebugUtil() : DebugUtilInterface
    {
        return $this->debugUtil;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    private function getConfiguration() : ConfigurationInterface
    {
        return $this->configuration;
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
        if ($this->getConfiguration()->isDebugMode()) {
            $this->getDebugUtil()->prepareDump($application->getSerial());
        }
    }
}
