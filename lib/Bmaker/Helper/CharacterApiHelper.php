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

namespace Bmaker\Helper;

use Bmaker\Translator\TranslatorInterface;

/**
 * Register character api related keywords
 */
class CharacterApiHelper implements KeywordsCollection
{

    /** @var \SimpleXMLElement */
    protected $xml;

    /** @var TranslatorInterface */
    protected $translate;

    /**
     * @param TranslatorInterface $translate
     */
    public function __construct(TranslatorInterface $translate)
    {
        $this->xml = null;
        $this->translate = $translate;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public function setCharacter(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    /**
     * @param string $id
     * @param array $params
     *
     * @return mixed
     */
    public function get($id, array $params = array())
    {
        if (!$this->xml) {
            return null;
        }

        $translate = isset($params['translate']) ? $params['translate'] : true;

        switch ($id) {
            case 'name':
                $result = (string)$this->xml->name;
                break;
            case 'shard':
                $result = ucwords($this->xml->shard);
                break;
            case 'guild':
                if (isset($this->xml->guild)) {
                    $result = (string)$this->xml->guild->name;
                } else {
                    $result = '';
                }
                break;
            case 'race':
                $result = ucwords($this->xml->race);
                break;
            case 'title':
                // fall thru
            case 'titleid':
                $result = (string)$this->xml->titleid;
                if ($translate && !empty($result) && $result[0] != '#') {
                    $result = $this->translate->trans($this->xml->titleid.'.title');
                } else {
                    if (substr($result, 0, 1) == '#') {
                        $result = substr($result, 1);
                    }
                }
                break;
            case 'status':
                $online = intval($this->xml->played['lastlogin']) > intval($this->xml->played['lastlogout']);
                if ($online) {
                    $key = 'uiOnline';
                } else {
                    $key = 'uiOffline';
                }
                $result = $this->translate->trans($key);
                break;
            case 'last_seen':
                $login = intval($this->xml->played['lastlogin']);
                $logout = intval($this->xml->played['lastlogout']);
                if ($login > $logout) {
                    $result = $this->translate->trans('uiOnline');
                } else {
                    $result = gmstrftime('%a, %d %b %Y %H:%M:%S', $logout);
                }
                break;
            case 'last_seen_status':
                $timestamp = max(intval($this->xml->played['lastlogin']), intval($this->xml->played['lastlogout']));
                $result = gmstrftime('%a, %d %b %Y %H:%M:%S', $timestamp);
                break;
            case 'played_time':
                $result = $this->sec2txt((int)$this->xml->played);
                break;
            case 'race_logo':
                $result = (string)$this->xml->race;
                break;
            case 'guild_logo':
                if (isset($this->xml->guild)) {
                    $result = (string)$this->xml->guild->icon;
                } else {
                    $result = '';
                }
                break;
            case 'cult_logo':
                $result = (string)$this->xml->cult;
                break;
            case 'civ_logo':
                $result = (string)$this->xml->civ;
                break;
            default:
                $result = null;
        }
        return $result;
    }

    /**
     * Formats seconds to D day(s), HH:MM:SS
     *
     * @param int $sec
     *
     * @return string
     */
    protected function sec2txt($sec)
    {
        $num = abs($sec);
        $sec = $num % 60;
        $min = floor(($num / 60) % 60);
        $hour = floor(($num / 3600) % 24);
        $days = floor(($num / 86400));
        $ret = sprintf('%02d:%02d:%02d', $hour, $min, $sec);
        if ($days > 0) {
            if ($days == 1) {
                $ret = $this->translate->trans('uiDayPlayed', array('%day%' => $days)).' '.$ret;
            } else {
                $ret = $this->translate->trans('uiDaysPlayed', array('%days%' => $days)).' '.$ret;
            }
        }
        return $ret;
    }
}
