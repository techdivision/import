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
     * @throws \InvalidArgumentException Is thrown if the passed entity doesn't contain the necessary members
     * @see \TechDivision\Import\Utils\Generators\GeneratorInterface::generate()
     */
    public function generate(array $entity = array())
    {

        // query whether or not the entity has the necessary members
        // to generate the UID for the core configuation
        if (isset($entity[MemberNames::PATH]) &&
            isset($entity[MemberNames::SCOPE]) &&
            isset($entity[MemberNames::SCOPE_ID])
        ) {
            // load the data to generate the entity with
            $path = $entity[MemberNames::PATH] ?? 'unknown';
            $scope = $entity[MemberNames::SCOPE] ?? 'unknown';
            $scopeId = $entity[MemberNames::SCOPE_ID] ?? 'unknown';
            // generate and return the entity
            return sprintf('%s/%s/%s', $scope, $scopeId, $path);
        }

        // throw an exception, if the UID can not be generated
        throw new \InvalidArgumentException('Can\'t generate UID from members of the passed entity');
    }
}
