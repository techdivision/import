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
     * Sanitizes and returns row data for given statement.
     *
     * @param array  $row       The current row data
     * @param string $statement The SQL statement to sanitize for
     *
     * @return array The sanitized row data
     */
    public function execute(array $row, string $statement) : array
    {
        return array_intersect_key($row, $this->getAllowedColumns($statement));
    }

    /**
     * Determines allowed columns for current statement.
     *
     * @param  string $statement The statement to determine the allowed columns for
     *
     * @return array The allowed columns for this statement
     */
    protected function getAllowedColumns(string $statement): array
    {

        // load the cache key for the statement
        $queryCacheKey = $this->getKey($statement);

        // query whether or not the query has already been processed
        if (!isset($this->queryCache[$queryCacheKey])) {
            // determine the allowed columns
            $statementColumns = $this->extractAllowedColumns($statement);
            $allowedColumns = array_merge($statementColumns, $this->getSpecialColumns());
            // register the array with the columns in the query cache
            $this->queryCache[$queryCacheKey] = array_combine($allowedColumns, $allowedColumns);
        }

        // return the array with the allowed columns
        return $this->queryCache[$queryCacheKey];
    }

    /**
     * Calculate internal cache key.
     *
     * crc32 is used as performance is more important than cryptographic safety.
     *
     * @param string $statement The statement to create the key for
     *
     * @return int
     */
    protected function getKey(string $statement) : int
    {
        return crc32($statement);
    }

    /**
     * Extract allowed columns from statement.
     *
     * @param string $sql The SQL to extract the allowed columns from
     *
     * @return array The array with the allowed columns
     */
    protected function extractAllowedColumns(string $sql)
    {

        // initialize the array for the matches and invoke the PCRE regex
        $matches = [];
        preg_match_all('/:([^,\n )]*)/', $sql, $matches);

        // return the found columns
        return $matches[1];
    }

    /**
     * Return special column names, that are necessary additionally to statement columns.
     *
     * @return array Return an array with special columns, e. g. the entity status
     */
    protected function getSpecialColumns()
    {
        return [EntityStatus::MEMBER_NAME];
    }
}
