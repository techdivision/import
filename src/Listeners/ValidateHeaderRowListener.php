<?php

/**
 * TechDivision\Import\Listeners\ValidateHeaderRowListener
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners;

use League\Event\EventInterface;
use League\Event\AbstractListener;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Subjects\SubjectInterface;

/**
 * A listener implementation that renders the ANSI art.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ValidateHeaderRowListener extends AbstractListener
{

    /**
     * Handle the event.
     *
     * @param \League\Event\EventInterface                        $event   The event that triggered the listener
     * @param \TechDivision\Import\Subjects\SubjectInterface|null $subject The subject instance
     *
     * @return void
     */
    public function handle(EventInterface $event, SubjectInterface $subject = null)
    {

        // load the header mappings from the subject instance
        $headerMappings = array_flip($subject->getHeaderMappings());

        // iterate over the ORIGINAL header
        foreach ($subject->getRow() as $headerName) {
            // query whether or not an mapping for the column exists
            if (array_key_exists($headerName, $headerMappings)) {
                // add the the validation result to the status
                $subject->mergeStatus(
                    array(
                        RegistryKeys::VALIDATIONS => array(
                            basename($subject->getFilename()) => array(
                                $subject->getLineNumber() => array(
                                    $headerName => sprintf(
                                        'Column name invalid because of header mapping [%s > %s]',
                                        $headerName,
                                        $headerMappings[$headerName]
                                    )
                                )
                            )
                        )
                    )
                );
            }
        }
    }
}
