<?php

/**
 * TechDivision\Import\Repositories\ImageTypeRepository
 *
 * PHP version 7
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;

/**
 * Repository implementation to load image type data.
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImageTypeRepository extends AbstractRepository implements ImageTypeRepositoryInterface
{
    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * The prepared statement to load the default store.
     *
     * @var \PDOStatement
     */
    protected $imageTypesByEntityTypeCodeAndFrontendInputStmt;

    /**
     * The mapping for the image/thumbnail image types
     *
     * @var array
     */
    protected $defaultMappings = [
        'image' => 'base_image',
        'thumbnail' => 'thumbnail_image',
    ];

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->imageTypesByEntityTypeCodeAndFrontendInputStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::IMAGE_TYPES));
    }

    /**
     * Return an array with all available image types for the passed entity type code
     * and frontend input type with the type code as key.
     *
     * @return array The available image types
     */
    public function findAll()
    {
        // query whether we've already loaded the value
        if (isset($this->cache[__METHOD__])) {
            return $this->cache[__METHOD__];
        }
        
        // initialize the result array
        $result = [];

        // load and the image types from the EAV attribute table
        $this->imageTypesByEntityTypeCodeAndFrontendInputStmt->execute();

        // fetch the image types with the passed parameters
        if ($imageTypes = $this->imageTypesByEntityTypeCodeAndFrontendInputStmt->fetchAll(\PDO::FETCH_ASSOC)) {
            // iterate over the image types found
            foreach ($imageTypes as $imageType) {
                $attributeCode = $imageType[MemberNames::ATTRIBUTE_CODE];
                // map the default image types
                if (isset($this->defaultMappings[$attributeCode])) {
                    $attributeCode = $this->defaultMappings[$attributeCode];
                }

                // add the (mapped) image type
                $result[$attributeCode] = sprintf('%s_label', $attributeCode);
            }
        }

        // append to the cache
        $this->cache[__METHOD__] = $result;

        // return from the cache
        return $this->cache[__METHOD__];
    }
}
