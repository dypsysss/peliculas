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

class PeliculasControllerMovies extends JControllerAdmin
{
    /**
     * @var     string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_PELICULAS_MOVIES';

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        if ($this->input->get('view') == 'featured')
        {
            $this->view_list = 'featured';
        }

        $this->registerTask('unfeatured', 'featured');
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
    public function getModel($name = 'Movie', $prefix = 'PeliculasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Method to toggle the featured setting of a list of articles.
     *
     * @return  void
     */
    public function featured()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $user   = JFactory::getUser();
        $ids    = $this->input->get('cid', array(), 'array');
        $values = array('featured' => 1, 'unfeatured' => 0);
        $task   = $this->getTask();
        $value  = JArrayHelper::getValue($values, $task, 0, 'int');

        // Access checks.
        foreach ($ids as $i => $id) {
            if (!$user->authorise('core.edit.state', 'com_peliculas.movie.' . (int) $id)) {
                // Prune items that you can't change.
                unset($ids[$i]);
                JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
            }
        }

        if (empty($ids)) {
            JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Publish the items.
            if (!$model->featured($ids, $value)) {
                JError::raiseWarning(500, $model->getError());
            }

            if ($value == 1) {
                $message = JText::plural('COM_PELICULAS_MOVIES_N_ITEMS_FEATURED', count($ids));
            } else {
                $message = JText::plural('COM_PELICULAS_MOVIES_N_ITEMS_UNFEATURED', count($ids));
            }
        }

        $view = $this->input->get('view', '');

        if ($view == 'featured') {
            $this->setRedirect(JRoute::_('index.php?option=com_peliculas&view=featured', false), $message);
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_peliculas&view=movies', false), $message);
        }
    }
}
