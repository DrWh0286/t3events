<?php

namespace DWenzel\T3events\Tests\Unit\Controller\Backend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DWenzel\T3events\Controller\Backend\BackendViewTrait;
use DWenzel\T3events\InvalidRequestException;
use DWenzel\T3events\Utility\SettingsInterface;
use DWenzel\T3events\View\ConfigurableViewInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Class BackendViewTraitTest
 */
class BackendViewTraitTest extends UnitTestCase
{
    /**
     * @var BackendViewTrait|MockObject
     */
    protected $subject;

    /**
     * @var ModuleTemplate|MockObject
     */
    protected $moduleTemplate;

    /**
     * @var ViewInterface|MockObject
     */
    protected $view;

    /**
     * @var PageRenderer|MockObject
     */
    protected $pageRenderer;

    /**
     * @var ConfigurationManager|MockObject
     */
    protected $configurationManager;

    /**
     * set up subject
     */
    protected function setUp(): void
    {
        $this->configurationManager = $this->getMockBuilder(ConfigurationManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfiguration'])
            ->getMock();

        $this->subject = new class ($this->configurationManager) {
            use BackendViewTrait;

            private $getViewPropertyReturnValue = [];

            public function __construct(private readonly ConfigurationManager $configurationManager)
            {
            }

            public function getConfigurationManager()
            {
                return $this->configurationManager;
            }

            /**
             * @return ButtonBar
             */
            protected function getButtonBar()
            {

            }

            /**
             * @return UriBuilder
             */
            protected function getUriBuilder()
            {

            }

            /**
             * @return IconFactory
             */
            protected function getIconFactory()
            {

            }

            /**
             * Translate a given key
             *
             * @param string $key
             * @param string $extension
             * @param array $arguments
             * @return string
             */
            public function translate($key, $extension = 't3events', $arguments = null)
            {

            }

            public function setGetViewPropertyReturnValue(array $configuration)
            {
                $this->getViewPropertyReturnValue = $configuration;
            }

            public function getViewProperty($extbaseFrameworkConfiguration, $setting)
            {
                return $this->getViewPropertyReturnValue;
            }

            /**
             * @param array $settings
             */
            public function setSettings(array $settings): void
            {
                $this->settings = $settings;
            }
        };

        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addRequireJsConfiguration', 'loadRequireJsModule'])
            ->getMock();
        $this->moduleTemplate = $this->getMockBuilder(ModuleTemplate::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPageRenderer'])
            ->getMock();
        $this->moduleTemplate
            ->method('getPageRenderer')
            ->willReturn($this->pageRenderer);

        $this->view = $this->getMockBuilder(BackendTemplateView::class)
            ->onlyMethods(['getModuleTemplate'])
            ->getMock();
        $this->view
            ->method('getModuleTemplate')
            ->willReturn($this->moduleTemplate);
    }

    /**
     * @test
     */
    public function initializeViewAppliesSettingsToInstancesOfConfigurableViewInterface(): void
    {
        $settings = [
            ConfigurableViewInterface::SETTINGS_KEY => ['foo']
        ];

        $this->subject->setSettings(
            $settings
        );
        /** @var ConfigurableViewInterface|ViewInterface|\PHPUnit_Framework_MockObject_MockObject $mockView */
        $mockView = $this->getMockBuilder(ConfigurableViewInterface::class)
            ->onlyMethods(['apply'])
            ->getMockForAbstractClass();
        $mockView->expects($this->once())->method('apply')
            ->with($settings[ConfigurableViewInterface::SETTINGS_KEY]);
        $this->subject->initializeView($mockView);
    }


    /**
     * Data provider for invalid RequireJs settings
     * @return array
     */
    public function initializeViewIgnoresInvalidSettingsForRequireJsDataProvider(): array
    {
        return [
            'empty configuration' => [[]]
            ,
            'empty requireJs Configuration' => [
                [SettingsInterface::REQUIRE_JS => []]
            ],
            'requireJs configuration must not be string' => [
                [SettingsInterface::REQUIRE_JS => 'foo']
            ],
            'requireJs configuration must not be integer' => [
                [SettingsInterface::REQUIRE_JS => 1]
            ]
        ];
    }

    /**
     * @test
     * @param array $configuration
     * @dataProvider initializeViewIgnoresInvalidSettingsForRequireJsDataProvider
     */
    public function initializeViewIgnoresInvalidSettingsForRequireJs(array $configuration): void
    {
        $this->markAsRisky();
        //$this->expectException(InvalidRequestException::class);
        //$this->expectExceptionCode(1684167963);
        $frameWorkConfiguration = ['bar'];
        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->will($this->returnValue($frameWorkConfiguration));

        $this->subject->setGetViewPropertyReturnValue($configuration);

        $this->pageRenderer->expects($this->never())
            ->method('addRequireJsConfiguration');
        $this->subject->initializeView($this->view);
    }

    /**
     * Data provider for valid RequireJs settings
     */
    public function initializeViewAddsRequireJsConfigurationFromSettingsDataProvider(): array
    {
        return [
            'register 1 namespace and 0 modules' => [
                [
                    SettingsInterface::REQUIRE_JS => [
                        'nameOfFooLibrary' => [
                            SettingsInterface::PATH => '/path/to/stuff'
                        ]
                    ]
                ],
                1,
                0
            ],
            'register 2 namespaces and 0 modules' => [
                [
                    SettingsInterface::REQUIRE_JS => [
                        'nameOfFooLibrary' => [
                            SettingsInterface::PATH => '/path/to/stuff'
                        ],
                        'nameOfBarLibrary' => [
                            SettingsInterface::PATH => '/other/stuff'
                        ]
                    ]
                ],
                1,
                0
            ],
            'register 1 namespace and 2 modules' => [
                [
                    SettingsInterface::REQUIRE_JS => [
                        'nameOfFooLibrary' => [
                            SettingsInterface::PATH => '/path/to/stuff',
                            SettingsInterface::MODULES => [
                                0 => 'fancyStuffIWrote',
                                10 => 'moreStuffIWrote'
                            ]
                        ]
                    ]
                ],
                1,
                2
            ]
        ];
    }

    /**
     * @test
     * @param array $configuration
     * @param $configurationCount Expected number of configurations
     * @param integer $moduleCount Expected number of modules
     * @dataProvider initializeViewAddsRequireJsConfigurationFromSettingsDataProvider
     */
    public function initializeViewAddsRequireJsConfigurationFromSettings(array $configuration, $configurationCount, $moduleCount): void
    {
        $this->markAsRisky();
        //$this->expectException(InvalidRequestException::class);
        //$this->expectExceptionCode(1684167963);

        $frameWorkConfiguration = ['bar'];
        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->will($this->returnValue($frameWorkConfiguration));

        $this->subject->setGetViewPropertyReturnValue($configuration);

        $this->pageRenderer->expects($this->exactly($configurationCount))
            ->method('addRequireJsConfiguration');
        $this->pageRenderer->expects($this->exactly($moduleCount))
            ->method('loadRequireJsModule');

        $this->subject->initializeView($this->view);
    }
}
