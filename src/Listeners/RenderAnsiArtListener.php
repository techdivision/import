<?php

/**
 * TechDivision\Import\Listeners\RenderAnsiArtListener
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
use TechDivision\Import\ConfigurationInterface;

/**
 * An listener implementation that invokes the renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderAnsiArtListener extends AbstractListener
{

    /**
     * The TechDivision company name as ANSI art.
     *
     * @var string
     */
    protected $ansiArt = ' _______        _     _____  _       _     _
|__   __|      | |   |  __ \(_)     (_)   (_)
   | | ___  ___| |__ | |  | |___   ___ ___ _  ___  _ __
   | |/ _ \/ __| \'_ \| |  | | \ \ / / / __| |/ _ \| \'_ \
   | |  __/ (__| | | | |__| | |\ V /| \__ \ | (_) | | | |
   |_|\___|\___|_| |_|_____/|_| \_/ |_|___/_|\___/|_| |_|
';

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the event.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
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

        // write the TechDivision ANSI art icon to the console
        $application->log($this->ansiArt);

        // log the debug information, if debug mode is enabled
        if ($this->getConfiguration()->isDebugMode()) {
            // log the Magento + the system's PHP configuration
            $application->log(sprintf('Magento Edition: %s', $this->getConfiguration()->getMagentoEdition()), LogLevel::DEBUG);
            $application->log(sprintf('Magento Version: %s', $this->getConfiguration()->getMagentoVersion()), LogLevel::DEBUG);
            $application->log(sprintf('PHP Version: %s', phpversion()), LogLevel::DEBUG);
            $application->log(sprintf('App Version: %s', $application->getVersion()), LogLevel::DEBUG);
            $application->log('-------------------- Loaded Extensions -----------------------', LogLevel::DEBUG);
            $application->log(implode(', ', $loadedExtensions = get_loaded_extensions()), LogLevel::DEBUG);
            $application->log('--------------------------------------------------------------', LogLevel::DEBUG);

            // write a warning for low performance, if XDebug extension is activated
            if (in_array('xdebug', $loadedExtensions)) {
                $application->log('Low performance exptected, as result of enabled XDebug extension!', LogLevel::WARNING);
            }
        }

        // log a message that import has been started
        $application->log(
            sprintf(
                'Now start import with serial %s [%s => %s]',
                $application->getSerial(),
                $this->getConfiguration()->getEntityTypeCode(),
                $this->getConfiguration()->getOperationName()
            ),
            LogLevel::INFO
        );
    }

    /**
     * Returns the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }
}
