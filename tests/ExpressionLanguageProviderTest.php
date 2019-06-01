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

namespace Test\Doyo\Behat;

use Doyo\Behat\ExpressionLanguage;
use Doyo\Behat\ExpressionLanguageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpressionLanguageProviderTest extends TestCase
{
    public function testTransFunction()
    {
        $trans = $this->createMock(TranslatorInterface::class);

        $trans->expects($this->exactly(2))
            ->method('trans')
            ->with('foo', ['some' => 'params'], 'validators')
            ->willReturn('translated-foo');

        $expr = new Expression('trans("foo",{"some":"params"},"validators")');

        $expression = $this->getExpression();
        $this->assertEquals('foo', $expression->evaluate($expr));

        $expression = $this->getExpression($trans);
        $this->assertEquals('translated-foo', $expression->evaluate($expr));
        $this->assertEquals('translated-foo', $expression->compile($expr));
    }

    public function testRouteFunction()
    {
        $router = $this->createMock(RouterInterface::class);

        $router->expects($this->exactly(2))
            ->method('generate')
            ->with('route', ['some' => 'params'], 1)
            ->willReturn('generated-route');

        $expr = new Expression('route("route", {"some":"params"},1)');

        $expression = $this->getExpression();
        $this->assertEquals('route', $expression->evaluate($expr));

        $expression = $this->getExpression(null, $router);
        $this->assertEquals('generated-route', $expression->evaluate($expr));
        $this->assertEquals('generated-route', $expression->compile($expr));
    }

    /**
     * @param mixed|null $translator
     * @param mixed|null $router
     *
     * @return ExpressionLanguage
     */
    private function getExpression(
        $translator = null,
        $router = null
    ) {
        $provider = new ExpressionLanguageProvider();

        if (null !== $translator) {
            $provider->setTranslator($translator);
        }
        if (null !== $router) {
            $provider->setRouter($router);
        }

        $expression = new ExpressionLanguage();
        $expression->registerProvider($provider);

        return $expression;
    }
}
