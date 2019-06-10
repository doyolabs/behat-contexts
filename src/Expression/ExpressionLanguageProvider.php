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

namespace Doyo\Behat\Expression;

use Doyo\Behat\Bridge\Symfony\Translation\TranslatorInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Routing\RouterInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'trans',
                function ($id, $params = null, $domain = null, $locale = null) {
                    $this->normalizeValue($id);
                    $this->normalizeValue($params);
                    $this->normalizeValue($domain);
                    $this->normalizeValue($locale);

                    return $this->trans($id, $params, $domain, $locale);
                },
                function ($args, $id, $params = [], $domain = null, $locale = null) {
                    return $this->trans($id, $params, $domain, $locale);
                }
            ),
            new ExpressionFunction(
                'route',
                function ($name, $params=[], $referenceType=1) {
                    $this->normalizeValue($name);
                    $this->normalizeValue($params);
                    $this->normalizeValue($referenceType);

                    return $this->route($name, $params, $referenceType);
                },
                function ($args, $name, $params=[], $referenceType=1) {
                    return $this->route($name, $params, $referenceType);
                }
            ),
        ];
    }

    public function trans($id, $params = [], $domains = null, $locale = null)
    {
        if (null === $this->translator) {
            return $id;
        }

        return $this->translator->trans($id, $params, $domains, $locale);
    }

    public function route($name, $params = [], $referenceType=1)
    {
        if (null === $this->router) {
            return $name;
        }

        return $this->router->generate($name, $params, $referenceType);
    }

    private function normalizeValue(&$value)
    {
        if (null !== $value) {
            eval('$value = '.$value.';');
        }

        return $value;
    }
}
