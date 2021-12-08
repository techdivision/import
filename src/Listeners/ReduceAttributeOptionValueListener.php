<?php

/**
 * TechDivision\Import\Listeners\ReduceAttributeOptionValueListener
 *
 * PHP version 7
 *
 * @author    Martin Eissenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * An listener implementation that reduces and sorts the array with the exported attribute option values.
 *
 * @author    Martin Eissenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class ReduceAttributeOptionValueListener extends AbstractListener
{

    /**
     * The registry processor instance.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * Initializes the listener with the registry processor instance.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * return the artefact name from option values
     *
     * @return string
     */
    abstract public function getArtefactName();

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {

        // try to load the availalbe artefacts from the registry processor
        if ($artefacts = $this->registryProcessor->getAttribute(CacheKeys::ARTEFACTS)) {
            // query whether or not categories are available
            if (isset($artefacts[$this->getArtefactName()])) {
                // initialize the array for the sorted und merged categories
                $toExport = array();

                // load the categories from the artefacts
                $arts = $artefacts[$this->getArtefactName()];
                // iterate over the categories
                foreach ($arts as $attributeOptionValues) {
                    foreach ($attributeOptionValues as $attributeOptionValue) {
                        // Generate unique key for artefact to avoid duplicates
                        $attributeCode = md5(\json_encode($attributeOptionValue));
                        // query whether or not the attribute code has already been processed
                        if (isset($toExport[$attributeCode])) {
                            continue;
                        }

                        // if not, add it to the array
                        $toExport[$attributeCode] = $attributeOptionValue;
                    }
                }

                // replace them in the array with the artefacts
                $artefacts[$this->getArtefactName()] = array($toExport);
                // override the old artefacts
                $this->registryProcessor->setAttribute(CacheKeys::ARTEFACTS, $artefacts, array(), array(), true);
            }
        }
    }
}
