<?php
//
// RyzomBmaker - https://github.com/nimetu/ryzom_bmaker
// Copyright (c) 2013 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomBmaker.
//
// RyzomBmaker is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomBmaker is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

namespace Bmaker\Render;

use Bmaker\Element\ElementInterface;
use Bmaker\Utils\Color;

/**
 * Renderer setter/getter
 */
abstract class AbstractRenderer implements RenderInterface
{

    /** @var int */
    protected $minWidth;
    /** @var int */
    protected $minHeight;
    /** @var int */
    protected $maxWidth;
    /** @var int */
    protected $maxHeight;

    /** @var string */
    protected $ryzomLinkPosref;
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;
    /** @var Color */
    protected $bgColor;
    /** @var string */
    protected $bgImage;
    /** @var string */
    protected $race;
    /** @var string */
    protected $cult;
    /** @var string */
    protected $civ;

    /** @var ElementInterface[] */
    protected $elements;

    /**
     * Setup default renderer
     */
    public function __construct()
    {
        $this->elements = array();

        // minimum output image size
        $this->minWidth = 5;
        $this->minHeight = 5;

        // maximum output image size
        $this->maxWidth = 512;
        $this->maxHeight = 100;

        // default values
        $this->ryzomLinkPosref = true;
        $this->width = 500;
        $this->height = 70;
        $this->bgColor = false;
        $this->bgImage = 'banner_bg.png';
        $this->race = '';
        $this->cult = '';
        $this->civ = '';
    }

    /**
     * {@inheritdoc}
     */
    public function add(ElementInterface $elm)
    {
        $id = $elm->getId();
        $this->elements[$id] = $elm;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($width, $height)
    {
        $this->width = max($this->minWidth, min($this->maxWidth, $width));
        $this->height = max($this->minHeight, min($this->maxHeight, $height));
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return array($this->width, $this->height);
    }

    /**
     * {@inheritdoc}
     */
    public function setBackground($color, $image)
    {
        if (!empty($color)) {
            $this->bgColor = new Color($color);
        } else {
            $this->bgColor = false;
        }
        $this->bgImage = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackground()
    {
        return array($this->bgColor, $this->bgImage);
    }

    /**
     * {@inheritdoc}
     */
    public function setRyzomLink($posref)
    {
        $this->ryzomLinkPosref = $posref;
    }

    /**
     * {@inheritdoc}
     */
    public function getRyzomLink()
    {
        return $this->ryzomLinkPosref;
    }

}
