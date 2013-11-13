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
 * PolygonElement
 */
class PolygonElement extends AbstractElement
{
    const MAX_NB_POINTS = 50;

    /** @var int */
    protected $thickness;
    /** @var Color */
    protected $color;
    /** @var Color */
    protected $fillColor;
    /** @var array */
    protected $points = array();

    /**
     * @param int $thickness
     */
    function setThickness($thickness)
    {
        $this->thickness = min(50, max(1, $thickness));
    }

    /**
     * @param string $color
     */
    function setColor($color)
    {
        $this->color = new Color($color);
    }

    /**
     * @param string $fill
     */
    function setFillColor($fill)
    {
        $this->fillColor = new Color($fill);
    }

    /**
     * Add polygon point
     *
     * First 50 points accepted
     *
     * @param int $x
     * @param int $y
     * @param boolean $rel
     */
    function addPoint($x, $y, $rel = false)
    {
        $count = count($this->points);
        if ($count > self::MAX_NB_POINTS) {
            return;
        }

        // x,y are relative to previous point
        if ($rel) {
            if ($count > 0) {
                $last = $this->points[$count - 1];
            } else {
                $last = $this->getPos();
            }
            $x += $last[0];
            $y += $last[1];
        }
        array_push($this->points, $x, $y);
    }

    /**
     * Add array of points to polygon
     *
     * @param array $points
     */
    function setPolygon(array $points)
    {
        foreach ($points as $point) {
            $this->addPoint($point[0], $point[1], $point[2]);
        }
    }

    /**
     * Not used for polygon, return [0,0,0,0] array
     *
     * {@inheritdoc}
     */
    function getBbox()
    {
        return array(0, 0, 0, 0);
    }

    /**
     * Draw polygon to it's original X,Y position
     *
     * {@inheritdoc}
     */
    function drawAt($canvas)
    {
        if (count($this->points) < 6) {
            return;
        }
        // fill first
        if ($this->fillColor) {
            $fillColor = imagecolorallocatealpha(
                $canvas,
                $this->fillColor->getRed(),
                $this->fillColor->getGreen(),
                $this->fillColor->getBlue(),
                $this->fillColor->getAlpha()
            );
            imagefilledpolygon($canvas, $this->points, count($this->points) / 2, $fillColor);
        }

        $color = imagecolorallocatealpha(
            $canvas,
            $this->color->getRed(),
            $this->color->getGreen(),
            $this->color->getBlue(),
            $this->color->getAlpha()
        );
        imagesetthickness($canvas, $this->thickness);
        imagepolygon($canvas, $this->points, count($this->points) / 2, $color);

        // reset thickness if we modified it
        if ($this->thickness > 1) {
            imagesetthickness($canvas, 1);
        }
    }
}
