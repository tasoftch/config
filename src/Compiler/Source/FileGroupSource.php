<?php
namespace TASoft\Config\Compiler\Source;

class FileGroupSource implements SourceInterface {
	private $members = [];
	private $pos;
	
	
	public function addSourceFile(string $filename) {
		$fn = realpath($filename);
		if($fn) {
			if(!in_array($fn, $this->members))
				$this->members[] = $fn;
		} else {
			trigger_error("Could not locate file '$filename'", E_USER_NOTICE);
		}
	}
	
	public function rewind() {
		$this->pos = 0;
	}
	
	public function next() {
		$this->pos++;
	}
	
	public function valid() {
		return isset($this->members[$this->pos]);
	}
	
	public function current() {
		return $this->members[$this->pos];
	}
	
	public function key() {
		return $this->pos;
	}
	
	public function hasChildren() { return false; }
	public function getChildren() { return NULL; }
}