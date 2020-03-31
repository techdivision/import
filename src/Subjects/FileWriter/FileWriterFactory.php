<?php

/**
 * TechDivision\Import\Subjects\FileWriter\FileWriterFactory
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

namespace TechDivision\Import\Subjects\FileWriter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class FileWriterFactory implements FileWriterFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The DI container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates and returns the file resolver instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the file resolver for
     *
     * @return \TechDivision\Import\Subjects\FileWriter\FileWriterInterface The file resolver instance
     */
    public function createFileWriter(SubjectConfigurationInterface $subject) : FileWriterInterface
    {

        // create a new file resolver instance for the subject with the passed configuration
        $fileWriter = $this->container->get($subject->getFileWriter()->getId());
        $fileWriter->setFileResolver($this->container->get($subject->getFileResolver()->getId()));

        // return the file writer instance
        return $fileWriter;
    }
}
