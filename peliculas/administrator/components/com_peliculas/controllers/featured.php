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

require_once __DIR__ . '/movies.php';

/**
 * Movies list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasControllerFeatured extends PeliculasControllerMovies
{
	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();
		$ids  = $this->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id) {
			if (!$user->authorise('core.delete', 'com_peliculas.movie.' . (int) $id)) {
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Remove the items.
			if (!$model->featured($ids, 0)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_peliculas&view=featured');
	}

	/**
	 * Method to publish a list of articles.
	 *
	 * @return  void
	 */
	public function publish()
	{
		parent::publish();
		$this->setRedirect('index.php?option=com_peliculas&view=featured');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Feature', $prefix = 'PeliculasModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}