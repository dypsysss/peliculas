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

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasModelEvents extends JModelList
{
    /**
     * Model context string.
     *
     * @var  string
     */
    protected $context = 'com_peliculas.events';

    /**
     * Constructor.
     *
     * @param  array  An optional associative array of configuration settings.
     * @see    JModelList
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            // This fields concern the ordering
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'published', 'a.published',
                'access', 'a.access', 'access_level',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'modified', 'a.modified',
                'ordering', 'a.ordering',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'hits', 'a.hits',
                'language', 'a.language'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();

        $params = JComponentHelper::getParams('com_peliculas');
        $menuParams = new JRegistry;

        if ($menu = $app->getMenu()->getActive()) {
            $menuParams->loadString($menu->params);
        }

        $mergedParams = clone $menuParams;
        $mergedParams->merge($params);

        $this->setState('params', $mergedParams);
        $params = $mergedParams;
        $itemid = $app->input->get('Itemid', 0, 'int');

        $limit = 0;

        $orderCol = $app->getUserStateFromRequest( $this->context.'.list.' . $itemid . '.filter_order', 'filter_order', 'a.ordering', 'string');
        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = 'a.ordering';
        }

        $listOrder = $app->getUserStateFromRequest( $this->context.'.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', 'ASC', 'cmd');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
            $listOrder = 'ASC';
        }

        // Optional filter text
        $this->setState('list.filter', $app->input->getString('filter-search'));

        $this->setState('filter_movieid', $app->input->get('filter_movieid', 0, 'uint'));

        $this->setState('list.limit', $limit);
        $this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
        $this->setState('list.ordering', $orderCol);
        $this->setState('list.direction', $listOrder);
        $this->setState('filter.language', JLanguageMultilang::isEnabled());
        $this->setState('layout', $app->input->getString('layout'));
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     */
    protected function getListQuery()
    {
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());

        $params = JComponentHelper::getParams('com_peliculas');

        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $published = $this->getState('filter.published', 1);

//        $query->select('a.*');
//        $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug ');
//
//        $query->from($db->quoteName('#__peliculas_events') . ' AS a');


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


        $query->where('a.published = ' . (int)$published);

        // Define null and now dates
        $nullDate = $db->quote($db->getNullDate());
        $nowDate  = $db->quote(JFactory::getDate()->toSql());

        // Filter by start and end dates.
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
            ->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

        $search = $this->getState('list.filter');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(a.name LIKE ' . $search . ')');
        }

        // echo "model movie id:".$this->getState('filter_movieid')."<br/>";
        $movieid = (int)$this->getState('filter_movieid');

        if ($movieid>=1) {
            $query->where('(er.movie_id = ' . $movieid . ')');
        }

        // $query->group('er.id, er.cinema_id, er.room_id, er.movie_id, a.id, a.name, a.published');

        $orderCol  = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if (strtolower($orderCol)=='a.name' || strtolower($orderCol)=='name') {
            $orderCol = 'a.name';
        }

        $query->order($db->escape($orderCol.' '.$orderDirn));

        // echo nl2br(str_replace('#__','jos_',$query)) . "<br/>";

        return $query;
    }
}