<?php

namespace Knp\FriendlyContexts\Builder;

use Guzzle\Http\Message\PostFile;

class PostRequestBuilder extends AbstractRequestBuilder
{
    public function build($uri = null, array $queries = null, array $headers = null, array $postBody = null, $body = null, array $options = [])
    {
        parent::build($uri, $queries, $headers, $postBody, $body, $options);

        $resource = $queries ? sprintf('%s?%s', $uri, $this->formatQueryString($queries)) : $uri;
        $postBody = $postBody ?: $body;

        foreach ($postBody as $name => &$pattern) {
            $pattern = $this->convertFile($name, $pattern);
        }

        return $this->getClient()->post($resource, $headers, $postBody, $options);
    }

    private function convertFile($name, $pattern)
    {
        if (0 === strpos($pattern, 'file://')) {
            $path = substr($pattern, 7);

            return new PostFile($name, $path);
        }

        return $pattern;
    }
}
