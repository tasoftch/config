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

namespace TASoft\Config\Transformer;


/**
 * A regex transformer that captures regular expressions and will replace the fetched configuration values if a string value of it matches.
 * @package TASoft\Config\Transformer
 */
class Regex implements TransformerInterface
{
    private $regex;

    /**
     * Transforms the value if it is a string or an object that can be casted into a string.
     *
     * @param mixed|object|string $value
     * @return mixed|string
     */
    public function getTransformedValue($value)
    {
        if(is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            foreach($this->regex as $regex => $replace) {

                if(preg_match($regex, (string)$value))
                    return preg_replace($regex, $replace, $value);
            }
        }

       return $value;
    }

    /**
     * Adds a regex pattern with a given replacement string.
     *
     * @param string $regex
     * @param string $replacement
     */
    public function addRegularExpression($regex, $replacement) {
        $this->regex[$regex] = $replacement;
    }
}