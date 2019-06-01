<?php


namespace Doyo\Behat;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

class ExpressionLanguage extends BaseExpressionLanguage
{
    public function __construct()
    {
        $directory = sys_get_temp_dir().'/doyo';
        $cache = new FilesystemAdapter('behat.expression',0, $directory);
        parent::__construct($cache);
    }

    public function compile($content, $names = [])
    {
        $pattern = '/(trans|route)\(.*\)/';

        $callback = function($match) use($names){
            return $this->doCompile($match[0], $names);
        };
        $content = preg_replace_callback($pattern,$callback,$content);
        return $content;
    }

    private function doCompile($expression, $names)
    {
        $expression = new SerializedParsedExpression(
            $expression,
            \serialize($this->parse($expression,$names)->getNodes())
        );

        return parent::evaluate($expression, $names);
    }
}
