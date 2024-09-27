<?php

namespace DWenzel\T3events\Utility;

use DWenzel\T3events\Resource\ResourceFactory;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class DummyController
{
}

/**
 * Class SettingsUtilityTest
 *
 * @package DWenzel\T3events\Utility
 */
class SettingsUtilityTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    public const SKIP_MESSAGE_FILEREFERENCE = 'Skipped due to incompatible implementation in core.';

    /**
     * @var SettingsUtility|AccessibleMockObjectInterface|MockObject
     */
    protected $subject;
    /**
     * @var ResourceFactory|(ResourceFactory&object&MockObject)|(ResourceFactory&MockObject)|(object&MockObject)|MockObject
     */
    private $resourceFactory;

    protected function setUp(): void
    {
        $cObj = new ContentObjectRenderer();
        $this->resourceFactory = $this->createMock(ResourceFactory::class);
        $this->subject = new SettingsUtility($cObj, $this->resourceFactory);
    }

    /**
     * @test
     */
    public function getValueByKeyInitiallyReturnsNull(): void
    {
        $config = [];
        self::assertNull(
            $this->subject->getValueByKey(null, $config, 'foo')
        );
    }

    /**
     * @test
     */
    public function getValueByKeyReturnsStringValueIfFieldIsNotSet(): void
    {
        $key = 'foo';
        $config = [
            $key => 'bar'
        ];
        $expectedValue = $config[$key];

        self::assertSame(
            $expectedValue,
            $this->subject->getValueByKey(null, $config, $key)
        );
    }

    /**
     * @test
     */
    public function getValueByKeyReturnsValueFromObjectByPath(): void
    {
        $mockParentObject = $this->getMockBuilder(
            $this->buildAccessibleProxy(AbstractDomainObject::class)
        )->setMockClassName('ParentAbstractDomainObject')->addMethods(['getFoo'])->getMock();

        $mockChildObject = $this->getMockBuilder(
            $this->buildAccessibleProxy(AbstractDomainObject::class)
        )->setMockClassName('ChildAbstractDomainObject')->addMethods(['getBar'])->getMock();

        $expectedValue = 'baz';
        $mockChildObject->_set('bar', $expectedValue);
        $mockParentObject->_set('foo', $mockChildObject);

        $key = 'fooValue';
        $config = [
            $key => [
                'field' => 'foo.bar'
            ]
        ];
        $mockParentObject->expects(self::atLeastOnce())
            ->method('getFoo')
            ->willReturn($mockChildObject);
        $mockChildObject->expects(self::atLeastOnce())
            ->method('getBar')
            ->willReturn($expectedValue);

        self::assertSame(
            $expectedValue,
            $this->subject->getValueByKey($mockParentObject, $config, $key)
        );
    }

    /**
     * @test
     */
    public function getValueByKeyReturnsDefaultValueIfObjectByPathReturnsNull(): void
    {
        $mockParentObject = $this->getMockBuilder(
            $this->buildAccessibleProxy(AbstractDomainObject::class)
        )->setMockClassName('ParentAbstractDomainObject')->addMethods(['getFoo'])->getMock();

        $expectedFallbackValue = 'fallback';

        $key = 'fooValue';
        $config = [
            $key => [
                'field' => 'foo.bar',
                'default' => $expectedFallbackValue
            ]
        ];
        $mockParentObject->expects(self::atLeastOnce())
            ->method('getFoo');

        self::assertSame(
            $expectedFallbackValue,
            $this->subject->getValueByKey($mockParentObject, $config, $key)
        );
    }

    /**
     * @test
     */
    public function getValueByKeyWrapsFieldValue(): void
    {
        //        $cObj = new ContentObjectRenderer();
        //        $this->subject->injectContentObjectRenderer($cObj);

        $mockParentObject = $this->getMockBuilder(
            $this->buildAccessibleProxy(AbstractDomainObject::class)
        )->setMockClassName('ParentAbstractDomainObject')->addMethods(['getFoo'])->getMock();

        $fieldValue = 'field value';
        $wrap = '|Wrap |';
        $expectedWrappedValue = 'Wrap field value';

        $key = 'fooValue';
        $config = [
            $key => [
                'field' => 'foo',
                'noTrimWrap' => $wrap
            ]
        ];
        $mockParentObject->expects(self::atLeastOnce())
            ->method('getFoo')
            ->willReturn($fieldValue);

        self::assertSame(
            $expectedWrappedValue,
            $this->subject->getValueByKey($mockParentObject, $config, $key)
        );
    }

    /**
     * @test
     */
    public function getControllerKeyReturnsKeyIfSet(): void
    {
        $key = 'dummy';
        $controllerKeys = [
            DummyController::class => $key
        ];
        $this->subject->controllerKey = $controllerKeys;

        self::assertSame(
            $key,
            $this->subject->getControllerKey(new DummyController())
        );
    }

    /**
     * @test
     */
    public function getControllerKeyReturnsKeyByClassName(): void
    {
        $key = 'dummy';

        self::assertSame(
            $key,
            $this->subject->getControllerKey(new DummyController())
        );
    }

    /**
     * @test
     */
    public function getFileStorageInitiallyReturnsEmptyObjectStorage(): void
    {
        $config = [];
        /** @var DomainObjectInterface|MockObject $mockObject */
        $mockObject = $this->getMockBuilder(DomainObjectInterface::class)->getMock();

        $cObj = $this->createMock(ContentObjectRenderer::class);
        $resourceFactory = $this->createMock(ResourceFactory::class);
        $subject = new SettingsUtility($cObj, $resourceFactory);

        self::assertEquals(
            GeneralUtility::makeInstance(ObjectStorage::class),
            $subject->getFileStorage($mockObject, $config)
        );
    }

    /**
     * @test
     */
    public function getFileStorageReturnsNonEmptyFileReferenceStorageFromObject(): void
    {
        /** @var AbstractDomainObject|MockObject $mockObject */
        $mockObject = $this->getAccessibleMock(
            AbstractDomainObject::class
        );

        $mockObjectStorageFromObject = $this->getMockObjectStorage(['count', 'current']);
        $mockFileReference = $this->getMockFileReference();

        $config = [
            'field' => 'foo',
            'default' => $mockObjectStorageFromObject
        ];

        $mockObjectStorageFromObject->expects($this->any())
            ->method('count')
            ->will(self::returnValue(5));
        $mockObjectStorageFromObject->expects($this->any())
            ->method('current')
            ->will(self::returnValue($mockFileReference));

        self::assertSame(
            $mockObjectStorageFromObject,
            $this->subject->getFileStorage(
                $mockObject,
                $config
            )
        );
    }

    /**
     * @test
     */
    public function getFileStorageReturnsStorageWithFileReferenceFromObject(): void
    {
        /** @var AbstractDomainObject|MockObject $mockObject */
        $mockObject = $this->getAccessibleMock(
            AbstractDomainObject::class
        );
        $mockFileReference = $this->getMockFileReference();
        $config = ['foo', 'default' => $mockFileReference];

        $fileStorage = $this->subject->getFileStorage($mockObject, $config);

        $this->assertSame(1, $fileStorage->count());
        $this->assertSame($mockFileReference, $fileStorage->current());
    }

    /**
     * @test
     */
    public function getFileStorageAddsDefaultValueIfStorageFromObjectIsEmpty(): void
    {
        $defaultValue = 'bar';
        $config = [
            'field' => 'foo',
            'default' => $defaultValue
        ];
        /** @var AbstractDomainObject|MockObject $mockObject */
        $mockObject = $this->getAccessibleMock(
            AbstractDomainObject::class
        );

        $mockFile = $this->getMockFile();
        $mockFileReference = $this->getMockFileReference();

        $this->resourceFactory->expects(self::once())
            ->method('getFileObjectByCombinedIdentifier')
            ->with($defaultValue)
            ->will(self::returnValue($mockFile));
        $this->resourceFactory->expects(self::once())
            ->method('createFileReferenceFromFileObject')
            ->with($mockFile)
            ->will(self::returnValue($mockFileReference));

        $fileStorage = $this->subject->getFileStorage($mockObject, $config);

        $this->assertSame($mockFileReference, $fileStorage->current());
    }

    /**
     * @test
     */
    public function getFileStorageAddsAlwaysValue(): void
    {
        $defaultValue = 'bar';
        $alwaysValue = 'baz';
        $config = [
            'field' => 'foo',
            'default' => $defaultValue,
            'always' => $alwaysValue
        ];
        /** @var AbstractDomainObject|MockObject $mockObject */
        $mockObject = $this->getAccessibleMock(
            AbstractDomainObject::class
        );

        $mockFile = $this->getMockFile();
        $mockFileReferenceA = $this->getMockFileReference();
        $mockFileReferenceB = $this->getMockFileReference();

        $this->resourceFactory->expects($this->exactly(2))
            ->method('getFileObjectByCombinedIdentifier')
            ->withConsecutive(
                [$defaultValue],
                [$alwaysValue]
            )
            ->will(self::returnValue($mockFile));
        $this->resourceFactory->expects($this->exactly(2))
            ->method('createFileReferenceFromFileObject')
            ->with($mockFile)
            ->willReturnOnConsecutiveCalls($mockFileReferenceA, $mockFileReferenceB);


        $fileStorage = $this->subject->getFileStorage($mockObject, $config);

        $this->assertSame(2, $fileStorage->count());
        $this->assertSame($mockFileReferenceA, $fileStorage->current(), 'reference A');
        $fileStorage->next();
        $this->assertSame($mockFileReferenceB, $fileStorage->current(), 'reference B');
    }

    /**
     * @return mixed
     */
    protected function mockResourceFactory()
    {
        /** @var ResourceFactory|MockObject $mockResourceFactory */
        $mockResourceFactory = $this->getMockBuilder(ResourceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['getFileObjectByCombinedIdentifier', 'createFileReferenceFromFileObject']
            )->getMock();
        $this->subject->injectResourceFactory($mockResourceFactory);

        return $mockResourceFactory;
    }

    /**
     * @param array $methods Methods to mock
     * @return ObjectStorage|MockObject
     */
    protected function getMockObjectStorage(array $methods = []): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(ObjectStorage::class)
            ->setMethods($methods)->getMock();
    }

    /**
     * @return MockObject
     */
    protected function getMockFileReference(): MockObject
    {
        return $this->getMockBuilder(FileReference::class)->getMock();
    }

    /**
     * @return mixed
     */
    protected function getMockFile(): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
