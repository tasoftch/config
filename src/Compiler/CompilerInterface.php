<?php
namespace TASoft\Config\Compiler;

interface CompilerInterface {
	public function compile(): bool;
	
	public function getCompiledTargetFilename(): string;
	
	public function getCompilerSource(): \Traversable;
}