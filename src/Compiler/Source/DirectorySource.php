<?php
namespace TASoft\Config\Compiler\Source;

class DirectorySource implements SourceInterface {
	private $pattern;
	private $recursive;
	
	private $iterator;
	
	public function __construct($source, $recursive = false, $globPattern = '*.config.php') {
		$this->pattern = $globPattern;
		$this->recursive = $recursive;
		
		$this->iterator = new \DirectoryIterator($source);
	}
	
	
	protected function filenameMatchesPattern($filename) {
		if(fnmatch($this->pattern, $filename))
			return true;
		return false;
	}
	
	
	public function rewind() {
		$this->iterator->rewind();
	}
	
	public function next() {
		$this->iterator->next();
	}
	
	public function valid() {
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
	
	public function current() {
		$pn = realpath($this->iterator->current()->getPathname());
		return is_file($pn) ? $pn : NULL;
	}
	
	public function key() {
		return $this->iterator->key();
	}
	
	public function hasChildren() { return $this->recursive && $this->iterator->isDir(); }
	public function getChildren() {
		$cn = get_class($this);
		return new $cn($this->iterator->current()->getPathname(), true, $this->pattern);
	}
}