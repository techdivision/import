<?php

/**
 * TechDivision\Import\Subjects\SubjectFactory
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

namespace TechDivision\Import\Subjects;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * A generic subject factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectFactory implements SubjectFactoryInterface
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
     * Factory method to create new subject instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return \TechDivision\Import\Subjects\SubjectInterface The subject instance
     */
    public function createSubject(SubjectConfigurationInterface $subjectConfiguration)
    {

        // load the subject instance from the DI container and set the subject configuration
        $subjectInstance = $this->container->get($subjectConfiguration->getId());
        $subjectInstance->setConfiguration($subjectConfiguration);

        // load the import adapter instance from the DI container and set it on the subject instance
        $subjectInstance->setImportAdapter($this->container->get($subjectConfiguration->getImportAdapter()->getId()));

        // query whether or not we've a subject instance that implements the exportable subject interface
        if ($subjectInstance instanceof ExportableSubjectInterface) {
            $subjectInstance->setExportAdapter($this->container->get($subjectConfiguration->getExportAdapter()->getId()));
        }

        // return the initialized subject instance
        return $subjectInstance;
    }
}
