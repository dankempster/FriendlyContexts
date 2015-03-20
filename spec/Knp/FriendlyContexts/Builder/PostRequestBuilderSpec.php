<?php

namespace spec\Knp\FriendlyContexts\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\PostFile;

class PostRequestBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\FriendlyContexts\Builder\PostRequestBuilder');
    }

    function it_is_request_builder()
    {
        $this->shouldHaveType('Knp\FriendlyContexts\Builder\RequestBuilderInterface');
    }

    function it_build_a_post_request(ClientInterface $client, RequestInterface $request)
    {
        $client->post(
            '/resource?foo=bar',
            ['some headers'],
            ['data' => 'plop'],
            ['some options']
        )->shouldBeCalled(1)->willReturn($request);

        $this->setClient($client);

        $this->build(
            '/resource',
            ['foo' => 'bar'],
            ['some headers'],
            ['data' => 'plop'],
            null,
            ['some options']
        )->shouldReturn($request);
    }

    function it_build_a_post_request_with_file_supports(ClientInterface $client, RequestInterface $request)
    {
        $client->post(
            '/resource?foo=bar',
            ['some headers'],
            Argument::that(function ($data) {
                if (!isset($data['data'])) {
                    return false;
                }

                $data = $data['data'];

                if (!$data instanceof PostFile) {
                    return false;
                }

                return 'data' === $data->getFieldName() &&
                    __FILE__ === $data->getFilename()
                ;
            }),
            ['some options']
        )->shouldBeCalled(1)->willReturn($request);

        $this->setClient($client);

        $this->build(
            '/resource',
            ['foo' => 'bar'],
            ['some headers'],
            ['data' => 'file://'.__FILE__],
            null,
            ['some options']
        )->shouldReturn($request);
    }
}
