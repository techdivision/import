<?php

/**
 * TechDivision\Import\Execution\ExecutionContext
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */
namespace TechDivision\Import\Execution;

use TechDivision\Import\Configuration\ExecutionContextInterface;

/**
 * Class that contains data about the actual execution context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-configuration-jms
 * @link      http://www.techdivision.com
 */
class ExecutionContext implements ExecutionContextInterface
{

    /**
     * The Magento Edition to use in the actual execution context.
     *
     * @var string
     */
    private $magentoEdition;

    /**
     * The Entity Type Code to use in the actual execution context.
     *
     * @var string
     */
    private $entityTypeCode;

    /**
     * Initialize the instance with the actual context data.
     *
     * @param string $magentoEdition The Magento Edition to use in the actual execution context
     * @param string $entityTypeCode The Entity Type Code to use in the actual execution context
     */
    public function __construct($magentoEdition, $entityTypeCode)
    {
        $this->magentoEdition = $magentoEdition;
        $this->entityTypeCode = $entityTypeCode;
    }

    /**
     * Return's the Magento Edition to use in the actual execution context.
     *
     * @return string The Magento Edition
     */
    public function getMagentoEdition()
    {
        return $this->magentoEdition;
    }

    /**
     * Return's the Entity Type Code to use in the actual execution context.
     *
     * @return string The Entity Type
     */
    public function getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }
}
