<?php

namespace spec\Doyo\Behat\Contexts;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Mink;
use Doyo\Behat\Contexts\RestContext;
use Doyo\Behat\ExpressionAwareContextInterface;
use Doyo\Behat\ExpressionLanguage;
use Test\Doyo\Behat\Fixtures\TestRequest as Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RestContextSpec extends ObjectBehavior
{
    function let(
        Request $request,
        ExpressionLanguage $expressionLanguage,
        Mink $mink
    )
    {

        $mink->getDefaultSessionName()->willReturn('symfony2');
        $request->beConstructedWith([$mink->getWrappedObject()]);

        $this->beConstructedWith($request);
        $this->setExpressionLanguage($expressionLanguage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RestContext::class);
    }

    function it_should_be_an_expression_aware_context()
    {
        $this->shouldImplement(ExpressionAwareContextInterface::class);
    }

    function it_should_send_json_request(
        ExpressionLanguage $expressionLanguage,
        Request $request
    )
    {
        $expressionLanguage->compile('foo')
            ->shouldBeCalled()
            ->willReturn('generated-foo');

        $this->expectJsonHeader($request);
        $request->send("GET","/generated-foo",Argument::cetera())
            ->shouldBeCalled();

        $this->iSendJsonRequestTo('GET', 'foo');
    }

    function it_should_send_json_request_with_body(
        PyStringNode $node,
        Request $request
    )
    {
        $node->getRaw()->willReturn('body content');

        $this->expectJsonHeader($request);
        $request->send('GET','/',[],[],'body content')
            ->shouldBeCalled();
        $this->iSendJsonRequestToWithBody('GET','foo',$node);
    }

    private function expectJsonHeader(Request $request)
    {
        $request->setHttpHeader('Content-Type','application/json')
            ->shouldBeCalled();
        $request->setHttpHeader('Accept','application/json')
            ->shouldBeCalled();
    }
}
