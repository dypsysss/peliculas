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
 * Peliculas Component Events Helper
 */
class PeliculasHelperEvents
{
    public static function getEventsMovies($movieID) {

        $options    = null;
        $db         = JFactory::getDbo();
        // Define null and now dates
        $nullDate   = $db->quote($db->getNullDate());
        $nowDate    = $db->quote(JFactory::getDate()->toSql());

        $query  = $db->getQuery(true);

        $query->select('er.id, er.cinema_id, er.room_id, er.movie_id, er.informacion')
            ->from($db->quoteName('#__peliculas_events_rooms') . ' AS er');

        $query->select('a.*')
            ->join('LEFT', '#__peliculas_events as a on a.id = er.event_id');

        $query->select('c.name as cinema_name')
            ->join('LEFT', '#__peliculas_cinemas as c on c.id = er.cinema_id');

        $query->select('r.name as room_name')
            ->join('LEFT', '#__peliculas_cinemas_rooms as r on r.id = er.room_id');

        $query->select('m.name as movie_name, m.poster')
            ->select('CASE WHEN CHAR_LENGTH(m.alias) THEN CONCAT_WS(":", m.id, m.alias) ELSE m.id END as movie_slug')
            ->join('LEFT', '#__peliculas_movies as m on m.id = er.movie_id');


        $query->where('a.published = 1');

        // Filter by start and end dates.
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
            ->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

        $query->where('(er.movie_id = ' . $movieID . ')');

        $orderCol  = 'a.ordering';
        $orderDirn = 'ASC';

        $query->order($db->escape($orderCol.' '.$orderDirn));

        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (RuntimeException $e) {
            return false;
        }

        return $options;
    }
}