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

namespace TASoft\Config\Controller;


use TASoft\Config\Config;
use TASoft\Config\Transformer\TransformerInterface;

/**
 * The transformation controller accepts instances of TransformerInterface to transform fetched values into other values
 * @package TASoft\Config\Controller
 */
class TransformationController extends ConfigController
{
    /**
     * @var array
     */
    private $transformers = [];

    /**
     * Adds a transformer active for a passed key and prefix.
     * If you don't specify a key (and/or a prefix), it is active for all fetches.
     *
     * @param TransformerInterface $transformer
     * @param string $key
     * @param string|NULL $prefix
     */
    public function addTransformer(TransformerInterface $transformer, string $key = "", string $prefix = NULL) {
        if(!in_array($transformer, $this->transformers)) {
            $id = $prefix ? "$prefix.$key" : $key;
            $this->transformers[$id][] = $transformer;
        }
    }

    /**
     * Removes all registered transformers
     */
    public function removeAllTransformers() {
        $this->transformers = [];
    }

    /**
     * Removes registered transformers identified by key and prefix
     * @param string $key
     * @param string|NULL $prefix
     */
    public function removeTransformers(string $key, string $prefix = NULL) {
        $id = $prefix ? "$prefix.$key" : $key;
        if(isset($this->transformers[$id]))
            unset($this->transformers[$id]);
    }

    /**
     * @inheritdoc
     */
    public function getTransformedValue(Config $config, $key, $value)
    {
        $prefix = $config->getPrefix();
        $id = $prefix ? "$prefix.$key" : $key;


        if(isset($this->transformers[$id])) {
            /** @var TransformerInterface $trans */
            foreach($this->transformers[$id] as $trans) {
                $value = $trans->getTransformedValue($value);
            }
        }

        if($id != "" && isset($this->transformers[""])) {
            /** @var TransformerInterface $trans */

            foreach($this->transformers[""] as $trans) {
                $value = $trans->getTransformedValue($value);
            }
        }

        return $value;
    }
}