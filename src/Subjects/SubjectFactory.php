<?php

/**
 * TechDivision\Import\Subjects\SubjectFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\SystemLoggerTrait;
use TechDivision\Import\Adapter\ImportAdapterInterface;
use TechDivision\Import\Adapter\ExportAdapterInterface;
use TechDivision\Import\Adapter\ImportAdapterFactoryInterface;
use TechDivision\Import\Adapter\ExportAdapterFactoryInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Subjects\I18n\DateConverterFactoryInterface;
use TechDivision\Import\Subjects\I18n\NumberConverterFactoryInterface;

/**
 * A generic subject factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SubjectFactory implements SubjectFactoryInterface
{

    /**
     * The trait that provides basic filesystem handling functionality.
     *
     * @var TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The number converter factory instance.
     *
     * @var \TechDivision\Import\Subjects\I18n\NumberConverterFactoryInterface
     */
    protected $numberConverterFactory;

    /**
     * The date converter factory instance.
     *
     * @var \TechDivision\Import\Subjects\I18n\DateConverterFactoryInterface
     */
    protected $dateConverterFactory;

    /**
     * Initialize the factory with the DI container instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface          $container              The DI container instance
     * @param \Doctrine\Common\Collections\Collection                            $systemLoggers          The collection with the system loggers instances
     * @param \TechDivision\Import\Subjects\I18n\NumberConverterFactoryInterface $numberConverterFactory The number converter factory instance
     * @param \TechDivision\Import\Subjects\I18n\DateConverterFactoryInterface   $dateConverterFactory   The date converter factory instance
     */
    public function __construct(
        ContainerInterface $container,
        Collection $systemLoggers,
        NumberConverterFactoryInterface $numberConverterFactory,
        DateConverterFactoryInterface $dateConverterFactory
    ) {
        $this->container = $container;
        $this->systemLoggers = $systemLoggers;
        $this->numberConverterFactory = $numberConverterFactory;
        $this->dateConverterFactory = $dateConverterFactory;
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
        $importAdapter = $this->container->get($subjectConfiguration->getImportAdapter()->getId());

        // query whether or not we've found a factory or the instance itself
        if ($importAdapter instanceof ImportAdapterInterface) {
            $subjectInstance->setImportAdapter($importAdapter);
            // log a warning, that this is deprecated
            $this->getSystemLogger()->warning(
                sprintf(
                    'Direct injection of import adapter with DI ID "%s" is deprecated since version 3.0.0, please use factory instead',
                    $subjectConfiguration->getImportAdapter()->getId()
                )
            );
        } elseif ($importAdapter instanceof ImportAdapterFactoryInterface) {
            $subjectInstance->setImportAdapter($importAdapter->createImportAdapter($subjectConfiguration));
        } else {
            throw new \Exception(
                sprintf(
                    'Expected either an instance of ImportAdapterInterface or ImportAdapterFactoryInterface for DI ID "%s"',
                    $subjectConfiguration->getImportAdapter()->getId()
                )
            );
        }

        // query whether or not we've a subject instance that implements the exportable subject interface
        if ($subjectInstance instanceof ExportableSubjectInterface) {
            // load the export adapter instance from the DI container and set it on the subject instance
            $exportAdapter = $this->container->get($subjectConfiguration->getExportAdapter()->getId());

            // query whether or not we've found a factory or the instance itself
            if ($exportAdapter instanceof ExportAdapterInterface) {
                // inject the export adapter into the subject
                $subjectInstance->setExportAdapter($exportAdapter);
                // log a warning, that this is deprecated
                $this->getSystemLogger()->warning(
                    sprintf(
                        'Direct injection of export adapter with DI ID "%s" is deprecated since version 3.0.0, please use factory instead',
                        $subjectConfiguration->getExportAdapter()->getId()
                    )
                );
            } elseif ($exportAdapter instanceof ExportAdapterFactoryInterface) {
                $subjectInstance->setExportAdapter($exportAdapter->createExportAdapter($subjectConfiguration));
            } else {
                throw new \Exception(
                    sprintf(
                        'Expected either an instance of ExportAdapterInterface or ExportAdapterFactoryInterface for DI ID "%s"',
                        $subjectConfiguration->getExportAdapter()->getId()
                    )
                );
            }
        }

        // load te number converter instance from the DI container and set it on the subject instance
        if ($subjectInstance instanceof NumberConverterSubjectInterface) {
            $subjectInstance->setNumberConverter($this->numberConverterFactory->createNumberConverter($subjectConfiguration));
        }

        // load te date converter instance from the DI container and set it on the subject instance
        if ($subjectInstance instanceof DateConverterSubjectInterface) {
            $subjectInstance->setDateConverter($this->dateConverterFactory->createDateConverter($subjectConfiguration));
        }

        // load the filesystem adapter instance from the DI container and set it on the subject instance
        if ($subjectInstance instanceof FilesystemSubjectInterface) {
            $filesystemAdapterFactory = $this->container->get($subjectConfiguration->getFilesystemAdapter()->getId());
            $subjectInstance->setFilesystemAdapter($filesystemAdapterFactory->createFilesystemAdapter($subjectConfiguration));
        }

        // return the initialized subject instance
        return $subjectInstance;
    }
}
