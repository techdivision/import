<?php

/**
 * TechDivision\Import\Plugins\CreateOkFilesPlugin
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Adapter\FilesystemAdapterFactoryInterface;
use TechDivision\Import\Subjects\FileResolver\FileResolverFactoryInterface;



/**
 * Plugin that loads the global data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CreateOkFilesPlugin extends AbstractPlugin
{

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        // initialize the counter for the CSV files
        $okFilesCreated = 0;

        $configuration = $this->getConfiguration();

        $container = $this->getApplication()->getContainer();

        foreach ($configuration->getOperations() as $operation) {
            foreach ($operation as $entityTypes) {
                foreach ($entityTypes as $operationConfiguration) {
                    foreach ($operationConfiguration->getPlugins() as $plugin) {
                        foreach ($plugin->getSubjects() as $subject) {

                            if ($subject->isOkFileNeeded()) {

                                $fileResolver = $container->get('import.subject.file.resolver.simple');
                                if ($fileResolver instanceof FileResolverFactoryInterface) {
                                    $fileResolver = $fileResolver->createFileResolver($subject);
                                }

                                $filesystemAdapter = $container->get($subject->getFilesystemAdapter()->getId());
                                if ($filesystemAdapter instanceof FilesystemAdapterFactoryInterface) {
                                    $filesystemAdapter = $filesystemAdapter->createFilesystemAdapter($subject);
                                }

                                $fileResolver->setFilesystemAdapter($filesystemAdapter);
                                $fileResolver->setSubjectConfiguration($subject);

                                $fileWriter = $container->get('import.subject.file.writer.ok.file.aware');
                                $fileWriter->setFileResolver($fileResolver);

                                $okFilesCreated += $fileWriter->createOkFiles($configuration->getSerial());
                            }
                        }
                    }
                }
            }
        }

        // query whether or not we've found any CSV files
        if ($okFilesCreated === 0) {
            throw new \Exception(sprintf('Can\'t find any CSV files in source directory "%s"', $this->getSourceDir()));
        }
    }
}
