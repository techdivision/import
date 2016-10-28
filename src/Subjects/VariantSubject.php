<?php

/**
 * TechDivision\Import\Subjects\VariantSubject
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Subjects;

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A SLSB that handles the process to import product variants.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class VariantSubject extends AbstractSubject
{

    /**
     * The ID of the parent product to relate the variant with.
     *
     * @var integer
     */
    protected $parentId;

    /**
     * Intializes the previously loaded global data for exactly one variants.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function setUp()
    {

        // load the entity manager and the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import process
        $status = $registryProcessor->getAttribute($this->serial);

        // load the EAV attributes we've prepared initially
        $this->eavAttributes = $status['globalData'][RegistryKeys::EAV_ATTRIBUTES];

        // load the stores we've initialized before
        $this->stores = $status['globalData'][RegistryKeys::STORES];

        // load the attribute set we've prepared intially
        $this->skuEntityIdMapping = $status['skuEntityIdMapping'];
    }

    /**
     * Clean up the global data after importing the variants.
     *
     * @return void
     * @see \Importer\Csv\Actions\ProductImportAction::prepare()
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status of the actual import process
        $registryProcessor->mergeAttributesRecursive($this->serial, array('variations' => array($this->uid => array('status' => 1))));
    }

    /**
     * Imports the content of the file with the passed filename.
     *
     * @param string  $serial The unique process serial
     * @param integer $uid    The UUID of the file to process
     *
     * @return void
     */
    public function import($serial, $uid)
    {

        try {
            // track the start time
            $startTime = microtime(true);

            // set the serial (import ID) and the UID
            $this->serial = $serial;
            $this->uid = $uid;

            // load the connection, the system logger and the registry processor
            $connection = $this->getConnection();
            $systemLogger = $this->getSystemLogger();
            $registryProcessor = $this->getRegistryProcessor();

            // load the status of the actual import process
            $status = $registryProcessor->getAttribute($serial);

            // explode the data
            $variations = $status['variations'][$uid]['variations'];
            $variationLabels = $status['variations'][$uid]['variationLabels'];

            // create an array with the variation labels (attribute code as key)
            $varLabels = array();
            foreach ($variationLabels as $variationLabel) {
                if (strstr($variationLabel, '=')) {
                    list ($key, $value) = explode('=', $variationLabel);
                    $varLabels[$key] = $value;
                }
            }

            // initialize the global global data to import a bunch
            $this->setUp();

            // log a message that the file has to be imported
            $systemLogger->info(sprintf('Now start importing variations %s', $uid));

            try {
                // start the transaction
                $connection->beginTransaction();
                // iterate over all variations and import them
                foreach ($variations as $variation) {

                    // sku=Configurable Product 48-option 2,configurable_variation=option 2
                    list ($sku, $option) = explode(',', $variation);

                    // explode the variations child ID as well as option code and value
                    list (, $childId) = explode('=', $sku);
                    list ($optionCode, $optionValue) = explode('=', $option);

                    // load the apropriate variation label
                    $varLabel = '';
                    if (isset($varLabels[$optionCode])) {
                        $varLabel = $varLabels[$optionCode];
                    }

                    // import the varition itself
                    $this->importRow(
                        array(
                            $this->skuEntityIdMapping[$childId],
                            $uid,
                            $optionValue,
                            $varLabel
                        )
                    );
                }

                // commit the transaction
                $connection->commit();

            } catch (\Exception $e) {
                // log a message with the stack trace
                $systemLogger->error($e->__toString());
                // rollback the transaction
                $connection->rollBack();
            }

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the variations has successfully been imported
            $systemLogger->info(sprintf('Succesfully imported variations %s in %f s', $uid, $endTime));

        } catch (\Exception $e) {
            // log a message with the stack trace
            $systemLogger->error($e->__toString());

            // update the status with the error message
            $registryProcessor->mergeAttributesRecursive($serial, array('variations' => array($uid => array('error' => $e->__toString()))));

            // TODO fine graned error handling, e. g. with a log file
        }

        // clean up the data after importing the variations
        $this->tearDown();
    }

    /**
     * Imports the passed row into the database.
     *
     * If the import failed, the exception will be catched and logged,
     * but the import process will be continued.
     *
     * @param array $row The row with the data to be imported
     *
     * @return void
     */
    public function importRow(array $row)
    {

        // load the DB connection
        $connection = $this->getConnection();

        // extract the parent/child ID as well as option value and variation label from the row
        list ($childId, $parentId, $optionValue, $variationLabel) = $row;

        // create the product relation
        $this->persistProductRelation(array($parentId, $childId));
        $this->persistProductSuperLink(array($childId, $parentId));

        // load the EAV attribute
        $eavAttribute = $this->getEavAttributeByOptionValueAndStoreId(
            $optionValue,
            $this->stores['admin'][MemberNames::STORE_ID]
        );

        // query whether or not, the parent ID have changed
        if ($this->parentId !== $parentId) {
            // preserve the parent ID
            $this->parentId = $parentId;

            // if yes, create the super attribute
            $this->persistProductSuperAttribute(
                array(
                    $parentId,
                    $eavAttribute[MemberNames::ATTRIBUTE_ID],
                    0
                )
            );

            // load the ID of the new super attribute
            $productSuperAttributeId = $connection->lastInsertId();

            // query whether or not we've to create super attribute labels
            if (empty($variationLabel)) {
                $variationLabel = $eavAttribute['frontend_label'];
            }

            // create the super attribute labels for the available stores
            foreach ($this->stores as $store) {
                // prepare the super attribute label
                $params = array(
                    $productSuperAttributeId,
                    $store[MemberNames::STORE_ID],
                    0,
                    $variationLabel
                );

                // save the super attribute label
                $this->persistProductSuperAttributeLabel($params);
            }
        }
    }

    /**
     * Return's the first EAV attribute for the passed option value and store ID.
     *
     * @param string $optionValue The option value of the EAV attributes
     * @param string $storeId     The store ID of the EAV attribues
     *
     * @return array The array with the EAV attribute
     */
    public function getEavAttributeByOptionValueAndStoreId($optionValue, $storeId)
    {
        return $this->getProductProcessor()->getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);
    }

    /**
     * Persist's the passed product relation data and return's the ID.
     *
     * @param array $productRelation The product relation data to persist
     *
     * @return void
     */
    public function persistProductRelation($productRelation)
    {
        return $this->getProductProcessor()->persistProductRelation($productRelation);
    }

    /**
     * Persist's the passed product super link data and return's the ID.
     *
     * @param array $productSuperLink The product super link data to persist
     *
     * @return void
     */
    public function persistProductSuperLink($productSuperLink)
    {
        return $this->getProductProcessor()->persistProductSuperLink($productSuperLink);
    }

    /**
     * Persist's the passed product super attribute data and return's the ID.
     *
     * @param array $productSuperAttribute The product super attribute data to persist
     *
     * @return void
     */
    public function persistProductSuperAttribute($productSuperAttribute)
    {
        return $this->getProductProcessor()->persistProductSuperAttribute($productSuperAttribute);
    }

    /**
     * Persist's the passed product super attribute label data and return's the ID.
     *
     * @param array $productSuperAttributeLabel The product super attribute label data to persist
     *
     * @return void
     */
    public function persistProductSuperAttributeLabel($productSuperAttributeLabel)
    {
        return $this->getProductProcessor()->persistProductSuperAttributeLabel($productSuperAttributeLabel);
    }
}
