<?php

/**
 * TechDivision\Import\Listeners\ResetLoaderListener
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use TechDivision\Import\Loaders\ResetAwareLoaderInterface;

/**
 * After the subject has finished it's processing, this listener causes the obsolete tier prices to be deleted.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ResetLoaderListener extends \League\Event\AbstractListener
{

    /**
     * The loader instance.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * Initializes the listener with the tier price processor.
     *
     * @param \TechDivision\Import\Loaders\ResetAwareLoaderInterface $loader The registry processor instance
     */
    public function __construct(ResetAwareLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Handle the event.
     *
     * Deletes the tier prices for all the products, which have been touched by the import,
     * and which were not part of the tier price import.
     *
     * @param \League\Event\EventInterface $event The event that triggered the listener
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->loader->reset();
    }
}
