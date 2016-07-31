<?php
/**
 * @version     13/04/15 12:33
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('PeliculasModelBaseList', JPATH_COMPONENT_ADMINISTRATOR . '/models/_baselist.php');

/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasModelGenders extends PeliculasModelBaseList
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'ordering', 'a.ordering',
                'published', 'a.published',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'created_time', 'a.created_time',
                'created_user_id', 'a.created_user_id'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $state);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $this->setState('filter.access', $access);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_peliculas');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.name', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string  A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();

        $params = JComponentHelper::getParams('com_peliculas');

        // Select the required fields from the table.
        $query->select(
            'a.id, a.name, a.alias, '.
            'a.published, a.access, ' .
            'a.ordering AS ordering, ' .
            'a.checked_out, a.checked_out_time, a.created_user_id'
        );

        $query->from($db->quoteName('#__peliculas_genders') . ' AS a');

        // Join over the asset groups.
        $query->select('ag.title AS access_level')
            ->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the users for the author.
        $query->select('ua.name AS author_name')
            ->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

        // Filter by access level.
        if ($access = $this->getState('filter.access'))
        {
            $query->where('a.access = ' . (int) $access);
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.published IN (0, 1))');
        }

        $query->group('a.id, a.name, a.checked_out, a.checked_out_time, a.published, uc.name');

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } elseif (stripos($search, 'author:') === 0) {
                $search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
                $query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('a.name LIKE ' . $search );
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if (strtolower($orderCol)=='a.name' || strtolower($orderCol)=='name') {
            $orderCol = 'a.name';
        }

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        // echo nl2br(str_replace('#__','jos_',$query));

        return $query;
    }
}