<?php

/**
 * TechDivision\Import\Listeners\RenderMysqlInfoListener
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
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;

/**
 * A listener implementation that renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class RenderMysqlInfoListener extends AbstractListener
{

    /**
     * The loader instance for the MySQL info to be rendered.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * The renderer to use.
     *
     * @var \TechDivision\Import\Listeners\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * Initializes the plugin with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface              $loader   The loader instance
     * @param \TechDivision\Import\Listeners\Renderer\RendererInterface $renderer The renderer instance
     */
    public function __construct(LoaderInterface $loader, RendererInterface $renderer)
    {
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
        $this->getRenderer()->render($this->getLoader()->load(), array(0 => 30, 1 => 10, 2 => 80));
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
     * Return's the renderer instance
     *
     * @return \TechDivision\Import\Listeners\Renderer\RendererInterface The renderer instance
     */
    protected function getRenderer()
    {
        return $this->renderer;
    }
}
