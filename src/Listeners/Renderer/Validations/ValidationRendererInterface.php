<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\ValidationRendererInterface
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners\Renderer\Validations;

use TechDivision\Import\Listeners\Renderer\RendererInterface;

/**
 * The interface for validation renderer implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ValidationRendererInterface extends RendererInterface
{

    /**
     * Renders the validations to some output, e. g. the console or a logger.
     *
     * @param array $validations The validations to render
     *
     * @return void
     */
    public function render(array $validations = array());
}
