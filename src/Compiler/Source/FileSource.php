<?php
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