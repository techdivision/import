<?php

/**
 * TechDivision\Import\Subjects\ExportableSubjectInterface
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

use TechDivision\Import\Adapter\ExportAdapterInterface;

/**
 * The interface for all subject implementations that supports an artefact export.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface ExportableSubjectInterface
{

    /**
     * Set's the exporter adapter instance.
     *
     * @param \TechDivision\Import\Adapter\ExportAdapterInterface $exportAdapter The exporter adapter instance
     *
     * @return void
     */
    public function setExportAdapter(ExportAdapterInterface $exportAdapter);

    /**
     * Return's the exporter adapter instance.
     *
     * @return \TechDivision\Import\Adapter\ExportAdapterInterface The exporter adapter instance
     */
    public function getExportAdapter();

    /**
     * Return's the artefacts for post-processing.
     *
     * @return array The artefacts
     */
    public function getArtefacts();

    /**
     * Return the artefacts for the passed type and entity ID.
     *
     * @param string $type     The artefact type, e. g. configurable
     * @param string $entityId The entity ID to return the artefacts for
     *
     * @return array The array with the artefacts
     * @throws \Exception Is thrown, if no artefacts are available
     */
    public function getArtefactsByTypeAndEntityId($type, $entityId);

    /**
     * Add the passed product type artefacts to the product with the
     * last entity ID.
     *
     * @param string $type      The artefact type, e. g. configurable
     * @param array  $artefacts The product type artefacts
     *
     * @return void
     * @uses \TechDivision\Import\Product\Subjects\BunchSubject::getLastEntityId()
     */
    public function addArtefacts($type, array $artefacts);

    /**
     * Create's and return's a new empty artefact entity.
     *
     * @param array $columns             The array with the column data
     * @param array $originalColumnNames The array with a mapping from the old to the new column names
     *
     * @return array The new artefact entity
     */
    public function newArtefact(array $columns, array $originalColumnNames = array());

    /**
     * Export's the artefacts to CSV files.
     *
     * @param integer $timestamp The timestamp part of the original import file
     * @param string  $counter   The counter part of the origin import file
     *
     * @return void
     */
    public function export($timestamp, $counter);

    /**
     * Set's the ID of the product that has been created recently.
     *
     * @param string $lastEntityId The entity ID
     *
     * @return void
     */
    public function setLastEntityId($lastEntityId);

    /**
     * Return's the ID of the product that has been created recently.
     *
     * @return string The entity Id
     */
    public function getLastEntityId();
}
