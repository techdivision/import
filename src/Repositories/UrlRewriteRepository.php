<?php

/**
 * TechDivision\Import\Repositories\UrlRewriteRepository
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

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\MemberNames;

/**
 * Repository implementation to load URL rewrite data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteRepository extends AbstractRepository
{

    /**
     * The prepared statement to load the existing URL rewrites by their entity type and ID.
     *
     * @var \PDOStatement
     */
    protected $urlRewritesByEntityTypeAndEntityIdStmt;

    /**
     * The prepared statement to load the existing URL rewrites by their entity type, entity and store ID.
     *
     * @var \PDOStatement
     */
    protected $urlRewritesByEntityTypeAndEntityIdAndStoreIdStmt;

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // load the utility class name
        $utilityClassName = $this->getUtilityClassName();

        // initialize the prepared statements
        $this->urlRewritesByEntityTypeAndEntityIdStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID));
        $this->urlRewritesByEntityTypeAndEntityIdAndStoreIdStmt =
            $this->getConnection()->prepare($this->getUtilityClass()->find($utilityClassName::URL_REWRITES_BY_ENTITY_TYPE_AND_ENTITY_ID_AND_STORE_ID));
    }

    /**
     * Return's an array with the URL rewrites for the passed entity type and ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityId($entityType, $entityId)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE => $entityType,
            MemberNames::ENTITY_ID   => $entityId
        );

        // load and return the URL rewrites
        $this->urlRewritesByEntityTypeAndEntityIdStmt->execute($params);
        return $this->urlRewritesByEntityTypeAndEntityIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Return's an array with the URL rewrites for the passed entity type, entity and store ID.
     *
     * @param string  $entityType The entity type to load the URL rewrites for
     * @param integer $entityId   The entity ID to load the URL rewrites for
     * @param integer $storeId    The store ID to load the URL rewrites for
     *
     * @return array The URL rewrites
     */
    public function findAllByEntityTypeAndEntityIdAndStoreId($entityType, $entityId, $storeId)
    {

        // initialize the params
        $params = array(
            MemberNames::ENTITY_TYPE => $entityType,
            MemberNames::ENTITY_ID   => $entityId,
            MemberNames::STORE_ID    => $storeId
        );

        // load and return the URL rewrites
        $this->urlRewritesByEntityTypeAndEntityIdAndStoreIdStmt->execute($params);
        return $this->urlRewritesByEntityTypeAndEntityIdAndStoreIdStmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
