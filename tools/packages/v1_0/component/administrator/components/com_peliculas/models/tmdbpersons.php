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
JLoader::register('TMDB', JPATH_COMPONENT_ADMINISTRATOR . '/libs/tmdb-api.php');
/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasModelTmdbPersons extends PeliculasModelBaseList
{
    protected $_TMDB = null;
    protected $page = null;
    
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
                'name', 'a.name'
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

        $start = $this->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'uint');
        $this->setState('limitstart', $start);
        
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

        return parent::getStoreId($id);
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   12.2
     */
    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $limit = 20;
            $start = $this->getState('limitstart');
            $page = ceil($start / $limit) + 1;

            $result = $this->getTMDB()->_call('search/person', 'query='. urlencode($search) . '&page=' . $page, $this->getTMDB()->getLang());

            $items = array();
            if ($result['results']) {
                foreach ($result['results'] as $data) {
                    $items[] = new Person($data);
                }
            }

            $page = $result['page'];
            // $total_pages = $result['total_pages'];
            $total_results = $result['total_results'];

            $this->page = new JPagination($total_results, ($page-1)*$limit, $limit);
            
            // $items = $this->getTMDB()->searchPerson($search);
        } else {
            $items = null;
        }

        // Add the items to the internal cache.
        $this->cache[$store] = $items;

        return $this->cache[$store];
    }

    public function getPaginacion() {
        if ($this->page==null) {
            return new JPagination(0, 0, 0);
        } else {
            return $this->page;
        }
    }
    
    public function getTMDB() {

        if ($this->_TMDB == null) {
            $paramsCfg = JComponentHelper::getParams('com_peliculas');
            $apikey = $paramsCfg->get('themoviedb_api_key');

            $this->_TMDB = new TMDB($apikey, 'es', false);
        }

        return $this->_TMDB;
    }
}