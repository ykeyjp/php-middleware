<?php
namespace ykey\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Queue
 *
 * @package ykey\middleware
 */
class Queue
{
    /**
     * @var callable
     */
    private $middleware;
    /**
     * @var callable|null
     */
    private $next;

    /**
     * Queue constructor.
     *
     * @param callable      $middleware
     * @param callable|null $next
     */
    public function __construct(callable $middleware, ?callable $next)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $newResponse = call_user_func_array($this->middleware, [$request, $response, $this->next]);
        if ($newResponse instanceof ResponseInterface) {
            return $newResponse;
        }

        return $response;
    }
}
