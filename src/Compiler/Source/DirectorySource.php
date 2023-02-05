<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
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

namespace TASoft\Config\Compiler\Source;

use DirectoryIterator;
use ReturnTypeWillChange;

class DirectorySource implements SourceInterface {
	private $pattern;
	private $recursive;
	
	private $iterator;
	
	public function __construct($source, $recursive = false, $globPattern = '*.config.php') {
		$this->pattern = $globPattern;
		$this->recursive = $recursive;
		
		$this->iterator = new DirectoryIterator($source);
	}
	
	
	protected function filenameMatchesPattern($filename) {
		if(fnmatch($this->pattern, $filename))
			return true;
		return false;
	}
	
	
	#[ReturnTypeWillChange] public function rewind() {
		$this->iterator->rewind();
	}
	
	#[ReturnTypeWillChange] public function next() {
		$this->iterator->next();
	}
	
	#[ReturnTypeWillChange] public function valid() {
		while($this->iterator->valid()) {
			$fn = $this->iterator->current()->getFilename();
			if($fn =='.' || $fn == '..') {
				$this->next();
				continue;
			}
			
			if(!is_dir($this->iterator->current()->getPathname()) && !$this->filenameMatchesPattern($fn)) {
				$this->next();
				continue;
			}
			
			return true;
		}
		return false;
	}
	
	#[ReturnTypeWillChange] public function current() {
		$pn = realpath($this->iterator->current()->getPathname());
		return is_file($pn) ? $pn : NULL;
	}
	
	#[ReturnTypeWillChange] public function key() {
		return $this->iterator->key();
	}
	
	#[ReturnTypeWillChange] public function hasChildren() { return $this->recursive && $this->iterator->isDir(); }
	#[ReturnTypeWillChange] public function getChildren() {
		$cn = get_class($this);
		return new $cn($this->iterator->current()->getPathname(), true, $this->pattern);
	}
}