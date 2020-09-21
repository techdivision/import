<?php

/**
 * TechDivision\Import\Listeners\RenderDebugInfoListener
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

use Psr\Log\LogLevel;
use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * An listener implementation that invokes the renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderDebugInfoListener extends AbstractListener
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
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

        // abort if the application instance is not available
        if (!$application instanceof ApplicationInterface) {
            throw new \Exception('Application instance not available in ' . __CLASS__);
        }

        // log the debug information, if debug mode is enabled
        if ($this->getConfiguration()->isDebugMode()) {
            // log the Magento + the system's PHP configuration
            $application->log(sprintf('Magento Edition: %s', $this->getConfiguration()->getMagentoEdition()), LogLevel::DEBUG);
            $application->log(sprintf('Magento Version: %s', $this->getConfiguration()->getMagentoVersion()), LogLevel::DEBUG);
            $application->log(sprintf('PHP Version: %s', phpversion()), LogLevel::DEBUG);
            $application->log(sprintf('App Version: %s', $application->getVersion()), LogLevel::DEBUG);
            $application->log('-------------------- Loaded Extensions -----------------------', LogLevel::DEBUG);
            $application->log(implode(', ', get_loaded_extensions()), LogLevel::DEBUG);
            $application->log('------------------- Executed Operations ----------------------', LogLevel::DEBUG);
            $application->log(implode(' > ', $this->getConfiguration()->getOperationNames()), LogLevel::DEBUG);
            $application->log('--------------------------------------------------------------', LogLevel::DEBUG);
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
}
