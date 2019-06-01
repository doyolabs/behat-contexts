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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route(
     *     path="/",
     *     name="homepage"
     * )
     */
    public function index(Request $request)
    {
        $defaults = [
            'foo'   => 'Bar',
            'hello' => 'World',
        ];

        $content = $request->getContent();
        $content = json_decode($content, true);
        if (null === $content) {
            $content = [];
        }
        $data = array_merge_recursive($defaults, $content);

        return new JsonResponse($data, 200);
    }
}
