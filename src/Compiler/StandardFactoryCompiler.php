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

namespace TASoft\Config\Compiler;


use InvalidArgumentException;
use TASoft\Config\Compiler\Target\FileTarget;
use TASoft\Config\Compiler\Target\TargetInterface;
use Traversable;

class StandardFactoryCompiler extends AbstractFactoryCompiler
{
    /** @var TargetInterface|string */
    private $target;
    /** @var Traversable */
    private $source;

    public function __construct($target = NULL) {
        $this->setTarget($target);
    }

    /**
     * @inheritDoc
     */
    public function getCompilerTarget(): TargetInterface
    {
        return $this->getTarget();
    }


    /**
     * @inheritDoc
     */
    public function getCompilerSource(): Traversable {
        return $this->getSource();
    }

    /**
     * @return string|TargetInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string|TargetInterface $target
     */
    public function setTarget($target): void
    {
        if(is_string($target)) {
            $target = new FileTarget($target);
        }

        if($target instanceof TargetInterface)
            $this->target = $target;
        else
            throw new InvalidArgumentException("Invalid target");
    }

    /**
     * @return Traversable
     */
    public function getSource(): Traversable
    {
        return $this->source;
    }

    /**
     * @param Traversable $source
     */
    public function setSource(Traversable $source): void
    {
        $this->source = $source;
    }
}