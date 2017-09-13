<?php
namespace AdrianTilita\ResourceExposer\Service;

use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class RequestHandlerTest extends TestCase
{
    public function testHandleList()
    {
        // build mocks
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->will($this->returnValue('http://foo.bar'));

        $modelListService = $this->getMockBuilder(ModelListService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modelListService->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue([
                'foo' => null,
                'bar' => null
            ]));

        $requestHandler = new RequestHandler($modelListService);
        $list = $requestHandler->handleList($requestMock);

        $this->assertEquals(
            [
                // content
                [
                    [
                        'resource_name' => 'foo',
                        'available_routes' => [
                            "GET" => [
                                'http://foo.bar/exposure/filter/foo/id/[int]',
                                'http://foo.bar/exposure/filter/foo/[field_name]/[field_value]',
                                'http://foo.bar/exposure/filter/foo/id/[int]'
                            ]
                        ]
                    ],
                    [
                        'resource_name' => 'bar',
                        'available_routes' => [
                            "GET" => [
                                'http://foo.bar/exposure/filter/bar/id/[int]',
                                'http://foo.bar/exposure/filter/bar/[field_name]/[field_value]',
                                'http://foo.bar/exposure/filter/bar/id/[int]'
                            ]
                        ]
                    ]
                ],
                // status code
                200
            ],
            $list
        );
    }
}
