<?php

namespace DWenzel\T3events\Tests\Unit\Hooks;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Hooks\ItemsProcFunc;
use DWenzel\T3events\Utility\TemplateLayoutUtility;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class ItemsProcFuncTest
 * @package DWenzel\T3events\Tests\Unit\Hooks
 */
class ItemsProcFuncTest extends UnitTestCase
{
    /**
     * @var ItemsProcFunc | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var TemplateLayoutUtility | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateLayoutUtility;

    /**
     *  set up
     */
    protected function setUp(): void
    {
        $this->templateLayoutUtility = $this->getMockBuilder(TemplateLayoutUtility::class)->getMock();

        //        $this->subject = $this->getAccessibleMock(
        //            ItemsProcFunc::class, ['getLanguageService'], [$this->templateLayoutUtility], '', false
        //        );

        $this->subject = new ItemsProcFunc($this->templateLayoutUtility);
    }

    /**
     * @test
     */
    public function user_templateLayoutGetsTemplateLayoutsFromUtility(): void
    {
        $extensionKey = ItemsProcFunc::EXTENSION_KEY;

        $config = [];
        $this->templateLayoutUtility->expects($this->once())
            ->method('getLayouts')
            ->with($extensionKey)
            ->will($this->returnValue([]));
        $this->subject->user_templateLayout($config);
    }

    /**
     * @test
     */
    public function user_templateLayoutGetsPidFromConfigRow(): void
    {
        $extensionKey = ItemsProcFunc::EXTENSION_KEY;
        $pageId = 1;

        $config = [
            'row' => [
                'pid' => $pageId
            ]
        ];
        $this->templateLayoutUtility->expects($this->once())
            ->method('getLayouts')
            ->with($extensionKey, $pageId)
            ->will($this->returnValue([]));
        $this->subject->user_templateLayout($config);
    }

    /**
     * @test
     */
    public function user_templateLayoutGetsPidFromConfigFlexParentDatabaseRow(): void
    {
        $extensionKey = ItemsProcFunc::EXTENSION_KEY;
        $pageId = 1;

        $config = [
            'flexParentDatabaseRow' => [
                'pid' => $pageId
            ]
        ];
        $this->templateLayoutUtility->expects($this->once())
            ->method('getLayouts')
            ->with($extensionKey, $pageId)
            ->will($this->returnValue([]));
        $this->subject->user_templateLayout($config);
    }

    /**
     * @test
     */
    public function user_templateLayoutAddsItemsToConfig(): void
    {
        $mockLanguageService = $this->getMockBuilder(LanguageService::class)
            ->disableOriginalConstructor()->getMock();
        $GLOBALS['LANG'] = $mockLanguageService;

        $extensionKey = ItemsProcFunc::EXTENSION_KEY;
        $title = 'foo';
        $templateName = 'bar';
        $additionalLayouts = [
            [$title, $templateName]
        ];
        $config = [
            'items' => []
        ];
        $this->templateLayoutUtility->expects($this->once())
            ->method('getLayouts')
            ->with($extensionKey)
            ->will($this->returnValue($additionalLayouts));

        $mockLanguageService->expects($this->once())
            ->method('sL')
            ->with($title)
            ->will($this->returnValue($title));

        $this->subject->user_templateLayout($config);
        $expectedConfig = [
            'items' => $additionalLayouts
        ];
        $this->assertSame(
            $expectedConfig,
            $config
        );
    }
}
