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

namespace Bmaker\Utils;

/**
 * Converts color from string to int values
 */
class Color
{
    /** @var array [r,g,b,a] */
    protected $value;

    /**
     * Creates color from string, default '#FFFFFF'
     *
     * @param $strColor
     */
    public function __construct($strColor)
    {
        $this->color = $strColor;
        $this->value = $this->parseColor($strColor);
        if (!$this->value) {
            $this->value = array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0);
        }
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return int
     */
    public function getRed()
    {
        return $this->value['r'];
    }

    /**
     * @return int
     */
    public function getGreen()
    {
        return $this->value['g'];
    }

    /**
     * @return int
     */
    public function getBlue()
    {
        return $this->value['b'];
    }

    /**
     * Alpha component is between 0..127
     * (0-opaque, 127-transparent)
     *
     * @return int
     */
    public function getAlpha()
    {
        return $this->value['a'];
    }

    /**
     * Converts string color of #RRGGBBAA or rgb(r,g,b,a) into integer value
     *
     * @param $strColor
     *
     * @return array
     */
    protected function parseColor($strColor)
    {
        $strColor = strtolower($strColor);
        if (preg_match('/^#?([a-z0-9]{2})([a-z0-9]{2})([a-z0-9]{2})([a-z0-9]{2})?$/', $strColor, $m)) {
            // #RRGGBB or #RRGGBBAA
            $r = hexdec($m[1]);
            $g = hexdec($m[2]);
            $b = hexdec($m[3]);
            if (isset($m[4])) {
                $a = hexdec($m[4]);
            } else {
                $a = 255;
            }
        } elseif (preg_match('/^rgb\((\d+),(\d+),(\d+)(?:,(\d+))?\)$/', $strColor, $m)) {
            // rgb(r, g, b, a)
            $r = (int)$m[1];
            $g = (int)$m[2];
            $b = (int)$m[3];
            if (isset($m[4])) {
                $a = (int)$m[4];
            } else {
                $a = 255;
            }
        } else {
            return false;
        }
        // a needs to be turned around and scaled to 0-127
        // (0-opaque, 127-transparent)
        $a = round((255 - $a) / 255 * 127);

        return array('r' => $r, 'g' => $g, 'b' => $b, 'a' => $a);
    }
}
