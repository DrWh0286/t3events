<?php

namespace DWenzel\T3events\Controller\Routing;

/**
 * This file is part of the TYPO3 CMS project.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Class Route
 */
class Route
{
    /**
     * @const METHOD_REDIRECT
     */
    public const METHOD_REDIRECT = SI::REDIRECT;

    /**
     * @const METHOD_FORWARD
     */
    public const METHOD_FORWARD = SI::FORWARD;

    /**
     * @const METHOD_REDIRECT_TO_URI
     */
    public const METHOD_REDIRECT_TO_URI = 'redirectToUri';

    /**
     * @const ORIGIN_SEPARATOR
     */
    public const ORIGIN_SEPARATOR = '|';

    /**
     * Origin of route
     *
     * @var string A string of fully qualified controller class name and action method separated by ORIGIN_SEPARATOR.
     */
    protected $origin;

    /**
     * Options for routing method
     * default is
     * [
     *  SI::ACTION_NAME => null,
     *  SI::CONTROLLER_NAME => null,
     *  SI::KEY_EXTENSION_NAME => null,
     *  SI::ARGUMENTS => null,
     *  'pageUid' => null,
     *  'delay' => 0,
     *  'statusCode' => 303,
     *  'uri' => null
     * ]
     *
     * @var array
     */
    protected $options = [
        SI::ACTION_NAME => null,
        SI::CONTROLLER_NAME => null,
        SI::KEY_EXTENSION_NAME => null,
        SI::ARGUMENTS => null,
        'pageUid' => null,
        'delay' => 0,
        'statusCode' => 303,
        'uri' => null
    ];

    /**
     * Routing method which should be applied
     *
     * @var string Allowed: forward, redirect (default), redirectToUri
     */
    protected $method = self::METHOD_REDIRECT;

    /**
     * Valid routing methods
     *
     * @var array
     */
    protected static $validMethods = [
        self::METHOD_REDIRECT,
        self::METHOD_FORWARD,
        self::METHOD_REDIRECT_TO_URI
    ];

    /**
     * Route constructor.
     *
     * @var string $origin Pipe separated string of fully qualified controller class name and action method
     */
    public function __construct($origin)
    {
        $this->origin = $origin;
    }

    /**
     * Gets the origin
     * The origin serves as identifier for this route
     *
     * @return string A pipe separated string of controller class name and action
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Gets the routing method
     *
     * @return string Allowed: redirect (default), forward, redirectToUri
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the routing method
     * This method implements a fluent interface.
     *
     * @param string $method Routing method. Allowed: redirect, forward, redirectToUri
     * @return Route The current route instance.
     */
    public function setMethod($method)
    {
        if (in_array($method, static::$validMethods)) {
            $this->method = $method;
        }

        return $this;
    }

    /**
     * Returns the options for routing method
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an option value
     *
     * @param string $name Name of the option
     * @return mixed The Option value or null when option is not set
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Sets an option value
     * This method implements a fluent interface.
     *
     * @param string $name Option name
     * @param mixed $value Option value
     * @return Route The current route instance.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Set the options
     * This method implements a fluent interface.
     * Expects an array of
     * [
     *  <optionName> => <optionValue>
     * ]
     *
     * @param array $options An array of option
     * @return Route The current route instance.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Tells if an option is set
     *
     * @param string $name Option name
     * @return bool true if the option is set, otherwise false
     */
    public function hasOption($name): bool
    {
        return array_key_exists($name, $this->options);
    }
}
