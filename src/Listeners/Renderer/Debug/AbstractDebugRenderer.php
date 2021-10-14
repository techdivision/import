<?php

/**
 * TechDivision\Import\Listeners\Renderer\AbstractDebugRenderer
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

use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\DependencyInjectionKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A abstract debug renderer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
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
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Initializes the renderer with the configuration that has to be rendered.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The configuration instance
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers     The system logger instances
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container         The container instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        ConfigurationInterface $configuration,
        Collection $systemLoggers,
        ContainerInterface $container = null
    ) {
        $this->registryProcessor = $registryProcessor;
        $this->configuration = $configuration;
        $this->systemLoggers = $systemLoggers;
        $this->container = $container;
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
     * Return's the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The container instance
     */
    protected function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    /**
     * Returns the CLI application version.
     *
     * @return string The application version
     */
    public function getApplicationVersion() : string
    {

        // query whether or not the container is available
        if ($this->getContainer() instanceof ContainerInterface) {
            return $this->container->get(DependencyInjectionKeys::APPLICATION)->getVersion();
        }

        // return a unknown version string
        return 'Unknown';
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

        // write the data to the file with the passed name
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
