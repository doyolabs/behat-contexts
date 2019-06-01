<?php


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
            'foo' => 'Bar',
            'hello' => 'World'
        ];

        $content = $request->getContent();
        $content = json_decode($content, true);
        if(is_null($content)){
            $content = [];
        }
        $data = array_merge_recursive($defaults, $content);
        return new JsonResponse($data,200);
    }
}
