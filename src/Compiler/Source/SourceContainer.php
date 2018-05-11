<?php
namespace TASoft\Config\Compiler\Source;

class SourceContainer implements SourceInterface {
	private $members=[];
	private $pos;
	
	public function addSource(SourceInterface $source) {
		if(!in_array($source, $this->members))
			$this->members[] = $source;
	}
	
	public function rewind() {
		$this->pos=0;
	}
	
	public function next() {
		$this->pos++;
	}
	
	public function valid() {
		return isset($this->members[$this->pos]);
	}
	
	public function current() {
		return NULL;
	}
	
	public function key() {
		return NULL;
	}
	
	public function hasChildren() { return $this->valid(); }
	public function getChildren() { return $this->members[$this->pos]; }
}