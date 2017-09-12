<?php
namespace AdrianTilita\ResourceExposer\Service;

use AdrianTilita\ResourceExposer\Base\CacheInterface;
use NeedleProject\Common\ClassFinder;
use PHPUnit\Framework\TestCase;

class ModelListServiceTest extends TestCase
{
    /**
     * Test search method
     */
    public function testSearch()
    {
        // build mock
        $classFinderMock = $this->getMockBuilder(ClassFinder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $classFinderMock->expects($this->once())
            ->method('findClasses')
            ->willReturn([
                'Foo\\Bar',
                'Bar\\Foo'
            ]);

        $cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock->expects($this->once())
            ->method('store')
            ->with(
                $this->equalTo(ModelListService::STORE_KEY),
                $this->equalTo([
                    'bar' => 'Foo\\Bar',
                    'foo' => 'Bar\\Foo'
                ])
            )
            ->willReturn(null);

        $service = new ModelListService(
            $classFinderMock,
            $cacheMock
        );

        $service->search();
    }

    /**
     * Test fetchAll method
     */
    public function testFetchAll()
    {
        // build mocks
        $classFinderMock = $this->getMockBuilder(ClassFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock->expects($this->once())
            ->method('get')
            ->willReturn([
                'bar' => 'Foo\\Bar'
            ]);

        $cacheMock->expects($this->once())
            ->method('has')
            ->willReturn(true);

        // build service
        $service = new ModelListService(
            $classFinderMock,
            $cacheMock
        );

        $list = $service->fetchAll();
        $this->assertEquals(
            ['bar' => 'Foo\\Bar'],
            $list
        );
    }
}
