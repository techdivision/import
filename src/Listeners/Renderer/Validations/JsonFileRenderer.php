<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\JsonFileRenderer
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

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * A renderer implementation that renders the validations as JSON content to a file in the target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class JsonFileRenderer implements ValidationRendererInterface
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * @var string
     */
    private $filename;

    /**
     * Initializes the renderer with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     * @param string                                                   $filename          File Name
     */
    public function __construct(RegistryProcessorInterface $registryProcessor, $filename = 'validations.json')
    {
        $this->registryProcessor = $registryProcessor;
        $this->filename = $filename;
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

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // try to load the status
        $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

        // query whether or not a target directory is available
        if (isset($status[RegistryKeys::TARGET_DIRECTORY])) {
            // prepare the filename to save the validation messsages to
            $filename = implode(DIRECTORY_SEPARATOR, array($status[RegistryKeys::TARGET_DIRECTORY], $this->filename));
            if (file_exists($filename)) {
                $validationData = file_get_contents($filename);
                $validationJson = \json_decode($validationData, true);
                if ($validationJson) {
                    $validations = array_merge($validationJson, $validations);
                }
            }
            // create the files inside the target directory
            file_put_contents($filename, json_encode($validations, JSON_PRETTY_PRINT));
            // register the file as artefact in the registry
            $registryProcessor->mergeAttributesRecursive(RegistryKeys::STATUS, array(RegistryKeys::FILES => array($filename => array())));
        }
    }
}
