<?php
/**
 * Created by PhpStorm.
 * User: adrian-tilita
 * Date: 9/12/17
 * Time: 3:04 PM
 */

namespace AdrianTilita\ResourceExposer\Service;


use AdrianTilita\ResourceExposer\Base\CacheInterface;
use NeedleProject\Common\ClassFinder;

class ModelListServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSearch()
    {
        $classFinderMock = $this->getMockBuilder(ClassFinder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $classFinderMock->expects($this->once())
            ->method('findClasses')
            ->willReturn([
                'Foo\\Bar'
            ]);

        $cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock->expects($this->once())
            ->method('store')
            ->willReturn(null);

        $service = new ModelListService(
            $classFinderMock,
            $cacheMock
        );

        $service->search();
    }

    public function testFetchAll()
    {
        $classFinderMock = $this->getMockBuilder(ClassFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock->expects($this->once())
            ->method('get')
            ->willReturn([
                'Foo\\Bar' => 'bar'
            ]);

        $cacheMock->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $service = new ModelListService(
            $classFinderMock,
            $cacheMock
        );

        $list = $service->fetchAll();
        var_dump($list);
    }
}
