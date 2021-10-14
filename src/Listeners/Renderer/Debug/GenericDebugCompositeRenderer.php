<?php

/**
 * TechDivision\Import\Listeners\Renderer\Debug\DebugCompositeRenderer
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners\Renderer\Debug;

use TechDivision\Import\Listeners\Renderer\GenericCompositeRenderer;

/**
 * A generic debug composite renderer instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericDebugCompositeRenderer extends GenericCompositeRenderer
{

    /**
     * Renders the data to some output, e. g. the console or a logger.
     *
     * @param string $serial The serial of the import to render the dump artefacts for
     *
     * @return void
     */
    public function render(string $serial = null)
    {
        foreach ($this->getRenderers() as $renderer) {
            $renderer->render($serial);
        }
    }
}
