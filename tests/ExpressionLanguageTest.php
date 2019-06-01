<?php

namespace Test\Doyo\Behat;

use Doyo\Behat\ExpressionLanguage;
use Doyo\Behat\ExpressionLanguageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpressionLanguageTest extends TestCase
{
    public function testCompile()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->any())
            ->method('trans')
            ->willReturn('translated');

        $subject = $this->getSubject($translator);
        $expr = <<<EOC
{
    "foo": "trans("foo")"
}
EOC;

        $compiled = $subject->compile($expr);

        $this->assertStringContainsString('"translated"', $compiled);
    }

    public function getSubject(
        TranslatorInterface $translator = null,
        RouterInterface $router = null
    )
    {
        $provider = new ExpressionLanguageProvider();
        if(!is_null($translator)){
            $provider->setTranslator($translator);
        }
        if(!is_null($router)){
            $provider->setRouter($router);
        }
        $expression = new ExpressionLanguage();
        $expression->registerProvider($provider);

        return $expression;
    }
}
