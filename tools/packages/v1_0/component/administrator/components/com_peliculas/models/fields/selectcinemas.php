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


JLoader::register('HelperCinemas', JPATH_ADMINISTRATOR . '/components/com_peliculas/helpers/cinemas.php');

/**
 * Form Field class for Multiselect servicios.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jpeliculas
 * @see         JFormField
 */
class JFormFieldSelectCinemas extends JFormFieldList
{
    /**
     * A flexible tag list that respects access controls
     *
     * @var    string
     */
    public $type = 'SelectCinemas';

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
     * Method to get a list of tags
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), HelperCinemas::getCinemaOptions());
    }
}