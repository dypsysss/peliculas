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


jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;

/**
 * Methods supporting a list of elements records.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasModelMovie extends JModelItem
{
    /**
     * The name of the view for a single item
     */
    protected $view_item = 'movie';

    /**
     * A loaded item
     */
    protected $_item = null;

    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context = 'com_peliculas.movie';

    public function getTable($type = 'Movies', $prefix = 'PeliculasTable', $config = array())
    {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('com_peliculas');

        $id = JFactory::getApplication()->input->get('id');
        JFactory::getApplication()->setUserState('com_peliculas.pelicula.id', $id);
        
        $this->setState('movie.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if(isset($params_array['item_id'])){
            $this->setState('movie.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Gets a element
     *
     * @param integer $pk  Id for the contact
     *
     * @return mixed Object or null
     */
    public function &getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState('movie.id');

        if ($this->_item === null) {
            $this->_item = array();
        }

        if (!isset($this->_item[$pk])) {
            $this->_item[$pk] = false;

            try {
                $db = $this->getDbo();
                $query = $db->getQuery(true);
                $query->select('a.*');

                $query->from($db->quoteName('#__peliculas_movies') . ' AS a');

                $query->select('GROUP_CONCAT(g.id,":",g.name SEPARATOR "|") AS gendersnames');
                $query->join('LEFT', '#__peliculas_movies_gender AS mgp ON mgp.movie_id = a.id')
                    ->join('LEFT', '#__peliculas_genders AS g ON g.id = mgp.gender_id');

                $query->where('a.id = ' . (int) $pk);

                $db->setQuery($query);

                $data = $db->loadObject();

                if (empty($data)) {
                    throw new Exception(JText::_('COM_PELICULAS_ERROR_MOVIE_NOT_FOUND'), 404);
                    // return JError::raiseError(404, JText::_('COM_PELICULAS_ERROR_MOVIE_NOT_FOUND'));
                }
                $this->_item[$pk] = $data;

            } catch (Exception $e) {
                $this->setError($e);
                $this->_item[$pk] = false;
            }
        }

        return $this->_item[$pk];
    }

    /**
     * Increment the hit counter for the element.
     *
     * @param   int  $pk  Optional primary key of the item to increment.
     *
     * @return  boolean  True if successful; false otherwise and internal error set.
     */
    public function hit($pk = 0)
    {
        $input = JFactory::getApplication()->input;
        $hitcount = $input->getInt('hitcount', 1);

        if ($hitcount)
        {
            $pk = (!empty($pk)) ? $pk : (int) $this->getState('movie.id');

            $table = $this->getTable();
            $table->load($pk);
            $table->hit($pk);
        }

        return true;
    }
}