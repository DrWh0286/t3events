<?php
namespace DWenzel\T3events\Tests\Unit\Domain\Model;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Domain\Model\Content;

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
class ContentTest extends UnitTestCase
{

    /**
     * @var TtContent
     */
    protected $ttContentDomainModelInstance;

    /**
     * Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->ttContentDomainModelInstance = new Content();
    }

    /**
     * @test
     */
    public function crdateCanBeSet(): void
    {
        $fieldValue = new \DateTime();
        $this->ttContentDomainModelInstance->setCrdate($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getCrdate());
    }

    /**
     * @test
     */
    public function tstampCanBeSet(): void
    {
        $fieldValue = new \DateTime();
        $this->ttContentDomainModelInstance->setTstamp($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getTstamp());
    }

    /**
     * @test
     */
    public function cTypeCanBeSet(): void
    {
        $fieldValue = 'fo123';
        $this->ttContentDomainModelInstance->setCType($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getCType());
    }

    /**
     * @test
     */
    public function headerCanBeSet(): void
    {
        $fieldValue = 'fo123';
        $this->ttContentDomainModelInstance->setHeader($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getHeader());
    }

    /**
     * @test
     */
    public function headerPositionCanBeSet(): void
    {
        $fieldValue = 'fo123';
        $this->ttContentDomainModelInstance->setHeaderPosition($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getHeaderPosition());
    }

    /**
     * @test
     */
    public function bodytextCanBeSet(): void
    {
        $fieldValue = 'fo123';
        $this->ttContentDomainModelInstance->setBodytext($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getBodytext());
    }

    /**
     * @test
     */
    public function colPosCanBeSet(): void
    {
        $fieldValue = 1;
        $this->ttContentDomainModelInstance->setColPos($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getColPos());
    }

    /**
     * @test
     */
    public function imageCanBeSet(): void
    {
        $fieldValue = 'fo123';
        $this->ttContentDomainModelInstance->setImage($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImage());
    }

    /**
     * @test
     */
    public function imageWidthCanBeSet(): void
    {
        $fieldValue = 123;
        $this->ttContentDomainModelInstance->setImagewidth($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImagewidth());
    }

    /**
     * @test
     */
    public function imageOrientCanBeSet(): void
    {
        $fieldValue = 'Test123';
        $this->ttContentDomainModelInstance->setImageorient($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImageorient());
    }

    /**
     * @test
     */
    public function imageCaptionCanBeSet(): void
    {
        $fieldValue = 'Test123';
        $this->ttContentDomainModelInstance->setImagecaption($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImagecaption());
    }

    /**
     * @test
     */
    public function imageColsCanBeSet(): void
    {
        $fieldValue = 123;
        $this->ttContentDomainModelInstance->setImagecols($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImagecols());
    }

    /**
     * @test
     */
    public function imageBorderCanBeSet(): void
    {
        $fieldValue = 123;
        $this->ttContentDomainModelInstance->setImageborder($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImageborder());
    }

    /**
     * @test
     */
    public function mediaCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setMedia($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getMedia());
    }

    /**
     * @test
     */
    public function layoutCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setLayout($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getLayout());
    }

    /**
     * @test
     */
    public function colsCanBeSet(): void
    {
        $fieldValue = 123;
        $this->ttContentDomainModelInstance->setCols($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getCols());
    }

    /**
     * @test
     */
    public function subheaderCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setSubheader($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getSubheader());
    }

    /**
     * @test
     */
    public function headerLinkCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setHeaderLink($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getHeaderLink());
    }

    /**
     * @test
     */
    public function imageLinkCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setImageLink($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImageLink());
    }

    /**
     * @test
     */
    public function imageZoomCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setImageZoom($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getImageZoom());
    }

    /**
     * @test
     */
    public function altTextCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setAltText($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getAltText());
    }

    /**
     * @test
     */
    public function titleTextCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setTitleText($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getTitleText());
    }

    /**
     * @test
     */
    public function headerLayoutCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setHeaderLayout($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getHeaderLayout());
    }

    /**
     * @test
     */
    public function listTypeCanBeSet(): void
    {
        $fieldValue = 'Test 123';
        $this->ttContentDomainModelInstance->setListType($fieldValue);
        $this->assertEquals($fieldValue, $this->ttContentDomainModelInstance->getListType());
    }
}
