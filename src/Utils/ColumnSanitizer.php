<?php

/**
 * TechDivision\Import\Utils\ColumnSanitizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 7
 *
 * @author    Team CSI <csi-kolbermoor@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * Utility class for statement query sanitizing.
 *
 * @author    Team CSI <csi-kolbermoor@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ColumnSanitizer implements SanitizerInterface
{
    /**
     * Holds already processed statement column data.
     *
     * @var array
     */
    protected $queryCache = [];

    /**
     * @inheritDoc
     */
    public function execute(array $row, string $statement)
    {
        $allowedColumns = $this->getAllowedColumns($statement);

        return array_intersect_key($row, $allowedColumns);
    }

    /**
     * Determines allowed columns for current statement.
     *
     * @param string $statement
     * @return array The allowed columns for this statement
     */
    protected function getAllowedColumns(string $statement): array
    {
        $queryCacheKey = $this->getKey($statement);
        if (!isset($this->queryCache[$queryCacheKey])) {

            $statementColumns = $this->extractAllowedColumns($statement);
            $allowedColumns = array_merge($statementColumns, $this->getSpecialColumns());

            $this->queryCache[$queryCacheKey] = array_combine($allowedColumns, $allowedColumns);
        }

        return $this->queryCache[$queryCacheKey];
    }

    /**
     * Calculate internal cache key.
     *
     * crc32 is used as performance is more important than cryptographic safety.
     *
     * @param string $statement
     * @return int
     */
    protected function getKey(string $statement)
    {
        return crc32($statement);
    }

    /**
     * Extract allowed columns from statement
     *
     * @param string $sql
     * @return array
     */
    protected function extractAllowedColumns(string $sql)
    {
        $matches = [];
        preg_match_all('/:([^,\n )]*)/', $sql, $matches);

        return $matches[1];
    }

    /**
     * Return special column names, that are necessary additionally to statement columns.
     *
     * @return array
     */
    protected function getSpecialColumns()
    {
        return [EntityStatus::MEMBER_NAME];
    }
}
