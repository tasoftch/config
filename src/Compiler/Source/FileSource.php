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

class FileSource implements SourceInterface {
	private $path;
	private $pos = 0;
	
	public function __construct($filePath) {
		$this->path = realpath($filePath);
		if(!is_file($this->path))
			throw new \TASoft\Config\Compiler\BadSourceException("File not found: $filePath", 190);
	}
	
	public function rewind() {
		$this->pos = 0;
	}
	
	public function next() {
		$this->pos++;
	}
	
	public function valid() {
		return $this->pos < 1;
	}
	
	public function current() {
		if($this->pos == 0)
			return $this->path;
		return NULL;
	}
	
	public function key() {
		return 'path';
	}
	
	public function hasChildren() { return false; }
	public function getChildren() { return NULL; }
}