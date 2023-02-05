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

namespace TASoft\Config\Compiler\Target;


use InvalidArgumentException;
use TASoft\Config\Config;

class FileTarget extends AbstractFileTarget
{
    const MODE_SERIALIZED = 'serialized';
    const MODE_BASE_64 = 'base64';
    const MODE_PHP = 'php';

    private $mode;

    /**
     * FileTarget constructor.
     * @param $filename
     * @param string $mode
     */
    public function __construct($filename, $mode = self::MODE_PHP)
    {
        parent::__construct($filename);
        if(is_callable([$this, "do{$mode}"]))
            $this->mode = $mode;
        else
            throw  new InvalidArgumentException("Mode $mode is not supported");
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @inheritDoc
     */
    protected function serialize(Config $config, array $importedFiles): string
    {
        $mthd = "do" . $this->getMode();
        return call_user_func([$this, $mthd], $config, $importedFiles) ?: "";
    }

    /**
     * Creates a simple doc comment with the imported files
     *
     * @param array $importedFiles
     * @return string
     */
    protected function createDocComment(array $importedFiles) {
        $target = get_class($this);
        $fn = $this->getFilename();

        if($importedFiles) {
            $files = "";
            foreach($importedFiles as $idx => $file)
                $files .= " *	".($idx+1) .".	$file\n";


            return <<< EOT
/**
 *  == TASoft Config Compiler ==
 *  Target: $target ($fn)
 *  Compiled from:
$files */

EOT;
        } else {
            return <<< EOT
/**
 *  == TASoft Config Compiler ==
 *  Target: $target ($fn)
 */

EOT;
        }
    }

    /**
     * Simple internal php serialize function used
     *
     * @param Config $config
     * @param array $importedFiles
     * @return string
     */
    protected function doSerialized(Config $config, array $importedFiles) {
        $comment = $this->createDocComment($importedFiles);
        $data = serialize( $config->toArray() );

        return <<< EOT
<?php
$comment
return unserialize( $data );
EOT;
    }

    /**
     * Simple internal php serialize function used
     *
     * @param Config $config
     * @param array $importedFiles
     * @return string
     */
    protected function doBase64(Config $config, array $importedFiles) {
        $comment = $this->createDocComment($importedFiles);
        $data = var_export(base64_encode( serialize( $config->toArray() ) ), true);

        return <<< EOT
<?php
$comment
return unserialize( base64_decode ( $data ) );
EOT;
    }

    /**
     * Simple internal php serialize function used
     *
     * @param Config $config
     * @param array $importedFiles
     * @return string
     */
    protected function doPHP(Config $config, array $importedFiles) {
        $comment = $this->createDocComment($importedFiles);
        $data = var_export($config->toArray(), true);

        return <<< EOT
<?php
$comment
return $data;
EOT;
    }
}