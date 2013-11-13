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

/**
 * Template element setter/getter
 */
abstract class AbstractElement implements ElementInterface
{

    /**
     * @param string $id
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param string $parent
     * @param string $posref
     */
    public function __construct($id, $x, $y, $width, $height, $parent, $posref)
    {
        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        $this->parent = $parent;
        $this->posref = $posref;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPos()
    {
        return array($this->x, $this->y);
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
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosref()
    {
        return $this->posref;
    }

    /**
     * {@inheritdoc}
     */
    public function setPos($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosref($posref)
    {
        $this->posref = $posref;
    }

}
