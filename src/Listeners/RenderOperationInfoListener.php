<?php

/**
 * TechDivision\Import\Listeners\RenderOperationInfoListener
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
 * A listener implementation that renders the operation information.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderOperationInfoListener extends AbstractListener
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

        // write a warning for low performance, if XDebug extension is activated
        if (in_array('xdebug', get_loaded_extensions())) {
            $application->log('Low performance exptected, as result of enabled XDebug extension!', LogLevel::WARNING);
        }

        // prepare the operation name and the entity type code
        $operationName = sprintf('custom');
        $configuration = $this->getConfiguration();
        $entityTypeCode = $configuration->getEntityTypeCode();

        // replace the operation name with the shortcut, iv available
        if ($shortcut = $configuration->getShortcut()) {
            $operationName = $shortcut;
        }

        // log a message that import has been started
        $application->log(sprintf('Now start import with serial %s [%s => %s]', $application->getSerial(), $entityTypeCode, $operationName), LogLevel::INFO);
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
