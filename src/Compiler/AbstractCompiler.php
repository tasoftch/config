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

use TASoft\Config\Config;

abstract class AbstractCompiler implements CompilerInterface {
	protected $saveMode = 'php';
	
	public function setSaveMode($mode = 'php') {
		$this->saveMode = $mode;
	}
	public function getSaveMode() { return $this->saveMode; }
	
	private function _collectFilePaths(array &$outPaths, \Traversable $traversable) {
		foreach($traversable as $source) {
			if($source)
				$outPaths[] = $source;
			
			if($traversable instanceof \RecursiveIterator && $traversable->hasChildren()) {
				$this->_collectFilePaths($outPaths, $traversable->getChildren());
			}
		}
	}
	
	
	protected function packConfigArray(array $config): string {
		switch($this->saveMode) {
			case 'base64':
				$data = base64_encode( serialize($config) );
				//$data = str_replace('\"', '\\"', $data);
				//$data = str_replace('"', '\"', $data);
				return "unserialize( base64_decode ( \"$data\" ) )";
			default:
				return var_export($config, true);
		}
	}
	
	
	public function compile(): bool {
		$traversable = $this->getCompilerSource();
		
		$allPaths = [];
		$this->_collectFilePaths($allPaths, $traversable);
		
		$target = $this->getCompiledTargetFilename();
		$tgRef = realpath($target);
		
		$config = new Config([]);
		
		$files = "";
		
		$allPaths = array_unique($allPaths);
		
		foreach($allPaths as $idx => $path) {
			if($path == $tgRef)
				continue;
			$files .= " *	".($idx+1) .".	$path\n";
			
			$data = @include($path);
			if(is_array($data) && count($data)) {
				$cfg = new Config($data);
				$config->merge($cfg);
			}
		}
		
		$array = $config->toArray();
		$data = $this->packConfigArray($array);
		file_put_contents($target, "<?php
/*
 *	== TASoft Config Compiler == 
 * 	Compiled from:
$files */
return $data;
?>");
		
		return true;
	}
	
	abstract public function getCompiledTargetFilename(): string;
	abstract public function getCompilerSource(): \Traversable;
}