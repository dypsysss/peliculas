<?php
/**
 * @version     7/05/16 20:23
 * @package     Peliculas J3x
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class HelperCinemas
{
    protected static $_rooms   = array();

    public static function getCinemaDsp($cinemaId) 
    {
        
    }
    
    /**
     * Get cinema list in text/value format for a select field
     *
     * @return  array
     */
    public static function getCinemaOptions()
    {
        $options = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id AS value, name AS text')
            ->from('#__peliculas_cinemas AS a')
            ->where('a.published = 1')
            ->order('a.name');

        // Get the options.
        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_PELICULAS_NO_CINEMA')));

        return $options;
    }
    
    public static function getRooms($cinemaID)
    {
        $returnItems = array();

        if (empty(self::$_rooms[$cinemaID])) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select('r.id, r.name, r.informacion')
                ->from($db->quoteName('#__peliculas_cinemas_rooms') . ' AS r');
            $query->where('r.cinema_id =' . (int)$cinemaID);

            $query->order('r.ordering ASC');

            $db->setQuery($query);

            $items = $db->loadObjectList();

            if (count($items)) {
                foreach ($items as $item) {
                    if (!isset($returnItems[$item->id])) {
                        if (!isset($returnItems[$item->id])) {
                            $returnItems[$item->id] = new stdClass();
                            $returnItems[$item->id]->id     = $item->id;
                            $returnItems[$item->id]->name   = $item->name;
                            $returnItems[$item->id]->informacion = $item->informacion;
                        }
                    }
                }
            }
            self::$_rooms[$cinemaID] = $returnItems;
        }

        return self::$_rooms[$cinemaID];
    }
}