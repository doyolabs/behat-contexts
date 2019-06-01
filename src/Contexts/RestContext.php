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
use Behatch\Context\RestContext as BaseRestContext;
use Doyo\Behat\ExpressionAwareContextInterface;
use Doyo\Behat\ExpressionLanguage;

class RestContext extends BaseRestContext implements ExpressionAwareContextInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expression;

    public function setExpressionLanguage(ExpressionLanguage $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @Given I send a JSON :method request to :url
     * @Given I send a JSON :method request to :url with :body
     *
     * @param string $method
     * @param string $url
     * @param array  $files
     */
    public function iSendJsonRequestTo($method, $url, PyStringNode $body = null, $files = [])
    {
        $url = $this->expression->compile($url);
        $this->iAddHeaderEqualTo('Content-Type', 'application/json');
        $this->iAddHeaderEqualTo('Accept', 'application/json');
        $this->iSendARequestTo($method, $url, $body, $files);
    }

    /**
     * @Given I send a JSON :method request to :url with body:
     *
     * @param string $method
     * @param string $url
     */
    public function iSendJsonRequestToWithBody($method, $url, PyStringNode $body)
    {
        $this->iSendJsonRequestTo($method, $url, $body);
    }
}
