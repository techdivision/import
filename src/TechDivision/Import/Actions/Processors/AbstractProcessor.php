<?php

/**
 * TechDivision\Import\Actions\Processors\ProductPersistProcessor
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

namespace TechDivision\Import\Actions\Processors;

use TechDivision\Import\Utils\SqlStatements;

/**
 * An abstract respository implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
abstract class AbstractProcessor implements ProcessorInterface
{

    /**
     * The Magento Edition to use, EE or CE.
     *
     * @var string
     */
    protected $magentoEdition;

    /**
     * The Magento Version to use, e. g. 2.1.0
     *
     * @var string
     */
    protected $magentoVersion;

    /**
     * The PDO connection instance.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * Return's the passed statement from the Magento specific
     * utility class.
     *
     * @return string The utility class name
     */
    protected function getUtilityClassName()
    {
        return SqlStatements::getUtilityClassName($this->getMagentoEdition(), $this->getMagentoVersion());
    }

    /**
     * Set's the Magento edition, EE or CE.
     *
     * @param string $magentoEdition The Magento edition
     *
     * @return void
     */
    public function setMagentoEdition($magentoEdition)
    {
        $this->magentoEdition = $magentoEdition;
    }

    /**
     * Return's the Magento edition, EE or CE.
     *
     * @return string The Magento edition
     */
    public function getMagentoEdition()
    {
        return $this->magentoEdition;
    }

    /**
     * Set's the Magento edition, EE or CE.
     *
     * @param string $magentoVersion The Magento edition
     *
     * @return void
     */
    public function setMagentoVersion($magentoVersion)
    {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * Return's the Magento version, e. g. 2.1.0.
     *
     * @return string The Magento version
     */
    public function getMagentoVersion()
    {
        return $this->magentoVersion;
    }

    /**
     * Sets's the initialized PDO connection.
     *
     * @param \PDO $connection The initialized PDO connection
     *
     * @return void
     */
    public function setConnection(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return's the initialized PDO connection.
     *
     * @return \PDO The initialized PDO connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
