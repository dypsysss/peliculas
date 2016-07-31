<?php
/**
 * @version     22/05/15 11:44
 * @package     JInmo_j3
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('PeliculasModelMovies', JPATH_COMPONENT_ADMINISTRATOR . '/models/movies.php');

/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasModelFeatured extends PeliculasModelMovies
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
				'ordering', 'a.ordering', 'fp.ordering',
				'featured', 'a.featured',
				'hits', 'a.hits',
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
			'a.id, a.name, a.alias, a.poster, ' .
			'a.release_date, a.original_title, ' .
			'a.published, a.access, a.featured, ' .
			'a.hits, a.ordering AS ordering, ' .
			'a.checked_out, a.checked_out_time, a.created_user_id'
		);

		$query->from($db->quoteName('#__peliculas_movies') . ' AS a');

		// Join over the featured table.
		$query->select('fp.ordering')
			->join('INNER', '#__peliculas_featured AS fp ON fp.movie_id = a.id');

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
		$orderCol = $this->state->get('list.ordering', 'fp.ordering');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if (strtolower($orderCol)=='a.name' || strtolower($orderCol)=='name') {
			$orderCol = 'a.name';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		// echo "start:".$this->getStart()."<br/>";
		// echo "limit:".$this->getState('list.limit')."<br/>";
		// echo "order:".$orderCol." state:" . $this->state->get('list.ordering', 'fp.ordering') . "<br/><br/><br/>";
		// echo nl2br(str_replace('#__','jos_',$query));

		return $query;
	}
}