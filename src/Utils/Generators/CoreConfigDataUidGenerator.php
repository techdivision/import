<?php

/**
 * TechDivision\Import\Utils\Generators\CoreConfigDataUidGenerator
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

namespace TechDivision\Import\Utils\Generators;

use TechDivision\Import\Utils\MemberNames;

/**
 * Generator implementation for the 'core_config_data' entity.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CoreConfigDataUidGenerator implements GeneratorInterface
{

    /**
     * Create a unique identifier for the passed 'core_config_data' entity.
     *
     * @param array $entity The entity to generate the UID for
     *
     * @return string The unique identifier
     * @see \TechDivision\Import\Utils\Generators\GeneratorInterface::generate()
     */
    public function generate(array $entity)
    {

        // load the data to generate the entity with
        $path = $entity[MemberNames::PATH];
        $scope = $entity[MemberNames::SCOPE];
        $scopeId = $entity[MemberNames::SCOPE_ID];

        // generate and return the entity
        return sprintf('%s/%s/%s', $scope, $scopeId, $path);
    }
}
