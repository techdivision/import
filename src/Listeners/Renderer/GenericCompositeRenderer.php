<?php

/**
 * TechDivision\Import\Listeners\Renderer\GenericCompositeRenderer
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners\Renderer;

/**
 * A generic composite renderer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericCompositeRenderer implements RendererInterface
{

    /**
     * The array with the composite's renderers.
     *
     * @var \TechDivision\Import\Listeners\Renderer\RendererInterface[]
     */
    private $renderers = array();

    /**
     * Add's a new renderer to the composite.
     *
     * @param \TechDivision\Import\Listeners\Renderer\RendererInterface $renderer the renderer to add
     *
     * @return void
     */
    public function addRenderer(RendererInterface $renderer) : void
    {
        $this->renderers[] = $renderer;
    }

    /**
     * Return's the array with the composite's renderers.
     *
     * @return \TechDivision\Import\Listeners\Renderer\RendererInterface[] The array with the renderers
     */
    protected function getRenderers() : array
    {
        return $this->renderers;
    }

    /**
     * Renders the data to some output, e. g. the console or a logger.
     *
     * @return void
     */
    public function render()
    {
        foreach ($this->getRenderers() as $renderer) {
            $renderer->render();
        }
    }
}
