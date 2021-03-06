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

JLoader::register('TMDB', JPATH_COMPONENT_ADMINISTRATOR . '/libs/tmdb-api.php');

/**
 * Gender list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasControllerPerson extends JControllerForm
{
    /**
     * @var     string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_PELICULAS_PERSON';

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  12.2
     */
    protected $view_list = 'persons';

    protected $_TMDB = null;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('applytmdb', 'save');
    }

    public function save($key = null, $urlVar = null) {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        $lang   = JFactory::getLanguage();
        $model  = $this->getModel();
        $table  = $model->getTable();
        $data   = $this->input->post->get('jform', array(), 'array');
        $task   = $this->getTask();

        if ($task == 'applytmdb')
        {
            $iPerson = $this->getTMDB()->getPerson($data['themoviedb_id']);

            $data['name']           = $iPerson->getName();
            $data['biography']      = $iPerson->get("biography");
            $data['birthday']       = $iPerson->get("birthday");
            $data['deathday']       = $iPerson->get("deathday");
            $data['homepage']       = $iPerson->get("homepage");
            $data['place_of_birth'] = $iPerson->get("place_of_birth");

            $profile_path = $iPerson->get("profile_path");
            if (!empty($profile_path)) {
                $data['poster_path'] = $this->getTMDB()->getImageURL("original");
                $data['poster_image'] = $iPerson->get("profile_path");
                if (substr($data['poster_image'], 0, 1) == "/") {
                    $newImage = substr($data['poster_image'], 1);
                    $data['poster_image'] = $newImage;
                }
            }
            
            $this->input->post->set('jform', $data);

            $this->task = 'apply';
        }

        return parent::save($key,$urlVar);
    }


    public function getTMDB() {

        if ($this->_TMDB == null) {
            $paramsCfg = JComponentHelper::getParams('com_peliculas');
            $apikey = $paramsCfg->get('themoviedb_api_key');

            $this->_TMDB = new TMDB($apikey, 'es', false);
        }

        return $this->_TMDB;
    }

}