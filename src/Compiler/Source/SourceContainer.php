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

use ReturnTypeWillChange;

class SourceContainer implements SourceInterface {
	private $members=[];
	private $pos;
	
	public function addSource(SourceInterface $source) {
		if(!in_array($source, $this->members))
			$this->members[] = $source;
	}
	
	#[ReturnTypeWillChange] public function rewind() {
		$this->pos=0;
	}
	
	#[ReturnTypeWillChange] public function next() {
		$this->pos++;
	}
	
	#[ReturnTypeWillChange] public function valid() {
		return isset($this->members[$this->pos]);
	}
	
	#[ReturnTypeWillChange] public function current() {
		return NULL;
	}
	
	#[ReturnTypeWillChange] public function key() {
		return NULL;
	}
	
	#[ReturnTypeWillChange] public function hasChildren() { return $this->valid(); }
	#[ReturnTypeWillChange] public function getChildren() { return $this->members[$this->pos]; }
}