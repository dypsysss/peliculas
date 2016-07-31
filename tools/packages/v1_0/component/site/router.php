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
 * Routing class from com_peliculas
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasRouter extends JComponentRouterBase
{
    /**
     * Build the route for the com_jinmo component
     *
     * @param   array  &$query  An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     */
    function build(&$query)
    {
        $segments = array();

        // Get a menu item based on Itemid or currently active
        if (empty($query['Itemid'])) {
            $menuItem = $this->menu->getActive();
        } else {
            $menuItem = $this->menu->getItem($query['Itemid']);
        }
        $option     = (empty($menuItem->component)) ? null : $menuItem->component;

        $mView 		= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
        $mId 		= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];
        $mItemId 	= (empty($menuItem->query['item_id'])) ? null : (int)$menuItem->query['item_id'];

        $view = !empty($query['view']) ? $query['view'] : null;
        $id = !empty($query['id']) ? $query['id'] : null;

        if ($option != 'com_peliculas') {
            // Cuando no estas en componente
            $query['option'] = 'com_peliculas';
            $option = 'com_peliculas';
        }

        if ($view && $option == 'com_peliculas') {
            if ($view != $mView) {
                $segments[] = $view;
            }

            unset($query['view']);

            if ($view == 'movie') {
                if ($view == $mView && intval($id) > 0 && intval($id) == $mId) {
                    // unset($query['id']);
                } else if ($mView == 'movies' && intval($id) > 0) {
                    // $segments[] = $id;
                    // unset($query['id']);
                } else if ($mView == 'events' && intval($id) > 0) {
                    // $segments[] = $id;
                    // unset($query['id']);
                }

                list($tmp, $id) = explode(':', $query['id'], 2);
                $segments[] = $id;

                unset($query['id']);
                unset($query['Itemid']);
            }

            if ($view == 'movies') {

            }

            if ($view == 'events') {

            }
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array  &$segments  The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = array();
        $total = count($segments);

        for ($i = 0; $i < $total; $i++) {
            $segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
        }

        // Get the active menu item.
        $item       = $this->menu->getActive();
        $params     = JComponentHelper::getParams('com_peliculas');
        $advanced   = $params->get('sef_advanced_link', 0);
/*
        // Count route segments
        $count = count($segments);

        if (!isset($item))
        {
            $vars['view'] = $segments[0];
            $vars['id'] = $segments[$count - 1];
            return $vars;
        }
*/
/*
        // From the categories view, we can only jump to a category.
        $id = (isset($item->query['id']) && $item->query['id'] > 1) ? $item->query['id'] : 'root';

        foreach ($segments as $segment) {
            $segment = $advanced ? str_replace(':', '-', $segment) : $segment;

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from('#__peliculas_movies')
                ->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
            $db->setQuery($query);
            $nid = $db->loadResult();

            $vars['id'] = $nid;
            $vars['view'] = 'movie';
        }
*/


        $vars['view'] = $segments[0];

        switch ($vars['view']) {
            case 'movie':
                $alias = str_replace(':', '-', $segments[1]);

                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select($db->quoteName('id'))
                    ->from('#__peliculas_movies')
                    ->where($db->quoteName('alias') . ' = ' . $db->quote($alias));
                $db->setQuery($query);
                $nid = $db->loadResult();

                // list($id, $alias) = explode(':', $segments[1], 2);
                // $vars['id'] = (int) $segments[$count - 1];
                $vars['id'] = $nid;
//                $vars['alias'] = $alias;
//                echo var_dump($vars)."<hr/>";
                break;

            default:
                break;
        }

        return $vars;
    }
}