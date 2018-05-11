<?php
/**
 * Copyright (c) 2018 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace TASoft\Config\Controller;

use TASoft\Config\Config;

/**
 * The Abstract Controller or subclasses of it are intermediate controllers for fetching configuration values.
 *
 * If a configuration fetches a value it will try to access default controller and let it handle the value before deliver.
 * You can only define one configuration controller.
 * The first call of getController on a specified class defines the current controller.
 *
 * @package TASoft\Config\Controller
 */
abstract class AbstractController
{
    /** @var AbstractController */
    private static $instance;


    /**
     * AbstractController constructor.
     * You should not instantiate controllers directly.
     * @see AbstractController::getController()
     */
    private function __construct()
    {
    }

    /**
     *
     * @return AbstractController
     */
    public static function getController() {
        if(!self::$instance)
            self::$instance = new static();
        return self::$instance;
    }

    public static function __callStatic($name, $arguments)
    {
        if(self::$instance) {
            if($name == '_tasoft_priv_getDefaultValueForConfig')
                return self::$instance->getDefaultValue($arguments[1], $arguments[0]);
        }
        if($name == '_tasoft_priv_getValue') {
            if(self::$instance)
                return self::$instance->getTransformedValue($arguments[0], $arguments[1], $arguments[2]);

            return $arguments[2];
        }
        return NULL;
    }

    /**
     * Provides a general default value, if a configuration failed to fetch required property
     * @param Config $config The configuration fetching a property
     * @param string $name The property name
     * @return mixed
     */
    abstract public function getDefaultValue(Config $config, $name);

    /**
     * Every configuration fetching properties will call this method before deliver the fetched value.
     * @param Config $config The configuration fetching a property
     * @param string $key The property value
     * @param mixed $value The fetched value
     * @return mixed
     */
    abstract public function getTransformedValue(Config $config, $key, $value);
}