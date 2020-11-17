<?php

/**
 * TechDivision\Import\Utils\DebugUtil
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
 * @link      https://github.com/techdivision/import-cli
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use Doctrine\Common\Collections\Collection;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Listeners\Renderer\RendererInterface;

/**
 * A utility class to create cache keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli
 * @link      http://www.techdivision.com
 */
class DebugUtil implements DebugUtilInterface
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
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * The debug dump renderer.
     *
     * @var \TechDivision\Import\Listeners\Renderer\RendererInterface
     */
    private $renderer;

    /**
     * Initializes the plugin with the application instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface  $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration     The configuration instance
     * @param \Doctrine\Common\Collections\Collection                   $systemLoggers     The array with the system loggers instances
     * @param \TechDivision\Import\Listeners\Renderer\RendererInterface $renderer          The debug dump renderer instance
     */
    public function __construct(
        RegistryProcessorInterface $registryProcessor,
        ConfigurationInterface $configuration,
        Collection $systemLoggers,
        RendererInterface $renderer
    ) {
        $this->registryProcessor = $registryProcessor;
        $this->configuration = $configuration;
        $this->systemLoggers = $systemLoggers;
        $this->renderer = $renderer;
    }

    /**
     * Return's the registry processor instance.
     *
     * @return \TechDivision\Import\Services\RegistryProcessorInterface The registry processor instance
     */
    private function getRegistryProcessor() : RegistryProcessorInterface
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\Configuration\ConfigurationInterface The configuration instance
     */
    private function getConfiguration() : ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * Return's the debug dump renderer instance.
     *
     * @return \TechDivision\Import\Listeners\Renderer\RendererInterface The debug dump renderer instance
     */
    private function getRenderer() : RendererInterface
    {
        return $this->renderer;
    }

    /**
     * The method to extract an archive that has already been created by a previous
     * import back into the source directory.
     *
     * @param string $serial The serial of the archive to extract
     *
     * @return void
     * @throws \Exception Is thrown, if the archive can not be extracted back into the source directory
     */
    public function extractArchive(string $serial) : void
    {

        // clear the filecache
        clearstatcache();

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // query whether or not the configured source directory is available
        if (isset($status[RegistryKeys::SOURCE_DIRECTORY])) {
            $sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY];
        } else {
            throw new \Exception('Source directory is not available!');
        }

        // try to load the archive directory
        $archiveDir = $this->getConfiguration()->getArchiveDir();

        // query whether or not the specified archive directory already exists
        if ($archiveDir === null) {
            $archiveDir = sprintf('var/import_history');
        }

        // initialize the ZIP instance
        $zip = new \ZipArchive();

        // prepare the archive filename
        $archiveFile = sprintf('%s/%s.zip', $archiveDir, $serial);

        // query whether or not the directory already exists
        if (is_dir($sourceDir)) {
            // log a warning and stop extracting the files
            $this->getSystemLogger()->warning(
                sprintf(
                    'Won\'t extract artefacts of "%s" for serial "%s" to directory "%s", because directory already exists',
                    $archiveFile,
                    $serial,
                    $sourceDir
                )
            );

            // stop processing the file extraction of the ZIP file
            return;
        }

        // try to open and extract the ZIP archive
        if (is_file($archiveFile)) {
            if ($zip->open($archiveFile) === true) {
                $zip->extractTo($sourceDir);
                $zip->close();
            } else {
                throw new \Exception(sprintf('Can\'t extract archive "%s" back into source directory', $archiveFile));
            }
        } else {
            $this->getSystemLogger()->debug(sprintf('"%s" is not available and can not be extracted therefore', $archiveFile));
        }

        // log a message that the file has successfully been extracted
        $this->getSystemLogger()->info(sprintf('Successfully extracted artefacts for serial "%s" to directory %s', $serial, $sourceDir));
    }

    /**
     * The method to create the debugging artefacts in the appropriate directory.
     *
     * @param string $serial The serial to prepare the dump for
     *
     * @return void
     * @throws \Exception Is thrown, if the configuration can not be dumped
     */
    public function prepareDump(string $serial) : void
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // clear the filecache
        clearstatcache();

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // render the debug artefacts
        $this->getRenderer()->render($serial);

        // log a message that the debug artefacts has successfully been rendered
        $this->getSystemLogger()->info(sprintf('Successfully rendered the debug artefacts for serial "%s"', $serial));
    }

    /**
     * The method to create the debug dump with all artefacts and reports.
     *
     * @param string $serial The serial to create the dump for
     *
     * @return string $filename The name of the dumpfile
     * @throws \InvalidArgumentException Is thrown, if the passed serial has no matching import to create the dump for
     */
    public function createDump(string $serial) : string
    {

        // initialize the dump filename
        $dumpFile = null;

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // clear the filecache
        clearstatcache();

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // create the ZIP archive
        $dumpFile = new \ZipArchive();
        $dumpFile->open($dumpFilename = sprintf('%s/%s.zip', sys_get_temp_dir(), $serial), \ZipArchive::CREATE);

        // init file iterator on source directory
        $fileIterator = new \FilesystemIterator($sourceDir);

        // iterate through all files and add them to the ZIP archive
        /** @var \SplFileInfo $filename */
        foreach ($fileIterator as $filename) {
            if ($filename->isFile()) {
                $dumpFile->addFile($filename, basename($filename));
            }
        }

        // save the ZIP archive
        $dumpFile->close();

        // log a message that the dump has successfully been created
        $this->getSystemLogger()->info(sprintf('Successfully created dump "%s" with artefacts for serial "%s"', $dumpFilename, $serial));

        // return the filename of the dump
        return $dumpFilename;
    }
}
