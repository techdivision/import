<?php

/**
 * TechDivision\Import\Subjects\BundleSubject
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

use TechDivision\Import\Utils\ColumnKeys;

/**
 * A SLSB that handles the process to import product variants.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class BundleSubject extends AbstractSubject
{

    /**
     * The mapping for the SKUs to the created entity IDs.
     *
     * @var array
     */
    protected $skuEntityIdMapping = array();

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

        // load the attribute set we've prepared intially
        $this->skuEntityIdMapping = $status['skuEntityIdMapping'];

        // prepare the callbacks
        parent::setUp();
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
            $this->setSerial($serial);
            $this->setUid($uid);

            // load the connection, the system logger and the registry processor
            $connection = $this->getConnection();
            $systemLogger = $this->getSystemLogger();
            $registryProcessor = $this->getRegistryProcessor();

            // load the status of the actual import process
            $status = $registryProcessor->getAttribute($serial);

            // explode the data
            $row = $status['bundles'][$uid];

            // name=Option 1,type=select,required=1,sku=Bundle Product 1,price=0.0000,default=0,default_qty=1.0000,price_type=fixed

            // initialize the global global data to import a bunch
            $this->setUp();

            // log a message that the file has to be imported
            $systemLogger->info(sprintf('Now start importing bundles %s', $uid));

            // iterate over all variations and import them
            foreach ($row[ColumnKeys::BUNDLE_VALUES] as $bundleValues) {
                // prepare the bundle values
                foreach (explode(',', $bundleValues) as $values) {
                    list ($key, $value) = explode('=', $values);
                    $row[$key] = $value;
                }

                $row['parendId'] = $uid;
                $row['childId'] = $this->skuEntityIdMapping[$row[ColumnKeys::SKU]];

                // import the bundle itself
                $this->importRow($row);
            }

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the variations has successfully been imported
            $systemLogger->info(sprintf('Succesfully imported bundles %s in %f s', $uid, $endTime));

        } catch (\Exception $e) {
            // log a message with the stack trace
            $systemLogger->error($e->__toString());

            // update the status with the error message
            $registryProcessor->mergeAttributesRecursive($serial, array('bundles' => array($uid => array('error' => $e->__toString()))));

            // re-throw the exception
            throw $e;
        }

        // clean up the data after importing the variations
        $this->tearDown();
    }

    /**
     * Clean up the global data after importing the bundles.
     *
     * @return void
     */
    public function tearDown()
    {

        // load the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // update the status of the actual import process
        $registryProcessor->mergeAttributesRecursive($this->serial, array('bundles' => array($this->getUid() => array('status' => 1))));
    }
}
