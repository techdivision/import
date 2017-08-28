<?php

/**
 * TechDivision\Import\Plugins\CacheWarmerPlugin
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

namespace TechDivision\Import\Plugins;

use TechDivision\Import\Utils\ConfigurationKeys;
use TechDivision\Import\Utils\DependencyInjectionKeys;

/**
 * Plugin implementation to warm the repository caches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CacheWarmerPlugin extends AbstractPlugin
{

    /**
     * Array with the default cache warmers.
     *
     * @var array
     */
    protected $cacheWarmers = array(
        DependencyInjectionKeys::IMPORT_CACHE_WARMER_EAV_ATTRIBUTE_OPTION_VALUE_REPOSITORY
    );

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        // query whether or not additional cache warmers has been configured
        if ($this->getPluginConfiguration()->hasParam(ConfigurationKeys::CACHE_WARMERS)) {
            // try ot load the cache warmers and merge them with the default ones
            $this->cacheWarmers = array_merge(
                $this->cacheWarmers,
                $this->getPluginConfiguration()->getParam(ConfigurationKeys::CACHE_WARMERS)
            );
        }

        // create the instances and warm the repository caches
        foreach ($this->cacheWarmers as $id) {
            $this->getApplication()->getContainer()->get($id)->warm();
        }
    }
}
