<?php

namespace DWenzel\T3events\Tests\Unit\Controller\Backend;

use DWenzel\T3events\Controller\Backend\BackendViewTrait;
use DWenzel\T3events\Utility\SettingsInterface;
use DWenzel\T3events\View\ConfigurableViewInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3Fluid\Fluid\View\ViewInterface;

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
     * Set up the subject and dependencies for testing.
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

            public function getConfigurationManager(): ConfigurationManager
            {
                return $this->configurationManager;
            }

            protected function getButtonBar(): void {}

            protected function getUriBuilder(): void {}

            protected function getIconFactory(): void {}

            public function translate($key, $extension = 't3events', $arguments = null): void {}

            public function setGetViewPropertyReturnValue(array $configuration): void
            {
                $this->getViewPropertyReturnValue = $configuration;
            }

            public function getViewProperty($extbaseFrameworkConfiguration, $setting)
            {
                return $this->getViewPropertyReturnValue;
            }

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

        $this->view = $this->getMockBuilder(ViewInterface::class)
            ->getMock();
    }

    /**
     * @test
     */
    public function initializeViewAppliesSettingsToInstancesOfConfigurableViewInterface(): void
    {
        $settings = [
            ConfigurableViewInterface::SETTINGS_KEY => ['foo']
        ];

        $this->subject->setSettings($settings);

        /** @var ConfigurableViewInterface|ViewInterface|MockObject $mockView */
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
            'empty configuration' => [[]],
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
     * @dataProvider initializeViewIgnoresInvalidSettingsForRequireJsDataProvider
     */
    public function initializeViewIgnoresInvalidSettingsForRequireJs(array $configuration): void
    {
        $frameWorkConfiguration = ['bar'];
        
        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->willReturn($frameWorkConfiguration);

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
     * @dataProvider initializeViewAddsRequireJsConfigurationFromSettingsDataProvider
     */
    public function initializeViewAddsRequireJsConfigurationFromSettings(array $configuration, int $configurationCount, int $moduleCount): void
    {
        $frameWorkConfiguration = ['bar'];
        
        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->willReturn($frameWorkConfiguration);

        $this->subject->setGetViewPropertyReturnValue($configuration);

        $this->pageRenderer->expects($this->exactly($configurationCount))
            ->method('addRequireJsConfiguration');
        
        $this->pageRenderer->expects($this->exactly($moduleCount))
            ->method('loadRequireJsModule');

        $this->subject->initializeView($this->view);
    }
}
