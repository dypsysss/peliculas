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

JLoader::register('GenderHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/gender.php');
JLoader::register('CompanyHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/company.php');
JLoader::register('PersonsHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/persons.php');
JLoader::register('MovieHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/movie.php');

/**
 * Gender list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasControllerMovie extends JControllerForm
{
    /**
     * @var     string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_PELICULAS_MOVIE';

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  12.2
     */
    protected $view_list = 'movies';

    protected $_TMDB = null;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('applytmdb', 'save');

        if ($this->input->get('return') == 'featured')
        {
            $this->view_list = 'featured';
            $this->view_item = 'movie&return=featured';
        }
    }

    /**
     * Method to save a record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   12.2
     */
    public function save($key = null, $urlVar = null)
    {
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
            $tmdbMovie = $this->getTMDB()->getMovie($data['themoviedb_id']);
            $tmdbMovie->setAPI($this->getTMDB());

            $data['name']        = $tmdbMovie->getTitle();
            $data['alias']       = '';
            $data['description'] = $tmdbMovie->get("overview");

            $companies = $tmdbMovie->get("production_companies");
            $tmpCompaniesIDS = "";
            if (count($companies)) {
                foreach($companies as $company) {
                    $idCompany = $company['id'];
                    $tblCompany = CompanyHelper::getCompanyByTmdbID($idCompany);
                    $tmpCompaniesIDS .= $tblCompany->id.",";
                }
            }

            $amultigenders = array();
            $aGeneros = $tmdbMovie->get("genres");
            $tmpGenerosNames = "";
            if (count($aGeneros)>=1) {
                foreach ($aGeneros as $iGen) {
                    $iGenero = GenderHelper::getGenderByTmdbID($iGen['id']);
                    $amultigenders[] = $iGenero->id;
                    $tmpGenerosNames = $iGenero->name;
                }
            }

            $tmpPaises = "";
            $countries = $tmdbMovie->get("production_countries");
            if (count($countries)) {
                foreach ($countries as $country) {
                    $tmpPaises .= $country['iso_3166_1'] . " ,";
                }
                if (strlen($tmpPaises)>=1) {
                    $tmpPaises = substr($tmpPaises, 0, -1);
                }
            }

            $data['multigenders']       = $amultigenders;

            $data['production_companies'] = $tmpCompaniesIDS;
            $data['production_countries'] = $tmpPaises;
            $data['imdb_id']            = $tmdbMovie->get("imdb_id");
            $data['release_date']       = $tmdbMovie->get("release_date");
            $data['homepage']           = $tmdbMovie->get("homepage");
            $data['original_title']     = $tmdbMovie->get("original_title");
            $data['original_language']  = $tmdbMovie->get("original_language");
            if ((int)$tmdbMovie->get("runtime")>=1) {
                $data['duracion'] = (int)$tmdbMovie->get("runtime");
            }

            $festreno = null;
            if (!empty($tmdbMovie->get("release_date"))) {
                $ymd = DateTime::createFromFormat('Y-m-d', $tmdbMovie->get("release_date"))->format('Y-m-d');
                $festreno = new JDate($ymd);
                $data['f_estreno'] = $festreno->format('d-m-Y');
            }

            $castings = $tmdbMovie->getCasting();

            $tmpInterpretes = "";
            $tmpInterpretesNames = "";
            if (count($castings)) {
                $maxCount = 15;
                $nCont = 0;
                foreach($castings as $casting) {
                    if ($nCont<=$maxCount) {
                        $idperson = $casting['id'];
                        $tblPerson = PersonsHelper::getPersonByTmdbID($idperson);
                        $tmpInterpretes .= $tblPerson->id . ",";
                        $tmpInterpretesNames .= $tblPerson->name ." ,";
                    }
                    $nCont++;
                }

                if (strlen($tmpInterpretesNames)>=1) {
                    $tmpInterpretesNames = substr($tmpInterpretesNames, 0, -1);
                }
            }
            $data['interpretes']    = $tmpInterpretes;

            $crews = $tmdbMovie->getCrew();

            $tmpProductores = "";
            $tmpDirectores = "";
            $tmpGuionistas = "";

            if (count($crews)) {
                foreach($crews as $crew) {
                    $idperson = $crew['id'];
                    $job = $crew['job'];

                    switch (strtolower($job)) {
                        case 'director':
                            $tblPerson = PersonsHelper::getPersonByTmdbID($idperson);
                            $tmpDirectores .= $tblPerson->id . ",";
                            break;

                        case 'producer':
                            $tblPerson = PersonsHelper::getPersonByTmdbID($idperson);
                            $tmpProductores .= $tblPerson->id . ",";
                            break;

                        case 'screenplay':
                            $tblPerson = PersonsHelper::getPersonByTmdbID($idperson);
                            $tmpGuionistas .= $tblPerson->id . ",";
                            break;
                    }
                }
            }

            $data['productores']    = $tmpProductores;
            $data['directores']     = $tmpDirectores;
            $data['guion']          = $tmpGuionistas;

            if (!empty($tmdbMovie->get("poster_path"))) {
                $data['poster_path']    = $this->getTMDB()->getImageURL("original");
                $data['poster_image']   = $tmdbMovie->get("poster_path");

                if (substr($data['poster_image'],0,1) == "/" ) {
                    $newImage = substr($data['poster_image'], 1);
                    $data['poster_image'] = $newImage;
                }
            }

            $ibackdrops = $tmdbMovie->getBackdrops();
            $data['imagenes'] = json_encode(array());

            // [
            //  {
            //  "name":"57920201aa883_1469186561.jpg",
            //  "title":"",
            //  "description":""
            //  },{"name":"579202b043994_1469186736.jpg","title":"","description":""}]

            if (count($ibackdrops)) {
                $images = array();
                foreach($ibackdrops as $iback) {
                    $tmpImg = new stdClass();

                    if (substr($iback['file_path'],0,1) == "/" ) {
                        $tmpImg->name = substr($iback['file_path'], 1);
                    } else {
                        $tmpImg->name = $iback['file_path'];
                    }
                    $tmpImg->title = "";
                    $tmpImg->description = "";
                    $tmpImg->url = $this->getTMDB()->getImageURL("original") . $iback['file_path'];
                    $images[] = $tmpImg;

                    // MovieHelper::donwloadImage($this->getTMDB()->getImageURL("original"), $tmpImg->name, 'backdrops');
                }

                $data['imagenes'] = $images;
            }

            $itrailers = $tmdbMovie->getVideos();

            if (count($itrailers)) {
                $videos = array();
                foreach ($itrailers as $itrailer) {
                    $tmpVideo = new stdClass();
                    $tmpVideo->site     = strtolower($itrailer['site']);
                    $tmpVideo->vkey     = $itrailer['key'];
                    $tmpVideo->vname    = $itrailer['name'];

                    $videos[] = $tmpVideo;
                }

                $data['videos'] = $videos;
            }

            $keyboards = $tmdbMovie->getKewords();
            if (count($keyboards)) {
                $data['meta_keyword'] = "";
                foreach ($keyboards as $keyboard) {
                    $data['meta_keyword'] .= $keyboard['name'] . ",";
                }

                if (strlen($data['meta_keyword'])>=1) {
                    $data['meta_keyword'] = substr($data['meta_keyword'], 0, -1);
                }
            }

            $tmpMetaDesc = $data['name'];
            if ($festreno!=null) {
                $tmpMetaDesc .= " (";
                $tmpMetaDesc .= $festreno->year;
                $tmpMetaDesc .= ")";
            }
            $tmpMetaDesc .= " : Película de " . $tmpGenerosNames;
            $tmpMetaDesc .= " con " . $tmpInterpretesNames;

            $data['meta_description']   = $tmpMetaDesc;
            $data['meta_title']         = $tmdbMovie->getTitle();

            // Ahora me ves 2 (USA, 2016): Película de magia con Jesse Eisenberg, Mark Ruffalo, Woody Harrelson, Dave Franco, Daniel Radcliffe, Lizzy Caplan
//            echo "meta_keyword<br/><hr/>";
//            echo var_dump($data['meta_keyword'])."<hr/>";
//            echo "tmdbMovie keyboards<br/><hr/>";
//            echo var_dump($keyboards)."<hr/>";
//            die();

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