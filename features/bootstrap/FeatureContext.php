<?php

use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;
use Doyo\Behat\ExpressionAwareContextInterface;
use Doyo\Behat\ExpressionLanguage;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, ExpressionAwareContextInterface
{
    private $output;

    private $route;

    /**
     * @var ExpressionLanguage
     */
    private $expression;

    public function setExpressionLanguage(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @Given I say :what
     * @param string $what
     */
    public function iSay($what)
    {
        $this->output = TestedClass::{$what}();
    }

    /**
     * @Given output should be :what
     * @param string $what
     */
    public function outputShouldBe($what)
    {
        Assert::same($this->output, $what);
    }

    /**
     * @Given I have text:
     * @param \Behat\Gherkin\Node\PyStringNode $node
     */
    public function iHaveText(\Behat\Gherkin\Node\PyStringNode $node)
    {
        $this->output = $node->getRaw();
    }

    /**
     * @Then translated output should be:
     * @param \Behat\Gherkin\Node\PyStringNode $node
     */
    public function translatedOutputShouldBe(\Behat\Gherkin\Node\PyStringNode $node)
    {
        $translated = $this->expression->compile($this->output);
        Assert::same($translated, $node->getRaw());
    }

    /**
     * @Given I have route :route
     * @param string $route
     */
    public function iHaveRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @Then generated route should be :exprected
     * @param string $expected
     */
    public function translatedRouteShouldBe($expected)
    {
        $generated = $this->expression->compile($this->route);
        Assert::same($generated, $expected);
    }
}
