<?php
/**
 * @version     21/05/15 18:39
 * @package     JInmo_j3
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

defined('_JEXEC') or die;

/**
 * Content HTML helper
 *
 * @since  3.0
 */
abstract class JHtmlFeatured
{
	/**
	 * Show the feature/unfeature links
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          Row number
	 * @param   boolean  $canChange  Is user allowed to change?
	 *
	 * @return  string       HTML code
	 */
	public static function featured($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action
		$states	= array(
			0	=> array('unfeatured',	'movies.featured', 	'COM_PELICULAS_UNFEATURED',	'JGLOBAL_TOGGLE_FEATURED'),
			1	=> array('featured',	'movies.unfeatured','COM_PELICULAS_FEATURED',	'JGLOBAL_TOGGLE_FEATURED'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];

		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-'
				. $icon . '"></i></a>';
		} else {
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-'
				. $icon . '"></i></a>';
		}

		return $html;
	}
}