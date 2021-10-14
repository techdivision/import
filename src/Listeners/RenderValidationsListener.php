<?php

/**
 * TechDivision\Import\Listeners\RenderValidationsListener
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

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Loaders\LoaderInterface;

/**
 * A listener implementation that renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderValidationsListener extends AbstractListener
{

    /**
     * The loader instance used to load the validations from the registry.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The array with the validation renderer instances.
     *
     * @var \TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface[]
     */
    protected $renderer;

    /**
     * Initializes the listener with the loader and the render instances.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface                                      $loader   The loader instance
     * @param \TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface[] $renderer The array with the validation renderer instances
     */
    public function __construct(LoaderInterface $loader, \ArrayAccess $renderer)
    {

        // set the passed instances
        $this->loader = $loader;
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

        // load the validations
        $validations = $this->getLoader()->load();

        // query whether or not we've validation errors
        if (is_array($validations) && sizeof($validations) > 0) {
            foreach ($this->getRenderer() as $renderer) {
                $renderer->render($validations);
            }
        }
    }

    /**
     * Return's the loader instance
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader()
    {
        return $this->loader;
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
