<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\GenericConsoleTableRenderer
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

namespace TechDivision\Import\Listeners\Renderer\Validations;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;

/**
 * A renderer implementation that renders the rows as table to the console output.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericConsoleTableRenderer implements RendererInterface
{

    /**
     * The output instance.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output instance
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Renders the rows to the output, in that case as table to the console.
     *
     * @param array $rows            The rows to render
     * @param array $columnMaxWidths The array with the maximum column widths
     *
     * @return void
     */
    public function render(array $rows = array(), array $columnMaxWidths = array())
    {

        // do nothing, if no validation error messages are available
        if (sizeof($rows) === 0) {
            return;
        }

        // initialize the console table/set the headers
        $table = new Table($this->output);
        $table->setHeaders(array_keys(current($rows)))->setRows($rows);

        // set the column max width, if passed
        foreach ($columnMaxWidths as $key => $width) {
            // this is because of the used symfony/console version which
            // can differ as a result of the Magento/PHP version used.
            // So the method setColumnMaxWidth() will be available up
            // from symfony/console version 4.2.0.
            if (method_exists($table, $methodName = 'setColumnMaxWidth')) {
                call_user_func(array($table, $methodName), $key, $width);
            }
        }

        // render the table
        $table->render();
    }
}
