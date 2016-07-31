<?php
/**
 * @version     7/05/16 20:23
 * @package     Peliculas J3x
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for Multiselect servicios.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jpeliculas
 * @see         JFormField
 */
class JFormFieldSelectPersons extends JFormFieldList
{
    /**
     * A flexible tag list that respects access controls
     *
     * @var    string
     */
    public $type = 'SelectPersons';

    /**
     * com_jpeliculas parameters
     *
     * @var    JRegistry
     */
    protected $comParams = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Load com_tags config
        $this->comParams = JComponentHelper::getParams('com_peliculas');
    }

    /**
     * Method to get the field input for a SelectCompanies field.
     *
     * @return  string  The field input.
     */
    protected function getInput()
    {

        if (!is_array($this->value) && !empty($this->value))
        {
            // String in format 2,5,4
            if (is_string($this->value))
            {
                $this->value = explode(',', $this->value);
            }
        }

        $input = parent::getInput();

        return $input;
    }

    /**
     * Method to get a list of tags
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);

        $query->select('a.id AS value, a.name AS text');
        $query->from('#__peliculas_persons AS a');

        // Get the options.
        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            return false;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}