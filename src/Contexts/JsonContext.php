<?php

/*
 * This file is part of the DoyoLabs Behat Common project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Doyo\Behat\Contexts;

use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\JsonContext as BaseJsonContext;
use Behatch\Json\Json;
use Doyo\Behat\Expression\ExpressionAwareContextInterface;
use Doyo\Behat\Expression\ExpressionLanguage;
use PHPUnit\Framework\Assert;

final class JsonContext extends BaseJsonContext implements ExpressionAwareContextInterface
{
    /**
     * @var \Doyo\Behat\Expression\ExpressionLanguage
     */
    private $expression;

    public function setExpressionLanguage(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @Then /^the JSON should be deep equal to:$/
     */
    public function theJsonShouldBeDeepEqualTo(PyStringNode $content)
    {
        $actual = $this->getJson();
        try {
            $expected = new Json($content);
        } catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        $actual   = new Json(json_encode($this->sortArrays($actual->getContent())));
        $expected = new Json(json_encode($this->sortArrays($expected->getContent())));

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            "The json is equal to:\n".$actual->encode()
        );
    }

    /**
     * @Then /^the JSON should be a superset of:$/
     */
    public function theJsonIsASupersetOf(PyStringNode $content)
    {
        $translated = $this->expression->compile($content->getRaw());
        $translated = new Json($translated);
        $translated = $this->sortArrays($translated);
        $actual     = $this->sortArrays($this->getJson());

        Assert::assertArraySubset((array) $translated, (array) $actual);
    }

    private function sortArrays($obj)
    {
        $isObject = \is_object($obj);

        foreach ($obj as $key => $value) {
            if (null === $value || is_scalar($value)) {
                continue;
            }

            if (\is_array($value)) {
                sort($value);
            }

            $value = $this->sortArrays($value);

            $isObject ? $obj->{$key} = $value : $obj[$key] = $value;
        }

        return $obj;
    }
}
