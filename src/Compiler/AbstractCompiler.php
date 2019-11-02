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

namespace TASoft\Config\Compiler;

use RecursiveIterator;
use TASoft\Config\Config;
use Traversable;

abstract class AbstractCompiler implements CompilerInterface {

    /**
     * @inheritDoc
     */
	public function compile(): bool {
		$traversable = $this->getCompilerSource();
		
		$allPaths = [];
		$this->_collectFilePaths($allPaths, $traversable);
		
		$config = new Config([]);
		
		$files = [];
		
		$allPaths = array_unique($allPaths);
		
		foreach($allPaths as $idx => $path) {
			$files[] = $path;
			
			$data = @include($path);
			if(is_array($data) && count($data)) {
				$cfg = new Config($data);
				$this->mergeConfiguration($config, $cfg);
			}
		}

		return $this->getCompilerTarget()->export( $config,  $files);
	}

    /**
     * @param array $outPaths
     * @param Traversable $traversable
     * @internal
     */
    private function _collectFilePaths(array &$outPaths, Traversable $traversable) {
        foreach($traversable as $source) {
            if($source)
                $outPaths[] = $source;

            if($traversable instanceof RecursiveIterator && $traversable->hasChildren()) {
                $this->_collectFilePaths($outPaths, $traversable->getChildren());
            }
        }
    }

    /**
     * Called to merge all configuration into one collected
     *
     * @param Config $collected
     * @param Config $new
     */
	protected function mergeConfiguration(Config $collected, Config $new) {
        $collected->merge($new);
    }
}