<?php
namespace ykey\middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class StreamTest extends TestCase
{
    public function testStream()
    {
        $stream = Stream::create()
            ->pipe(function (ServerRequestInterface $req, ResponseInterface $res, ?callable $next) {
                $res->getBody()->write('A');
                if ($next) {
                    $res = $next($req, $res);
                }
                $res->getBody()->write('1');

                return $res;
            })
            ->pipe(function (ServerRequestInterface $req, ResponseInterface $res, ?callable $next) {
                $res->getBody()->write('B');
                if ($next) {
                    $res = $next($req, $res);
                }
                $res->getBody()->write('2');

                return $res;
            })
            ->pipe(function (ServerRequestInterface $req, ResponseInterface $res, ?callable $next) {
                $res->getBody()->write('C');
                if ($next) {
                    $res = $next($req, $res);
                }
                $res->getBody()->write('3');

                return $res;
            });
        $res = $stream(new ServerRequest, new Response);
        $res->getBody()->rewind();
        $this->assertEquals('ABC321', $res->getBody()->getContents());
    }
}
