<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\GenericConsoleTableRenderer
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

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;
use Symfony\Component\Console\Input\InputInterface;
use TechDivision\Import\Utils\InputOptionKeysInterface;

/**
 * A renderer implementation that renders the rows as table to the console output.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericConsoleTableRenderer implements RendererInterface
{

    /**
     * The input instance.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output instance.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  The input instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output instance
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
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

        // slice the number of messages, that has to be rendered, out of the rows
        $rows = array_slice($rows, 0, $this->input->getOption(InputOptionKeysInterface::RENDER_VALIDATION_ISSUES));

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
