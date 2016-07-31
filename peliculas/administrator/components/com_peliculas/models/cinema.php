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

JLoader::register('PeliculasModelBaseForm', JPATH_COMPONENT_ADMINISTRATOR . '/models/_baseform.php');
JLoader::register('HelperCinemas', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cinemas.php');

/**
 * Methods supporting a edit elements.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 **/
class PeliculasModelCinema extends PeliculasModelBaseForm
{
    /**
     * The type alias for this content type.
     */
    public $typeAlias = 'com_peliculas.cinema';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable	A database object
     */
    public function getTable($type = 'Cinemas', $prefix = 'PeliculasTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object    $record    A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_peliculas.cinema.' . (int) $record->id);
        }
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_peliculas.cinema', 'cinema', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_peliculas.edit.cinema.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_peliculas.cinema', $data);

        return $data;
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
        if ($item = parent::getItem($pk))
        {
            $item->rooms = array();

            if (!empty($item->id))  {
                $item->helper = new HelperCinemas();
                $item->rooms = $item->helper->getRooms($item->id);
            }
        }

        return $item;
    }
    
    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $input = JFactory::getApplication()->input;

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy') {
            $data['name'] = $data['name'] . ' (Copia)';
            $data['published'] = 0;
        }

        $lreturn = parent::save($data);
        if ($lreturn) {
            $lreturnRooms = $this->processRooms($data);
        }
        return $lreturn;
    }

    public function processRooms($data)
    {
        $app = JFactory::getApplication();
        $item = $this->getItem();

        $bError = false;

        $jinput = JFactory::getApplication()->input;
        $dataRooms  = $jinput->post->get('jform', array(), 'array');

        if (isset($dataRooms['cinemaroom'])) {
            if (count($dataRooms['cinemaroom'])) {
                foreach ($dataRooms['cinemaroom'] as &$room) {
                    $room_id     = (int)$room['id'];
                    $delete      = isset($room['deleted']) ? (int)$room['deleted'] : 0;
                    $room_name   = isset($room['name']) ? $room['name'] : '';

                    unset($tblRoom);
                    $tblRoom = JTable::getInstance('cinemasRoom', 'PeliculasTable');

                    if ($room_id == -1) {
                        // Si el id es -1 ... es el comodin para dar de alta
                        if (!$delete) {
                            $tblRoom->id        = 0;
                            $tblRoom->cinema_id = $item->id;
                            $tblRoom->name      = $room_name;

                            $where = 'cinema_id = ' . (int) $item->id;

                            $tblRoom->ordering = $tblRoom->getNextOrder($where);

                            // 	Make sure the data is valid
                            if (!$tblRoom->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblRoom->getError(), 'warning');
                            }

                            if (!$tblRoom->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblRoom->getError(), 'warning');
                            }
                            $tblRoom->reorder($where);
                        }
                    } else {
                        $tblRoom->load($room_id);

                        // Es Modificacion
                        if ($delete) {
                            $tblRoom->delete($room_id);
                        } else {
                            $tblRoom->cinema_id = $item->id;
                            $tblRoom->name      = $room_name;

                            $where = 'cinema_id = ' . (int) $item->id;

                            $tblRoom->ordering = $tblRoom->getNextOrder($where);

                            // 	Make sure the data is valid
                            if (!$tblRoom->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblRoom->getError(), 'warning');
                            }

                            if (!$tblRoom->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblRoom->getError(), 'warning');
                            }
                            $tblRoom->reorder($where);
                        }
                    }
                }
            }
        }

        return !$bError;
    }
}