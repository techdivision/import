<?php

/**
 * TechDivision\Import\Utils\PrimaryKeyUtil
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Dbal\Utils\PrimaryKeyUtilInterface;

/**
 * Utility class for edition based primary key handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PrimaryKeyUtil implements PrimaryKeyUtilInterface
{

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The mapping for the edition to primary key member name.
     *
     * @var array
     */
    protected $editionPrimaryKeyMemberNameMappings = array(
        EditionNamesInterface::EE => MemberNames::ROW_ID,
        EditionNamesInterface::CE => MemberNames::ENTITY_ID
    );

    /**
     * Construct a new instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the primary key member name for the actual Magento edition.
     *
     * @return string The primary key member name
     * @throws \Exception Is thrown if the edition is not supported/available
     */
    public function getPrimaryKeyMemberName()
    {

        // make sure the edition name is in upper cases
        $editionName = strtoupper($this->configuration->getMagentoEdition());

        // return the primary key member name for the actual edition
        if (isset($this->editionPrimaryKeyMemberNameMappings[$editionName])) {
            return $this->editionPrimaryKeyMemberNameMappings[$editionName];
        }

        // throw an exception if the edition is NOT supported/available
        throw new \Exception(sprintf('Found not supported/available Magento edition name "%s"', $editionName));
    }

    /**
     * Compiles the passed SQL statement.
     *
     * @param string $statement The SQL statement to compile
     *
     * @return string The compiled SQL statement
     */
    public function compile($statement)
    {
        return preg_replace(sprintf('/\$\{%s:(.*)\}/U', PrimaryKeyUtilInterface::TOKEN), $this->getPrimaryKeyMemberName(), $statement);
    }
}
