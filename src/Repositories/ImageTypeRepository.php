<?php

/**
 * TechDivision\Import\Repositories\ImageTypeRepository
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

/**
 * Repository implementation to load image type data.
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImageTypeRepository extends AbstractRepository implements LinkTypeRepositoryInterface
{

    /**
     * The array with the Magento 2 default image types and their label columns.
     *
     * @var array
     */
    const IMAGE_TYPES = array(
        'base_image'      => 'base_image_label',
        'small_image'     => 'small_image_label',
        'swatch_image'    => 'swatch_image_label',
        'thumbnail_image' => 'thumbnail_image_label'
    );

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Extends the global image types. Override this function in case
     * you want to extends the image types.
     *
     * @return array The array with the extended image types
     */
    public function extendsImageTypesFromDatabase()
    {
        return array();
    }

    /**
     * Return's an array with all available image types with the link type code as key.
     *
     * @return array The available image types
     */
    public function findAll()
    {
        // query whether or not we've already loaded the value
        if (!isset($this->cache[__METHOD__])) {
            // append the image types to the cache
            $imageTypesFromDB = $this->extendsImageTypesFromDatabase();
            $imageTypes = array_merge(static::IMAGE_TYPES, $imageTypesFromDB);
            $this->cache[__METHOD__] = $imageTypes;
        }
        // return the image types from the cache
        return $this->cache[__METHOD__];
    }
}
