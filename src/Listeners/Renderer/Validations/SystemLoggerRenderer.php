<?php

/**
 * TechDivision\Import\Listeners\Renderer\Validations\SystemLoggerRenderer
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

namespace TechDivision\Import\Listeners\Renderer\Validations;

use TechDivision\Import\SystemLoggerTrait;
use Doctrine\Common\Collections\Collection;
use TechDivision\Import\ConfigurationInterface;

/**
 * A renderer implementation that renders the validations as JSON content to a file in the target directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class SystemLoggerRenderer implements ValidationRendererInterface
{

    /**
     * The trait that provides system logger functionality.
     *
     * @var \TechDivision\Import\SystemLoggerTrait
     */
    use SystemLoggerTrait;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the renderer with the registry processor instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     * @param \Doctrine\Common\Collections\Collection     $systemLoggers The array with the system loggers instances
     */
    public function __construct(ConfigurationInterface $configuration, Collection $systemLoggers)
    {
        $this->configuration = $configuration;
        $this->systemLoggers = $systemLoggers;
    }

    /**
     * Return's the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Renders the validations to some output, in that case as table to the console.
     *
     * @param array $validations The validations to render
     *
     * @return void
     */
    public function render(array $validations = array())
    {

        // do nothing, if no validation error messages are available
        if (sizeof($validations) === 0) {
            return;
        }

        // log a message that we've found validation errors
        foreach ($this->getSystemLoggers() as $systemLogger) {
            $systemLogger->warning(sprintf('Found validation errors in import with serial "%s"', $this->getConfiguration()->getSerial()));
        }
    }
}
