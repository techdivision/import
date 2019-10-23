<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\ConsoleTableRenderer
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

/**
 * A renderer implementation that renders the validations as table to the console output.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ConsoleTableRenderer implements ValidationRendererInterface
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
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     * @param \Symfony\Component\Console\Output\OutputInterface        $output            The output instance
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Renders the validations to some output, in that case as table to the console.
     *
     * @param array $validations The validations to render
     *
     * @return void
     */
    public function render(array $validations = array())
    {

        // do nothing, if no validation error messages are available
        if (sizeof($validations) === 0) {
            return;
        }

        // initialize the array with the validation errors
        $data = array();

        // prepare the array with the validation errors
        foreach ($validations as $filename => $attributes) {
            foreach ($attributes as $line => $errors) {
                foreach ($errors as $attributeCode => $message) {
                    $data[] = array($filename, $line, $attributeCode, $message);
                }
            }
        }

        // initialize the console table
        $table = new Table($this->output);

        // set headers and append the rows
        $table
            ->setHeaders(array('File', 'Line', 'Code', 'Error'))
            ->setRows($data);

        // render the tables
        $table->render();
    }
}
