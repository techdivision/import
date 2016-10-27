<?php

/**
 * TechDivision\Import\Utils\PropertyKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Utils;

/**
 * A SSB providing process registry functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class PropertyKeys
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Key for the property with the maximum threads value.
     *
     * @var string
     */
    const MAX_THREADS = 'max.threads';

    /**
     * Key for the property with the bunch size value.
     *
     * @var string
     */
    const BUNCH_SIZE = 'bunch.size';

    /**
     * Key for the property with the source date format.
     *
     * @var string
     */
    const SOURCE_DATE_FORMAT = 'source.date-format';

    /**
     * Key for the property with the magento edition, EE or CE.
     *
     * @var string
     */
    const MAGENTO_EDITION = 'magento.edition';

    /**
     * Key for the property with the magento versior, e. g. 2.1.0.
     *
     * @var string
     */
    const MAGENTO_VERSION = 'magento.version';
}