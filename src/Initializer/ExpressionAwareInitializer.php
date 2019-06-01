<?php

namespace Doyo\Behat\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Doyo\Behat\ExpressionAwareContextInterface;
use Doyo\Behat\ExpressionLanguage;

class ExpressionAwareInitializer implements ContextInitializer
{
    /**
     * @var ExpressionLanguage
     */
    private $expression;

    public function __construct(
        ExpressionLanguage $expression
    )
    {
        $this->expression = $expression;
    }

    public function initializeContext(Context $context)
    {
        if($context instanceof ExpressionAwareContextInterface){
            $context->setExpressionLanguage($this->expression);
        }
    }
}
