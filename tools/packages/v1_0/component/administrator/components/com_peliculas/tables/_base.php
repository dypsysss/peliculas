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

use Joomla\Utilities\ArrayHelper;

/**
 * Element table
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
abstract class PeliculasTableBase extends JTable
{
    /**
     * Object constructor to set table and key fields.  In most cases this will
     * be overridden by child classes to explicitly set the table and key fields
     * for a particular database table.
     *
     * @param   string           $table  Name of the table to model.
     * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.
     * @param   JDatabaseDriver  $db     JDatabaseDriver object.
     */
    public function __construct($table, $key, $db)
    {
        parent::__construct($table, $key, $db);

        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $this->checked_out_time = $db->getNullDate();
        $this->created_time     = $date->toSql();
        $this->created_user_id  = $user->get('id');
    }

    /**
     * Overloaded check function
     *
     * @throws UnexpectedValueException
     * @return  boolean  True on success, false on failure
     *
     * @see JTable::check
     */
    public function check()
    {
        // Check for a title.
        if ((isset($this->name)) && (trim($this->name) == '')) {
            throw new UnexpectedValueException(JText::_('COM_PELICULAS_WARNING_EMPTY_NAME'));
            return false;
        }

        // Set alias
        $this->alias = JApplicationHelper::stringURLSafe($this->alias);

        if (empty($this->alias)) {
            $this->alias = JApplicationHelper::stringURLSafe($this->name);
        }

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
        }

        // Set ordering
        if ($this->published < 0) {
            // Set ordering to 0 if state is archived or trashed
            $this->ordering = 0;
        } elseif (empty($this->ordering)) {
            // Set ordering to last if ordering was 0
            $this->ordering = self::getNextOrder('published>=0');
        }
        
        return true;
    }

    /**
     * Method to bind an associative array or object to the JTable instance.This
     * method only binds properties that are publicly accessible and optionally
     * takes an array of properties to ignore when binding.
     *
     * @param   mixed   $array  An associative array or object to bind to the JTable instance.
     * @param   mixed   $ignore An optional array or space separated list of properties to ignore while binding.
     *
     * @return      boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTable/bind
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overridden JTable::store to set created/modified and user id.
     *
     * @param   boolean $updateNulls True to update fields even if they are null.
     *
     * @throws UnexpectedValueException
     * @return  boolean  True on success.
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        if ($this->id) {
            // Existing category
            $this->modified_time    = $date->toSql();
            $this->modified_user_id = $user->get('id');
        } else {
            // New category
            $this->created_time     = $date->toSql();
            $this->created_user_id  = $user->get('id');
        }

        if ($this->checkUniqueTmdb()) {
            return parent::store($updateNulls);
        } else {
            return false;
        }
    }

    public function checkUniqueTmdb() {
        return true;    
    }
    
    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success.
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_key;

        // Sanitize input.
        $pks    = ArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            } else {
                // Nothing to set publishing state on, return false.
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE ' . $this->_db->quoteName($this->_tbl) .
            ' SET ' . $this->_db->quoteName('published') . ' = ' . (int) $state .
            ' WHERE (' . $where . ')' .
            $checkin
        );

        try {
            $this->_db->execute();
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->published = $state;
        }

        $this->setError('');

        return true;
    }
}