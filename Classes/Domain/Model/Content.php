<?php

namespace DWenzel\T3events\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
class Content extends AbstractEntity
{
    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var string
     */
    protected $CType;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $headerPosition;

    /**
     * @var string
     */
    protected $bodytext;

    /**
     * @var int
     */
    protected $colPos;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var int
     */
    protected $imagewidth;

    /**
     * @var int
     */
    protected $imageorient;

    /**
     * @var string
     */
    protected $imagecaption;

    /**
     * @var int
     */
    protected $imagecols;

    /**
     * @var int
     */
    protected $imageborder;

    /**
     * @var string
     */
    protected $media;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var int
     */
    protected $cols;

    /**
     * @var string
     */
    protected $subheader;

    /**
     * @var string
     */
    protected $headerLink;

    /**
     * @var string
     */
    protected $imageLink;

    /**
     * @var string
     */
    protected $imageZoom;

    /**
     * @var string
     */
    protected $altText;

    /**
     * @var string
     */
    protected $titleText;

    /**
     * @var string
     */
    protected $headerLayout;

    /**
     * @var string
     */
    protected $listType;

    /**
     * @return \DateTime
     */
    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    /**
     * @param $crdate
     * @return void
     */
    public function setCrdate($crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * @return \DateTime
     */
    public function getTstamp(): \DateTime
    {
        return $this->tstamp;
    }

    /**
     * @param $tstamp
     * @return void
     */
    public function setTstamp($tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return string
     */
    public function getCType(): string
    {
        return $this->CType;
    }

    /**
     * @param $ctype
     * @return void
     */
    public function setCType($ctype): void
    {
        $this->CType = $ctype;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeaderPosition(): string
    {
        return $this->headerPosition;
    }

    /**
     * @param $headerPosition
     * @return void
     */
    public function setHeaderPosition($headerPosition): void
    {
        $this->headerPosition = $headerPosition;
    }

    /**
     * @return string
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * @param $bodytext
     * @return void
     */
    public function setBodytext($bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos
     *
     * @return int
     */
    public function getColPos(): int
    {
        return (int)$this->colPos;
    }

    /**
     * Set colpos
     *
     * @param int $colPos
     * @return void
     */
    public function setColPos($colPos): void
    {
        $this->colPos = $colPos;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param $image
     * @return void
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getImagewidth(): int
    {
        return $this->imagewidth;
    }

    /**
     * @param $imagewidth
     * @return void
     */
    public function setImagewidth($imagewidth): void
    {
        $this->imagewidth = $imagewidth;
    }

    /**
     * @return int
     */
    public function getImageorient(): int
    {
        return $this->imageorient;
    }

    /**
     * @param $imageorient
     * @return void
     */
    public function setImageorient($imageorient): void
    {
        $this->imageorient = $imageorient;
    }

    /**
     * @return string
     */
    public function getImagecaption(): string
    {
        return $this->imagecaption;
    }

    /**
     * @param $imagecaption
     * @return void
     */
    public function setImagecaption($imagecaption): void
    {
        $this->imagecaption = $imagecaption;
    }

    /**
     * @return int
     */
    public function getImagecols(): int
    {
        return $this->imagecols;
    }

    /**
     * @param $imagecols
     * @return void
     */
    public function setImagecols($imagecols): void
    {
        $this->imagecols = $imagecols;
    }

    /**
     * @return int
     */
    public function getImageborder(): int
    {
        return $this->imageborder;
    }

    /**
     * @param $imageborder
     * @return void
     */
    public function setImageborder($imageborder): void
    {
        $this->imageborder = $imageborder;
    }

    /**
     * @return string
     */
    public function getMedia(): string
    {
        return $this->media;
    }

    /**
     * @param $media
     * @return void
     */
    public function setMedia($media): void
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param $layout
     * @return void
     */
    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return int
     */
    public function getCols(): int
    {
        return $this->cols;
    }

    /**
     * @param $cols
     * @return void
     */
    public function setCols($cols): void
    {
        $this->cols = $cols;
    }

    /**
     * @return string
     */
    public function getSubheader(): string
    {
        return $this->subheader;
    }

    /**
     * @param $subheader
     * @return void
     */
    public function setSubheader($subheader): void
    {
        $this->subheader = $subheader;
    }

    /**
     * @return string
     */
    public function getHeaderLink(): string
    {
        return $this->headerLink;
    }

    /**
     * @param $headerLink
     * @return void
     */
    public function setHeaderLink($headerLink): void
    {
        $this->headerLink = $headerLink;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    /**
     * @param $imageLink
     * @return void
     */
    public function setImageLink($imageLink): void
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string
     */
    public function getImageZoom(): string
    {
        return $this->imageZoom;
    }

    /**
     * @param $imageZoom
     * @return void
     */
    public function setImageZoom($imageZoom): void
    {
        $this->imageZoom = $imageZoom;
    }

    /**
     * @return string
     */
    public function getAltText(): string
    {
        return $this->altText;
    }

    /**
     * @param $altText
     * @return void
     */
    public function setAltText($altText): void
    {
        $this->altText = $altText;
    }

    /**
     * @return string
     */
    public function getTitleText(): string
    {
        return $this->titleText;
    }

    /**
     * @param $titleText
     * @return void
     */
    public function setTitleText($titleText): void
    {
        $this->titleText = $titleText;
    }

    /**
     * @return string
     */
    public function getHeaderLayout(): string
    {
        return $this->headerLayout;
    }

    /**
     * @param $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout): void
    {
        $this->headerLayout = $headerLayout;
    }

    /**
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param $listType
     * @return void
     */
    public function setListType($listType): void
    {
        $this->listType = $listType;
    }
}
