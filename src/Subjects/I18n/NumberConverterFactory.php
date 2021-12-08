<?php

/**
 * TechDivision\Import\Subjects\I18n\NumberConverterFactory
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Subjects\I18n;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Factory for number converter instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class NumberConverterFactory implements NumberConverterFactoryInterface
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
     * Creates and returns the number converter instance for the subject with the passed configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subject The subject to create the number converter for
     *
     * @return \TechDivision\Import\Subjects\I18n\NumberConverterInterface The number converter instance
     */
    public function createNumberConverter(SubjectConfigurationInterface $subject)
    {

        // create a new number converter instance for the subject with the passed configuration
        $numberConverter = $this->container->get($subject->getNumberConverter()->getId());
        $numberConverter->setSubjectConfiguration($subject);

        // return the number converter instance
        return $numberConverter;
    }
}
