<?php

/**
 * TechDivision\Import\Listeners\Renderer\AbstractDebugRenderer
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners\Renderer\Debug;

use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * A abstract debug renderer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli
 * @link      http://www.techdivision.com
 */
abstract class AbstractDebugRenderer implements RendererInterface
{

    /**
     * The trait that provides basic system logger functionality.
     *
     * @var \TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The import processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    private $registryProcessor;

    /**
     * The configuration instance that has to be rendered.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * Initializes the renderer with the configuration that has to be rendered.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The configuration instance
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers     The system logger instances
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        ConfigurationInterface $configuration,
        Collection $systemLoggers
    ) {
        $this->registryProcessor = $registryProcessor;
        $this->configuration = $configuration;
        $this->systemLoggers = $systemLoggers;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration() : ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    protected function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
    }

    /**
     * Writes the passed data to the also passed filename.
     *
     * @param string $data     The data to write
     * @param string $filename The name of the file to write the data to
     *
     * @return void
     *
     * @throws \Exception Is thrown if the file can not be written
     */
    protected function write(string $data, string $filename) : void
    {

        // do not override the original files, if available
        if (is_file($filename)) {
            return;
        }

        // write the data to the file with the apssed name
        if (file_put_contents($filename, $data)) {
            $this->getSystemLogger()->debug(sprintf('Successfully written file %s', $filename));
        } else {
            throw new \Exception(sprintf('Can\'t write debug artefact "%s"', $filename));
        }

        // update the status
        $this->getRegistryProcessor()->mergeAttributesRecursive(
            RegistryKeys::STATUS,
            array(
                RegistryKeys::FILES => array(
                    $filename => array(
                        Uuid::uuid4()->toString() => array(
                            RegistryKeys::STATUS => 1,
                            RegistryKeys::SKIPPED_ROWS => 0,
                            RegistryKeys::PROCESSED_ROWS => 0
                        )
                    )
                )
            )
        );
    }
}
