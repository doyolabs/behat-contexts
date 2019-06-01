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

namespace Test\Doyo\Behat\Fixtures;

use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class FeatureContext implements Context
{
    private $output;

    /**
     * @Given I say :what
     *
     * @param string $what
     */
    public function iSay($what)
    {
        $this->output = TestedClass::{$what}();
    }

    /**
     * @Given output should be :what
     *
     * @param string $what
     */
    public function outputShouldBe($what)
    {
        Assert::same($this->output, $what);
    }
}
