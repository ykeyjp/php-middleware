<?php
namespace ykey\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Stream
 *
 * @package ykey\middleware
 */
class Stream
{
    /**
     * @param array $stacks
     *
     * @return Stream
     */
    public static function create(array $stacks = []): self
    {
        return new self($stacks);
    }

    /**
     * @var callable[]
     */
    private $stacks;

    /**
     * Stream constructor.
     *
     * @param callable[] $stacks
     */
    private function __construct(array $stacks = [])
    {
        $this->stacks = $stacks;
    }

    /**
     * @param callable $middleware
     *
     * @return Stream
     */
    public function pipe(callable $middleware): self
    {
        $stacks = $this->stacks;
        $stacks[] = $middleware;

        return new self($stacks);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $next = null;
        foreach (array_reverse($this->stacks) as $middleware) {
            $next = new Queue($middleware, $next);
        }
        if ($next instanceof Queue) {
            return $next($request, $response);
        }

        return $response;
    }
}
