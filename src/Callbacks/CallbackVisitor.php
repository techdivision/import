<?php

/**
 * TechDivision\Import\Callbacks\CallbackVisitor
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Subjects\SubjectInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;

/**
 * Visitor implementation for a subject's callbacks.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CallbackVisitor implements CallbackVisitorInterface
{

    /**
     * The DI container builder instance.
     *
     * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
     */
    protected $container;

    /**
     * The constructor to initialize the instance.
     *
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface $container The container instance
     */
    public function __construct(TaggedContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Visitor implementation that initializes the observers of the passed subject.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject to initialize the observers for
     *
     * @return void
     */
    public function visit(SubjectInterface $subject)
    {

        // load the callback mappings
        $callbackMappings = $subject->getCallbackMappings();

        // prepare the callbacks
        foreach ($callbackMappings as $type => $callbacks) {
            $this->prepareCallbacks($subject, $callbacks, $type);
        }
    }

    /**
     * Prepare the callbacks defined in the system configuration.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject   The subject to prepare the callbacks for
     * @param array                                          $callbacks The array with the callbacks
     * @param string                                         $type      The actual callback type
     *
     * @return void
     */
    protected function prepareCallbacks(SubjectInterface $subject, array $callbacks, $type = null)
    {

        // iterate over the array with callbacks and prepare them
        foreach ($callbacks as $key => $callback) {
            // we have to initialize the type only on the first level
            if ($type == null) {
                $type = $key;
            }

            // query whether or not we've an subarry or not
            if (is_array($callback)) {
                $this->prepareCallbacks($subject, $callback, $type);
            } else {
                // create the instance of the callback/factory
                $instance = $this->container->get($callback);
                // query whether or not a factory has been specified
                if ($instance instanceof CallbackFactoryInterface) {
                    $subject->registerCallback($instance->createCallback($subject), $type);
                } elseif ($instance instanceof CallbackInterface) {
                    $subject->registerCallback($instance, $type);
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Instance of "%s" doesn\'t implement interface "%s" or "%s"',
                            $callback,
                            CallbackFactoryInterface::class,
                            CallbackInterface::class
                        )
                    );
                }
            }
        }
    }
}
