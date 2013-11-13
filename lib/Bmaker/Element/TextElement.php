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

namespace Bmaker\Element;

use Bmaker\Utils\Color;

/**
 * TextElement
 */
class TextElement extends AbstractElement
{

    /** @var string */
    protected $value;
    /** @var float */
    protected $size;
    /** @var float */
    protected $angle;
    /** @var string */
    protected $font;
    /** @var Color */
    protected $txtColor;
    /** @var Color */
    protected $shadowColor;
    /** @var int */
    protected $shadowOffsetX;
    /** @var int */
    protected $shadowOffsetY;

    /**
     * Set text attributes
     *
     * @param string $value
     * @param float $size
     * @param float $angle
     * @param string $font
     * @param string $color
     */
    function setText($value, $size, $angle, $font, $color)
    {
        $this->value = $value;
        $this->size = $size;
        $this->angle = $angle;
        $this->font = $font;
        $this->txtColor = new Color($color);

        $this->shadowColor = null;
    }

    /**
     * Set text drop shadow
     *
     * @param int $x
     * @param int $y
     * @param string $color
     */
    function setDropShadow($x, $y, $color)
    {
        $this->shadowOffsetX = $x;
        $this->shadowOffsetY = $y;
        $this->shadowColor = new Color($color);
    }

    /**
     * Calculate text bbox using size, angle, font and value
     *
     * {@inheritdoc}
     */
    function getBbox()
    {
        $bbox = imageftbbox($this->size, $this->angle, $this->font, $this->value);
        $boxWidth = $bbox[4] - $bbox[0];
        $boxHeight = $bbox[1] - $bbox[5];

        $boxX = 0;
        $boxY = 0;

        $lines = explode("\n", $this->value);
        if (count($lines) > 1) {
            $boxY = -($bbox[1] / count($lines));
        }
        return array($boxX, $boxY, $boxWidth, $boxHeight);
    }

    /**
     * Render text
     *
     * {@inheritdoc}
     */
    function drawAt($canvas)
    {
        // text needs bottom-left corner
        $x = $this->x;
        $y = $this->y + $this->height;

        if ($this->shadowColor) {
            $color = imagecolorallocatealpha(
                $canvas,
                $this->shadowColor->getRed(),
                $this->shadowColor->getGreen(),
                $this->shadowColor->getBlue(),
                $this->shadowColor->getAlpha()
            );
            imagefttext(
                $canvas,
                $this->size,
                $this->angle,
                $x + $this->shadowOffsetX,
                $y + $this->shadowOffsetY,
                $color,
                $this->font,
                $this->value
            );
        }

        $color = imagecolorallocatealpha(
            $canvas,
            $this->txtColor->getRed(),
            $this->txtColor->getGreen(),
            $this->txtColor->getBlue(),
            $this->txtColor->getAlpha()
        );
        imagefttext($canvas, $this->size, $this->angle, $x, $y, $color, $this->font, $this->value);
    }
}
