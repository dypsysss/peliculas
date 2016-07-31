<?php
/**
 * @version     16/05/15 18:31
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Peliculas Component Route Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
abstract class PeliculasHelperRoute
{
    protected static $lookup;

    /**
     * Get the URL route for a movie from a movie ID and language
     *
     * @param   integer  $id        The id of the contact
     * @param   mixed    $language  The id of the language being used.
     *
     * @return  string  The link to the contact
     */
    public static function getPeliculaRoute($id, $language = 0)
    {
        $needles = array(
            'movie' => array((int)$id)
        );

        // Create the link
        $link = 'index.php?option=com_peliculas&view=movie&id=' . $id;

        if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
            $link .= '&lang=' . $language;
            $needles['language'] = $language;
        }

        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }

    /**
     * Find an item ID.
     *
     * @param   array  $needles  An array of language codes.
     *
     * @return  mixed  The ID found or null otherwise.
     */
    protected static function _findItem($needles = null)
    {
        $app      = JFactory::getApplication();
        $menus    = $app->getMenu('site');
        $language = isset($needles['language']) ? $needles['language'] : '*';

        // Prepare the reverse lookup array.
        if (!isset(self::$lookup[$language])) {
            self::$lookup[$language] = array();

            $component  = JComponentHelper::getComponent('com_peliculas');
            $attributes = array('component_id');
            $values     = array($component->id);

            if ($language != '*') {
                $attributes[] = 'language';
                $values[] = array($needles['language'], '*');
            }

            $items = $menus->getItems($attributes, $values);

            foreach ($items as $item) {
                if (isset($item->query) && isset($item->query['view'])) {
                    $view = $item->query['view'];

                    if (!isset(self::$lookup[$language][$view])) {
                        self::$lookup[$language][$view] = array();
                    }

                    if (isset($item->query['id'])) {
                        /**
                         * Here it will become a bit tricky
                         * language != * can override existing entries
                         * language == * cannot override existing entries
                         */
                        if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
                        {
                            self::$lookup[$language][$view][$item->query['id']] = $item->id;
                        }
                    }
                }
            }
        }

        if ($needles) {
            foreach ($needles as $view => $ids) {
                if (isset(self::$lookup[$language][$view])) {
                    foreach ($ids as $id) {
                        if (isset(self::$lookup[$language][$view][(int) $id])) {
                            return self::$lookup[$language][$view][(int) $id];
                        }
                    }
                }
            }
        }

        // Check if the active menuitem matches the requested language
        $active = $menus->getActive();
        if ($active
            && $active->component == 'com_peliculas'
            && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
        {
            return $active->id;
        }

        // If not found, return language specific home link
        $default = $menus->getDefault($language);

        return !empty($default->id) ? $default->id : null;
    }
}