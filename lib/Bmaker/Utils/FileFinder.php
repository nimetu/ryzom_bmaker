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
 * File finder interface around glob() function
 */
class FileFinder implements FinderInterface
{
    /** @var string */
    protected $pattern;

    /** @var boolean */
    protected $ignoreCase;

    /** @var array */
    protected $fileMaps;

    /**
     * @param string $pattern
     * @param bool $caseSensitive
     */
    public function __construct($pattern, $caseSensitive = false)
    {
        $this->pattern = $pattern;
        $this->ignoreCase = $caseSensitive !== true;

        $this->fileMaps = array();
    }

    /**
     * Fill file maps using glob(pattern)
     */
    private function updateMaps()
    {
        $this->fileMaps = array();

        $files = glob($this->pattern);
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }
            $name = basename($file);
            if ($this->ignoreCase) {
                $name = strtolower($name);
            }
            $this->fileMaps[$name] = $file;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($file)
    {
        if (empty($this->fileMaps)) {
            $this->updateMaps();
        }

        if ($this->ignoreCase) {
            $file = strtolower($file);
        }

        if (isset($this->fileMaps[$file])) {
            return $this->fileMaps[$file];
        }

        return null;
    }
}
