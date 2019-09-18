<?php

namespace Snap\Debug\Dumper;

use Snap\Services\Config;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;

/**
 * Replaces the standard calls to VarCloner::dump.
 */
class Handle
{
    /**
     * The css for the dumper color schemes.
     *
     * @var array
     */
    static private $schemes = [
        'snap' => [
            'default' => 'background:#fafafa; color:#323232; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:normal; color:#905',
            'const' => 'font-weight:normal; color:#0086b3',
            'str' => 'font-weight:normal; color:#690',
            'note' => 'color:#708090',
            'ref' => 'color:#A0A0A0',
            'public' => 'color:#DD4A68',
            'protected' => 'color:#DD4A68',
            'private' => 'color:#DD4A68',
            'meta' => 'color:#DD4A68',
            'key' => 'color:#a71d5d',
            'index' => 'color:#905',
            'ellipsis' => 'color:#690',
            'ns' => 'user-select:none;',
            'lineinfo' => 'color:#999; display: block; margin-bottom: 5px;',
        ],
        'symfony' => [
            'default' => 'background-color:#18171B; color:#FF8400; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
            'num' => 'font-weight:bold; color:#1299DA',
            'const' => 'font-weight:bold',
            'str' => 'font-weight:bold; color:#56DB3A',
            'note' => 'color:#1299DA',
            'ref' => 'color:#A0A0A0',
            'public' => 'color:#FFFFFF',
            'protected' => 'color:#FFFFFF',
            'private' => 'color:#FFFFFF',
            'meta' => 'color:#B729D9',
            'key' => 'color:#56DB3A',
            'index' => 'color:#1299DA',
            'ellipsis' => 'color:#FF8400',
        ],
        'laravel' => [
            'default' => 'background-color:#fff; color:#222; line-height:1.2em; font-weight:normal; font:12px Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:100000',
            'num' => 'color:#a71d5d',
            'const' => 'color:#795da3',
            'str' => 'color:#df5000',
            'cchr' => 'color:#222',
            'note' => 'color:#a71d5d',
            'ref' => 'color:#a0a0a0',
            'public' => 'color:#795da3',
            'protected' => 'color:#795da3',
            'private' => 'color:#795da3',
            'meta' => 'color:#b729d9',
            'key' => 'color:#df5000',
            'index' => 'color:#a71d5d',
            'lineinfo' => 'color:#999; display: block; margin-bottom: 5px;',
        ],
    ];
    
    /**
     * The dump replacement.
     *
     * @param  mixed $var The var to dump.
     */
    public static function dump($var)
    {
        $flags = 0;

        if (Config::get('debug.dump_show_string_length')) {
            $flags = $flags | AbstractDumper::DUMP_STRING_LENGTH;
        }

        $cloner = new VarCloner();

        if (! \in_array(\PHP_SAPI, array('cli', 'phpdbg'), true)) {
            $dumper = new HtmlDumper(null, null, $flags);

            $dumper->setStyles(self::getStyles());

            if (Config::get('debug.dump_include_trace')) {
                $dumper->setDumpBoundaries(
                    '<pre class=sf-dump id=%s data-indent-pad="%s">' . self::additionalOutput(),
                    '</pre><script>Sfdump(%s)</script>'
                );
            }
        } else {
            $dumper = new CliDumper(null, null, $flags);
        }

        $dumper->dump($cloner->cloneVar($var));
    }

    /**
     * Ensures the dump output uses the correct styles for Snap.
     *
     * Can be overwritten by setting the config key.
     *
     * @return array
     */
    private static function getStyles()
    {
        $style = Config::get('debug.dump_set_style', 'snap');

        if (\is_array($style)) {
            return $style;
        }

        if (\array_key_exists($style, self::$schemes)) {
            return self::$schemes[ $style ];
        }

        return self::$schemes['snap'];
    }

    /**
     * Gets the line and file of where the dump was called.
     *
     * @return string The HTML for showing the line info.
     */
    private static function additionalOutput()
    {
        $backtrace = \debug_backtrace()[3];

        $root = \realpath(__DIR__ . '/../../../../../../');

        return '<span class="sf-dump-lineinfo">' . \ltrim(\str_replace($root, '', $backtrace['file']), '/\\') . ':' . $backtrace['line'] . '</span>';
    }
}
