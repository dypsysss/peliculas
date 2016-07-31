<?php
/**
 * @package     Peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_peliculas/helpers/route.php';

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_peliculas/models', 'JPeliculasModel');

/**
 * Helper for mod_peliculas_featured
 *
 * @package     JGuide
 * @subpackage  mod_peliculas_featured
 */
abstract class ModPeliculasFeaturedHelper
{
    /**
     * Get a list of popular articles from the articles model
     *
     * @param   \Joomla\Registry\Registry  &$params  object holding the models parameters
     *
     * @return mixed
     */
    public static function getList(&$params)
    {
        $db     = JFactory::getDbo();
        $count  = (int) $params->get('count', 6);
        $model  = JModelLegacy::getInstance('Movies', 'PeliculasModel', array('ignore_request' => true));

        $model->setState('params', $params);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', $count);
        $model->setState('filter.published', 1);
        $model->setState('filter.featured', 'only');

        // Ordering
        $model->setState('list.ordering', 'a.hits');
        $model->setState('list.direction', 'DESC');

        $items = $model->getItems();

        // echo var_dump($items)."<hr/>";
        return $items;
    }
}