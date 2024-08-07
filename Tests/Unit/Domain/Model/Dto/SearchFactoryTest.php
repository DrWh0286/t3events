<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model\Dto;

use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***************************************************************
 *  Copyright notice
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class SearchFactoryTest extends UnitTestCase
{
    /**
     * @var SearchFactory
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new SearchFactory();
    }

    /**
     * @test
     */
    public function getSetsSearchFields(): void
    {
        $subject = 'foo';
        $searchFields = 'bar,baz';

        $searchRequest = [
            'subject' => $subject
        ];
        $settings = [
            'fields' => $searchFields
        ];

        $expectedSearchObject = new Search();
        $expectedSearchObject->setSubject('foo');
        $expectedSearchObject->setFields('bar,baz');

        $actualSearchObject = $this->subject->get($searchRequest, $settings);

        $this->assertEquals($expectedSearchObject, $actualSearchObject);
    }

    /**
     * @test
     */
    public function getSetsSearchSubject(): void
    {
        $subject = 'foo';
        $searchFields = 'bar,baz';

        $searchRequest = [
            'subject' => $subject
        ];
        $settings = [
            'fields' => $searchFields
        ];

        $expectedSearchObject = new Search();
        $expectedSearchObject->setSubject('foo');
        $expectedSearchObject->setFields('bar,baz');

        $actualSearchObject = $this->subject->get($searchRequest, $settings);

        $this->assertEquals($expectedSearchObject, $actualSearchObject);
    }

    /**
     * @test
     */
    public function getSetsLocationAndRadius(): void
    {
        $location = 'foo';
        $radius = 10;
        $searchFields = 'bar,baz';

        $searchRequest = [
            'location' => $location,
            'radius' => $radius
        ];
        $settings = [
            'fields' => $searchFields
        ];

        $expectedSearchObject = new Search();
        $expectedSearchObject->setLocation('foo');
        $expectedSearchObject->setRadius(10);

        $actualSearchObject = $this->subject->get($searchRequest, $settings);

        $this->assertEquals($expectedSearchObject, $actualSearchObject);
    }
}
