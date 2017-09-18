<?php
namespace AdrianTilita\ResourceExposer\Service;

use AdrianTilita\ResourceExposer\Provider\ApplicationServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\TestCase;

class RequestHandlerTest extends TestCase
{
    /**
     * Reset mockery instances
     */
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * Test list resources
     */
    public function testHandleList()
    {
        // build mocks
        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([
                'foo' => null,
                'bar' => null
            ]));

        URL::shouldReceive('to')
            ->once()
            ->with('/')
            ->andReturn('http://foo.bar');

        $requestHandler = new RequestHandler($modelListService);
        $list = $requestHandler->listResources();

        $this->assertEquals(
            [
                // content
                [
                    [
                        'resource_name' => 'foo',
                        'url' => 'http://foo.bar/exposure/foo'
                    ],
                    [
                        'resource_name' => 'bar',
                        'url' => 'http://foo.bar/exposure/bar'
                    ]
                ],
                // status code
                200
            ],
            $list
        );
    }

    /**
     * Test list resources
     */
    public function testFailHandleList()
    {
        // build mocks
        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->willThrowException(new \Exception('Dummy'));

        $requestHandler = new RequestHandler($modelListService);
        $list = $requestHandler->listResources();

        $this->assertEquals([['error' => 'Temporary unavailable!'], 500], $list);
    }

    /**
     * Test get single resource
     */
    public function testGetResource()
    {
        // build mocks
        $baseModelMock = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();
        $baseModelMock->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'foo' => 'bar'
            ]);

        \Mockery::mock('overload:\Foo\FooModel')
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($baseModelMock);

        Config::shouldReceive('has')
            ->once()
            ->with(ApplicationServiceProvider::APPLICATION_IDENTIFIER)
            ->andReturn(false);

        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([
                'foo' => \Foo\FooModel::class
            ]));

        $requestHandler = new RequestHandler($modelListService);
        $response = $requestHandler->getResource('foo', 1);

        $this->assertEquals([['foo' => 'bar'], 200], $response);
    }

    /**
     * Test get single resource
     */
    public function testNotFoundGetResource()
    {
        // build mocks
        \Mockery::mock('overload:\Foo\FooModel')
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(null);

        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([
                'foo' => \Foo\FooModel::class
            ]));

        $requestHandler = new RequestHandler($modelListService);
        $response = $requestHandler->getResource('foo', 1);

        $this->assertEquals([['error' => 'Resource foo identified by 1 could not be found!'], 404], $response);
    }


    /**
     * Test get single resource
     */
    public function testInvalidGetResource()
    {
        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([]));

        $requestHandler = new RequestHandler($modelListService);
        $response = $requestHandler->getResource('foo', 1);

        $this->assertEquals([['error' => 'Resource foo does not exists!'], 404], $response);
    }
}
