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

namespace Bmaker\Translator;

/**
 * Translator
 *
 * Uses ryzom_extra library
 */
class Translator implements TranslatorInterface
{

    /** @var string */
    protected $defaultLanguage;

    /** @var array */
    protected $messages;

    /**
     * @param string $defaultLanguage
     */
    function __construct($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
        $this->messages = array();

        // load default translations
        $this->register(include __DIR__.'/../../../resources/lang_en.php', 'en');
        $this->register(include __DIR__.'/../../../resources/lang_fr.php', 'fr');
        $this->register(include __DIR__.'/../../../resources/lang_de.php', 'de');
    }

    /**
     * {@inheritdoc}
     */
    function getLang()
    {
        return $this->defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    function setLang($lang)
    {
        $this->defaultLanguage = $lang;
    }

    /**
     * {@inheritdoc}
     */
    function register(array $messages, $lang = null)
    {
        if ($lang === null) {
            $lang = $this->defaultLanguage;
        }
        foreach ($messages as $k => $v) {
            $k = strtolower($k);
            $this->messages[$lang][$k] = $v;
        }
    }

    /**
     * {@inheritdoc}
     */
    function trans($id, array $params = array(), $lang = null)
    {
        if ($lang === null) {
            $lang = $this->defaultLanguage;
        }

        $key = strtolower($id);

        // check local translation first
        if (isset($this->messages[$lang][$key])) {
            $result = $this->messages[$lang][$key];
            if (!empty($params)) {
                $result = strtr($result, $params);
            }
        } else {
            // pull in translation from external source
            $female = isset($params['female']) ? $params['female'] : false;
            $result = ryzom_translate($id, $lang, $female);
        }
        return $result;
    }
}
