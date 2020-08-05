<?php

/**
 * TechDivision\Import\Handlers\OkFileHandlerFactory
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

namespace TechDivision\Import\Handlers;

use TechDivision\Import\Loaders\LoaderFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Adapter\FilesystemAdapterFactoryInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * A .OK file handler factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class OkFileHandlerFactory implements HandlerFactoryInterface
{

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * The loader factory instance used to create the loader for the proposed .OK filenames.
     *
     * @var \TechDivision\Import\Loaders\LoaderFactoryInterface
     */
    private $loaderFactory;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container     The DI container instance
     * @param \TechDivision\Import\Loaders\LoaderFactoryInterface       $loaderFactory The loader factory instance used to create the loader for the proposed .OK filenames
     */
    public function __construct(
        ContainerInterface $container,
        LoaderFactoryInterface $loaderFactory
    ) {
        $this->container = $container;
        $this->loaderFactory = $loaderFactory;
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
     * Return's the loader factory instance.
     *
     * @return  \TechDivision\Import\Loaders\LoaderFactoryInterface The loader factory instance
     */
    protected function getLoaderFactory() : LoaderFactoryInterface
    {
        return $this->loaderFactory;
    }

    /**
     * Create's and return's a new handler instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface|null $subject The subject configuration instance
     *
     * @return \TechDivision\Import\Handlers\HandlerInterface|null The new handler instance
     */
    public function createHandler(SubjectConfigurationInterface $subject = null) : HandlerInterface
    {

        // load the proposed .OK file loader
        $proposedOkFileLoader = $this->getLoaderFactory()->createLoader($subject);

        // load the filesystem adapter instance
        $filesystemAdapter = $this->getContainer()->get($subject->getFilesystemAdapter()->getId());

        // query whether or not we've a factory instance
        if ($filesystemAdapter instanceof FilesystemAdapterFactoryInterface) {
            $filesystemAdapter = $filesystemAdapter->createFilesystemAdapter($subject);
        }

        // create a new .OK file handler instance
        $handler = new OkFileHandler();
        $handler->setLoader($proposedOkFileLoader);
        $handler->setFilesystemAdapter($filesystemAdapter);

        // return the new .OK file handler instance
        return $handler;
    }
}
