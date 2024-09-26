<?php

namespace DWenzel\T3events\Tests\Unit\Service\TCA;

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

use DWenzel\T3events\Service\BackendUtilityServiceInterface;
use DWenzel\T3events\Service\TCA\ScheduleConfigurationService;
use DWenzel\T3events\Service\TranslationService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Class ScheduleConfigurationServiceTest
 */
class ScheduleConfigurationServiceTest extends UnitTestCase
{
    /**
     * @var ScheduleConfigurationService|MockObject
     */
    protected $subject;
    private TranslationService|MockObject $translationService;
    private BackendUtilityServiceInterface|MockObject $backendUtilityService;

    /**
     * set up subject
     */
    protected function setUp(): void
    {
        $this->translationService = $this->createMock(TranslationService::class);
        $this->backendUtilityService = $this->createMock(BackendUtilityServiceInterface::class);

        $this->subject = new ScheduleConfigurationService(
            $this->translationService,
            $this->backendUtilityService
        );
    }

    /**
     * @test
     */
    public function getLabelGetsRecord(): void
    {
        $parameters = [
            'row' => [
                'uid' => 23
            ]
        ];

        $this->backendUtilityService
            ->expects($this->once())
            ->method('getRecord')
            ->with(
                SI::TABLE_SCHEDULES,
                $parameters['row']['uid']
            );

        $this->subject->getLabel($parameters);
    }

    /**
     * @test
     */
    public function getLabelGetsTranslatedDateFormat(): void
    {
        $parameters = [
            'row' => [
                'uid' => 23
            ]
        ];

        $timeStamp = time();
        $record = [
            'date' => $timeStamp
        ];
        $expectedTranslationKey = SI::TRANSLATION_FILE_DB . ':' . SI::DATE_FORMAT_SHORT;
        $this->backendUtilityService
            ->expects($this->once())
            ->method('getRecord')
            ->willReturn($record);
        $this->translationService->expects($this->once())
            ->method('translate')
            ->with($expectedTranslationKey);

        $this->subject->getLabel($parameters);
    }

    /**
     * @test
     */
    public function getLabelsSetsTitleToDate(): void
    {
        $parameters = [
            'row' => [
                'uid' => 23
            ]
        ];

        $timeStamp = time();
        $record = [
            'date' => $timeStamp
        ];
        $dateFormat = 'Y-m-d';

        $this->backendUtilityService->expects($this->once())
            ->method('getRecord')
            ->willReturn($record);
        $this->translationService->expects($this->once())
            ->method('translate')
            ->willReturn($dateFormat);
        $timeZone = new \DateTimeZone(date_default_timezone_get());

        $date = new \DateTime('now', $timeZone);
        $date->setTimestamp($timeStamp);
        $expectedDateString = $date->format($dateFormat);

        $this->subject->getLabel($parameters);

        $this->assertEquals(
            $parameters['title'],
            $expectedDateString
        );
    }

    /**
     * @test
     */
    public function getLabelGetsRecordTitleFromEventRecord(): void
    {
        $parameters = [
            'row' => [
                'uid' => 42
            ]
        ];

        $mockScheduleRecord = [
            'event' => 23
        ];
        $mockEventRecord = ['foo'];
        $mockEventTitle = 'baz';

        $this->backendUtilityService
            ->expects($this->exactly(2))
            ->method('getRecord')
            ->withConsecutive(
                [
                    SI::TABLE_SCHEDULES,
                    $parameters['row']['uid']
                ],
                [
                    SI::TABLE_EVENTS,
                    $mockScheduleRecord['event']
                ]
            )
            ->willReturnOnConsecutiveCalls(
                $mockScheduleRecord,
                $mockEventRecord
            );

        $this->backendUtilityService
            ->expects($this->once())
            ->method('getRecordTitle')
            ->with(SI::TABLE_EVENTS, $mockEventRecord)
            ->willReturn($mockEventTitle);

        $expectedTitle = ' - ' . $mockEventTitle;
        $this->subject->getLabel($parameters);
        $this->assertEquals(
            $parameters['title'],
            $expectedTitle
        );
    }
}
