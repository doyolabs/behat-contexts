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

use Doyo\Behat\Expression\ExpressionLanguage;
use Doyo\Behat\Expression\ExpressionLanguageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Doyo\Behat\Bridge\Symfony\Translation\TranslatorInterface;

class ExpressionLanguageTest extends TestCase
{
    public function testCompile()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->any())
            ->method('trans')
            ->willReturn('translated');

        $subject = $this->getSubject($translator);
        $expr    = <<<EOC
{
    "foo": "trans("foo")"
}
EOC;

        $compiled = $subject->compile($expr);

        $this->assertContains('"translated"', $compiled);
    }

    public function getSubject(
        TranslatorInterface $translator = null,
        RouterInterface $router = null
    ) {
        $provider = new \Doyo\Behat\Expression\ExpressionLanguageProvider();
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
