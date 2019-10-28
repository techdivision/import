<?php

/**
 * TechDivision\Import\Listeners\RenderValidationsListener
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
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * A listener implementation that renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderValidationsListener extends AbstractListener
{

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The array with the validation renderer instances.
     *
     * @var \TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface[]
     */
    protected $renderer;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface                          $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface[] $renderer          The array with the validation renderer instances
     */
    public function __construct(RegistryProcessorInterface $registryProcessor, \ArrayAccess $renderer)
    {

        // set the passed instances
        $this->registryProcessor = $registryProcessor;
        $this->renderer = $renderer;
    }

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // load the validations from the registry
        $validations = $this->getRegistryProcessor()->getAttribute(RegistryKeys::VALIDATIONS);

        // query whether or not we've validation errors
        if (is_array($validations) && sizeof($validations) > 0) {
            foreach ($this->getRenderer() as $renderer) {
                $renderer->render($validations);
            }
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

    /**
     * Return's the validation renderer instances.
     *
     * @return \TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface[] The renderer instances
     */
    protected function getRenderer()
    {
        return $this->renderer;
    }
}
