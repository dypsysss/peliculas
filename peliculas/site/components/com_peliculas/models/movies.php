<?php
/**
 * @version     16/05/15 18:31
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasModelMovies extends JModelList 
{

    /**
     * Model context string.
     *
     * @var  string
     */
    protected $context = 'com_peliculas.movies';

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
                'featured', 'a.featured',
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
        $menuParams = new Registry;

        if ($menu = $app->getMenu()->getActive()) {
            $menuParams->loadString($menu->params);
        }

        $mergedParams = clone $menuParams;
        $mergedParams->merge($params);

        $this->setState('params', $mergedParams);
        $params = $mergedParams;
        $itemid = $app->input->get('Itemid', 0, 'int');

        // List state information
        $limit = $app->getUserStateFromRequest( $this->context.'.list' . $itemid . '.limit', 'limit', $params->get('display_num'));

        $orderPrimary  = $params->def('orderby_pri', 'rdate');

        $orderCol = $app->getUserStateFromRequest( $this->context.'.list.' . $itemid . '.filter_order', 'filter_order', $orderPrimary, 'string');
        if (!in_array($orderCol, $this->filter_fields)) {
            // $orderCol = 'a.modified_time';
            $orderCol = $orderPrimary;
        }

        $listOrder = $app->getUserStateFromRequest( $this->context.'.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', 'DESC', 'cmd');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
            $listOrder = 'DESC';
        }


        // Optional filter text
        $this->setState('list.filter', $app->input->getString('filter-search'));
        
        $this->setState('list.limit', $limit);
        $this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
        $this->setState('list.ordering', $orderCol);
        $this->setState('list.direction', $listOrder);
        $this->setState('filter.language', JLanguageMultilang::isEnabled());
        $this->setState('layout', $app->input->getString('layout'));
        $this->setState('style', $app->input->getString('style'));
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
        $db         = $this->getDbo();
        $query      = $db->getQuery(true);
        // Define null and now dates
        $nullDate   = $db->quote($db->getNullDate());
        $nowDate    = $db->quote(JFactory::getDate()->toSql());

        $published = $this->getState('filter.published', 1);

        $query->select( 'a.*' );
        $query->select( 'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug ');

        $query->from($db->quoteName('#__peliculas_movies') . ' AS a');

        $query->where('a.published = ' . (int)$published);

        // Filter by search in title
        $search = $this->getState('list.filter');
        if (!empty($search))
        {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(a.name LIKE ' . $search . ')');
        }
        
        $query->select('GROUP_CONCAT(g.id,":",g.name SEPARATOR "|") AS gendersnames');
        $query->join('LEFT', '#__peliculas_movies_gender AS mgp ON mgp.movie_id = a.id')
            ->join('LEFT', '#__peliculas_genders AS g ON g.id = mgp.gender_id');

        $query->group('a.id, a.name, a.published');

        // Filter by featured state
        $featured = $this->getState('filter.featured');

        switch ($featured)
        {
            case 'hide':
                $query->where('a.featured = 0');
                break;

            case 'only':
                $query->where('a.featured = 1');
                break;

            case 'show':
            default:
                // Normally we do not discriminate
                // between featured/unfeatured items.
                break;
        }

        $orderCol  = $this->state->get('list.ordering', 'a.modified_time');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        if (strtolower($orderCol)=='a.name' || strtolower($orderCol)=='name') {
            $orderCol = 'a.name';
        }

        switch (strtolower($orderCol))
        {
            case 'festreno' :
                $orderCol   = 'a.f_estreno';
                $orderDirn  = 'ASC';

                $query->where('(a.f_estreno != ' . $nullDate . ' AND a.f_estreno > ' . $nowDate . ')');

                break;

            case 'rfestreno' :
                $orderCol   = 'a.f_estreno';
                $orderDirn  = 'ASC';

                $query->where('(a.f_estreno != ' . $nullDate . ' AND a.f_estreno > ' . $nowDate . ')');

                break;

            case 'date' :
                $orderCol   = 'a.created_time';
                $orderDirn  = 'ASC';
                break;

            case 'rdate' :
                $orderCol   = 'a.created_time';
                $orderDirn  = 'DESC';
                break;

            case 'hits' :
                $orderCol   = 'a.hits';
                $orderDirn  = 'DESC';
                break;

            case 'rhits' :
                $orderCol   = 'a.hits';
                $orderDirn  = 'ASC';
                break;

            case 'order' :
                $orderCol   = 'a.ordering';
                $orderDirn  = 'ASC';
                break;
        }

        $query->order($db->escape($orderCol.' '.$orderDirn));

        // echo nl2br(str_replace('#__','jos_',$query)) . "<br/>";

        return $query;
    }
}