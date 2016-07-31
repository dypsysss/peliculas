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

jimport('joomla.application.component.modeladmin');

/**
 * Class Base List of elements records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
abstract class PeliculasModelBaseForm extends JModelAdmin
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see     JController
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object $record A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            if ($record->published != -2) {
                return;
            }

            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_peliculas');
        }
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object $record A record object.
     *
     * @return  boolean  True if allowed to change the state of the record.
     *                   Defaults to the permission set in the component.
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();
        return $user->authorise('core.edit.state', 'com_peliculas');
    }

    /**
     * Method to get a single record.
     *
     * @param   integer     The id of the primary key.
     *
     * @return  mixed       Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($result = parent::getItem($pk)) {
            // Convert the created and modified dates to local user time for display in the form.
            $tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));

            if ((int)$result->created_time) {
                $date = new JDate($result->created_time);
                $date->setTimezone($tz);
                $result->created_time = $date->toSql(true);
            } else {
                $result->created_time = null;
            }

            if ((int)$result->modified_time) {
                $date = new JDate($result->modified_time);
                $date->setTimezone($tz);
                $result->modified_time = $date->toSql(true);
            } else {
                $result->modified_time = null;
            }
        }

        return $result;
    }
}