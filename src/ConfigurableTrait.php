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

/**
 * Trait ConfigurableTrait
 * @package TASoft\Config
 */
trait ConfigurableTrait {
    /**
     * @var Config
     */
    private $config;

    /**
     * Sets the configuration of the instance
     * @param iterable|Config $config
     */
    public function setConfiguration($config) {
		if(!($config instanceof Config))
			$config = new Config($config);
		$this->config = $config;
	}

    /**
     * Gets the configuration of the instance
     * @return null|Config
     */
    public function getConfiguration(): ?Config {
		if(!isset($this->config))
			return $this->getDefaultConfiguration();
		return $this->config;
	}

    /**
     * If no configuration was set, the configurable instance will try to get a default configuration.
     * @return null|Config
     */
    public function getDefaultConfiguration(): ?Config {
	    return NULL;
	}
}