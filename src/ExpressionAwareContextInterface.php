<?php


namespace Doyo\Behat;


interface ExpressionAwareContextInterface
{
    /**
     * @param ExpressionLanguage $expression
     */
    public function setExpressionLanguage(ExpressionLanguage $expression);
}
