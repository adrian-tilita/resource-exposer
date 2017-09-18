<?php
namespace AdrianTilita\ResourceExposer\Middleware;

use AdrianTilita\ResourceExposer\Bridge\ConfigBridge;
use AdrianTilita\ResourceExposer\Provider\ApplicationServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

class RouteMiddlewareTest extends TestCase
{
    /**
     * Test that we receive an unauthorized message
     */
    public function testNoAuthorization()
    {
        $request = Request::createFromGlobals();

        $routeMiddleware = new RouteMiddleware();

        /** @var Response $response */
        $response = $routeMiddleware->handle($request, function () {
            return true;
        });

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        // dummy test for message
        $this->assertEquals(json_encode(['error' => 'Authentication failed!']), $response->getContent());
    }

    public function testAuthorizationSuccess()
    {
        // build mocks
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getUser')
            ->willReturn('foo');
        $requestMock->expects($this->once())
            ->method('getPassword')
            ->willReturn('bar');

        $parametersBag = new ParameterBag(['authorization' => true]);
        $requestMock->headers = $parametersBag;

        Config::shouldReceive('has')
            ->once()
            ->with(ApplicationServiceProvider::APPLICATION_IDENTIFIER)
            ->andReturn(true);
        Config::shouldReceive('get')
            ->once()
            ->with(ApplicationServiceProvider::APPLICATION_IDENTIFIER)
            ->andReturn([
                'authorization' => [
                    'username' => 'foo',
                    'password' => 'bar'
                ]
            ]);

        $routeMiddleware = new RouteMiddleware();
        /** @var Response $response */
        $response = $routeMiddleware->handle($requestMock, function () {
            return 'foo';
        });

        // dummy test for message
        $this->assertEquals('foo', $response);
    }
}
