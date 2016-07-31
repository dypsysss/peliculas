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
JLoader::register('HelperEvents', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/events.php');

/**
 * Methods supporting a edit elements.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 **/
class PeliculasModelEvent extends PeliculasModelBaseForm
{
    /**
     * The type alias for this content type.
     */
    public $typeAlias = 'com_peliculas.event';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable	A database object
     */
    public function getTable($type = 'Events', $prefix = 'PeliculasTable', $config = array())
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
            return $user->authorise('core.delete', 'com_peliculas.event.' . (int) $record->id);
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
        $form = $this->loadForm('com_peliculas.event', 'event', array('control' => 'jform', 'load_data' => $loadData));

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
        $data = JFactory::getApplication()->getUserState('com_peliculas.edit.event.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_peliculas.event', $data);

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
                $item->helper = new HelperEvents();
                $item->erooms = $item->helper->getRooms($item->id);
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

            return $lreturn && $lreturnRooms;
        }
        return $lreturn;
    }

    public function processRooms($data) {

        $app    = JFactory::getApplication();
        $item   = $this->getItem();

        $bError = false;

        $jinput = JFactory::getApplication()->input;
        $dataMovies  = $jinput->post->get('jform', array(), 'array');

        if (isset($dataMovies['emovie'])) {
            if (count($dataMovies['emovie'])) {
                foreach ($dataMovies['emovie'] as &$emovie) {

                    $eroomID      = (int)$emovie['id'];
                    $delete       = isset($emovie['deleted']) ? (int)$emovie['deleted'] : 0;
                    $cinemaRoomID = (int)$emovie['cinemaroom_id'];
                    $movieID      = (int)$emovie['movie_id'];
                    $info         = isset($emovie['info']) ? $emovie['info'] : '';

                    unset($tblEventsRoom);
                    $tblEventsRoom = JTable::getInstance('EventsRoom', 'PeliculasTable');

                    if ($eroomID == -1) {
                        // Si el id es -1 ... es el comodin para dar de alta
                        if (!$delete) {
                            $tblEventsRoom->id          = 0;
                            $tblEventsRoom->event_id    = $item->id;
                            $tblEventsRoom->cinema_id   = $item->cinema_id;
                            $tblEventsRoom->room_id     = $cinemaRoomID;
                            $tblEventsRoom->movie_id    = $movieID;
                            $tblEventsRoom->informacion = $info;

                            $where = 'event_id = ' . (int) $item->id;
                            $tblEventsRoom->ordering = $tblEventsRoom->getNextOrder($where);

                            // 	Make sure the data is valid
                            if (!$tblEventsRoom->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblPrdAttr->getError(), 'warning');
                            }

                            if (!$tblEventsRoom->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblPrdAttr->getError(), 'warning');
                            }
                            $tblEventsRoom->reorder($where);
                        }
                    } else {
                        $tblEventsRoom->load($eroomID);

                        // Es Modificacion
                        if ($delete) {
                            $tblEventsRoom->delete($eroomID);
                        } else {
                            $tblEventsRoom->event_id    = $item->id;
                            $tblEventsRoom->cinema_id   = $item->cinema_id;
                            $tblEventsRoom->room_id     = $cinemaRoomID;
                            $tblEventsRoom->movie_id    = $movieID;
                            $tblEventsRoom->informacion = $info;

                            $where = 'event_id = ' . (int) $item->id;
                            $tblEventsRoom->ordering = $tblEventsRoom->getNextOrder($where);

                            // 	Make sure the data is valid
                            if (!$tblEventsRoom->check()) {
                                $bError = true;
                                $app->enqueueMessage($tblPrdAttr->getError(), 'warning');
                            }

                            if (!$tblEventsRoom->store()) {
                                $bError = true;
                                $app->enqueueMessage($tblPrdAttr->getError(), 'warning');
                            }
                            $tblEventsRoom->reorder($where);
                        }
                    }
                }
            }
        }

        return !$bError;
    }
}