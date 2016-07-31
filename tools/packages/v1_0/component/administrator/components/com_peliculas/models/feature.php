<?php
/**
 * @version     22/05/15 12:17
 * @package     JInmo_j3
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/movie.php';

/**
 * Feature model.
 */
class PeliculasModelFeature extends PeliculasModelMovie
{
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Featured', $prefix = 'PeliculasTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object	A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();

		return $condition;
	}
}