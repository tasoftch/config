<?php
namespace TASoft\Config\Compiler;

class StandardCompiler extends AbstractCompiler {
	private $target;
	private $source;
	
	public function __construct($target = NULL) {
		if(is_string($target))
			$this->setTarget($target);
	}
	
	public function setTarget(string $target) {
		$this->target = $target;
	}
	public function getTarget(): ? string {
		return $this->target;
	}
	
	public function setSource(\Traversable $source) {
		$this->source = $source;
	}
	public function getSource(): ? \Traversable {
		return $this->source;
	}
	
	
	public function getCompiledTargetFilename(): string {
		return $this->getTarget();
	}
	public function getCompilerSource(): \Traversable {
		return $this->getSource();
	}
}