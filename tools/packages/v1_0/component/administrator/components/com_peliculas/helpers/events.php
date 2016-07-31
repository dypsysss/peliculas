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

class HelperEvents
{
    protected static $_rooms = array();

    public static function getRooms($eventID)
    {
        $returnItems = array();

        if (empty(self::$_rooms[$eventID])) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select('er.id, er.cinema_id, er.room_id, er.movie_id, er.informacion')
                ->from($db->quoteName('#__peliculas_events_rooms') . ' AS er');

            $query->select('c.name as cinema_name')
                ->join('LEFT', '#__peliculas_cinemas as c on c.id = er.cinema_id');

            $query->select('r.name as room_name')
                ->join('LEFT', '#__peliculas_cinemas_rooms as r on r.id = er.room_id');

            $query->select('m.name as movie_name')
                ->join('LEFT', '#__peliculas_movies as m on m.id = er.movie_id');

            $query->where('er.event_id =' . (int)$eventID);

            $query->order('er.ordering ASC');

            $db->setQuery($query);

            $items = $db->loadObjectList();

            if (count($items)) {
                foreach ($items as $item) {
                    if (!isset($returnItems[$item->id])) {
                        if (!isset($returnItems[$item->id])) {
                            $returnItems[$item->id] = new stdClass();
                            $returnItems[$item->id]->id     = $item->id;
                            $returnItems[$item->id]->cinema_id  = $item->cinema_id;
                            $returnItems[$item->id]->cinema_name= $item->cinema_name;
                            $returnItems[$item->id]->room_id    = $item->room_id;
                            $returnItems[$item->id]->room_name  = $item->room_name;
                            $returnItems[$item->id]->movie_id   = $item->movie_id;
                            $returnItems[$item->id]->movie_name = $item->movie_name;
                            $returnItems[$item->id]->informacion = $item->informacion;
                        }
                    }
                }
            }
            self::$_rooms[$eventID] = $returnItems;
        }

        return self::$_rooms[$eventID];
    }
}