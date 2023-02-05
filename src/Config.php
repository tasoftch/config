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

namespace TASoft\Config;

use ArrayAccess;
use Countable;
use Iterator;
use ReturnTypeWillChange;
use Serializable;
use TASoft\Config\Controller\AbstractController;

/**
 * The configuration container
 * @package TASoft\Config
 */
class Config implements Countable, Iterator, ArrayAccess {
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var bool
     */
    private $transformsToConfig = true;


    /**
     * The prefix is a dot separated string containing the property names until this instance.
     * By default, TASoft Config creates it automatically for child configurations.
     * @return string
     */
    public function getPrefix() {
        return$this->prefix;
    }

    /**
     * @return array
     */
    public function __debugInfo() {
		return $this->data;
	}

    /**
     * Config constructor.
     * @param iterable $array
     * @param string $prefix
     */
    public function __construct(iterable $array = NULL, string $prefix = "", bool $transformChildConfigurations = true)
    {
        $this->setTransformsToConfig($transformChildConfigurations);

        if(is_iterable($array)) {
            foreach ($array as $key => $value) {
                $this->data[$key] = $this->_convertedValue($value, $key, $prefix);
            }
        }

        $this->prefix = $prefix;
    }

    /**
     * Get all keys from configuration
     * @return array
     */
    public function getKeys() {
	    return array_keys($this->data);
    }

    /**
     * Get all values from configuration
     * @return array
     */
    public function getValues() {
        return array_values($this->data);
    }

    /**
     * Get internal configuration
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function defaultValueForName($name) {
	    return AbstractController::_tasoft_priv_getDefaultValueForConfig($name, $this);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name) {
	    return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return AbstractController::_tasoft_priv_getValue($this, $name, $this->data[$name]);
        }

        return $this->defaultValueForName($name);
    }

    public function __set($name, $value)
    {
        if($name) {
            $this->data[$name] = $this->_convertedValue($value, $name, $this->getPrefix());
        }
    }

    public function __unset($name)
    {
        if(isset($this->data[$name]))
            unset($this->data[$name]);
    }

    /**
     * Converts the instance to an array
     * @param bool $recursive Converts containing configurations recursive as well.
     * @return array
     */
    public function toArray($recursive = true): array
	{
        $array = [];
        $data  = $this->data;

        /** @var self $value */
        foreach ($data as $key => $value) {
            if ($recursive && $value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
	{
		return count($this->data);
	}

    /**
     * @inheritdoc
     */
    public function current(): mixed
    {
        $value = current($this->data);
        return AbstractController::_tasoft_priv_getValue($this, $this->key(), $value);
    }

    /**
     * @inheritdoc
     */
    public function key(): string|int|null
	{
        return key($this->data);
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function rewind()
    {
        reset($this->data);
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function offsetSet($offset, $value)
    {
        if(is_null($value) && isset($this->data[$offset]))
            unset($this->data[$offset]);

        if(!is_null($offset))
            $this->data[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange] public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Merges the passed configuration with the instance.
     * It will add and/or overwrite the properties of the instance with the passed matching properties of merge.
     *
     * @param Config $merge
     * @return $this
     */
    public function merge(Config $merge)
    {
        foreach ($merge as $key => $value) {
            if (array_key_exists($key, $this->data)) {
                if ($value instanceof self && $this->data[$key] instanceof self) {
                    $this->data[$key]->merge($value);
                } else {
                    if ($value instanceof self) {
                        $this->data[$key] = new static($value->toArray(), $this->prefix ? "$this->prefix.$key" : "$key");
                    } else {
                        $this->data[$key] = $value;
                    }
                }
            } else {
                if ($value instanceof self) {
                    $this->data[$key] = new static($value->toArray(), $this->prefix ? "$this->prefix.$key" : "$key");
                } else {
                    $this->data[$key] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Returns true if the configuration transforms added values to Config objects automatically
     * @return bool
     */
    public function getTransformsToConfig(): bool
    {
        return $this->transformsToConfig;
    }

    /**
     * Set whether the configuration should transform added values to Config objects automatically
     * @param bool $transformToConfig
     */
    public function setTransformsToConfig(bool $transformsToConfig): void
    {
        $this->transformsToConfig = $transformsToConfig;
    }

    /**
     * Internal method to convert any value to a new configuration object if required.
     *
     * @internal
     * @param $value
     * @param $key
     * @param $prefix
     * @return Config
     */
    private function _convertedValue($value, $key, $prefix) {
        if($this->getTransformsToConfig()) {
            if(is_iterable($value))
                $value = new static($value, $prefix ? "$prefix.$key" : $key, true);
        }
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {

    }

    public function unserialize($serialized)
    {

    }

	public function __serialize(): array
	{
		return [$this->prefix, $this->transformsToConfig, $this->data];
	}

	public function __unserialize(array $data): void
	{
		list($this->prefix, $this->transformsToConfig, $this->data) = $data;
	}
}