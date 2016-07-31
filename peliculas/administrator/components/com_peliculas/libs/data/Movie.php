<?php
/**
 * 	This class handles all the data you can get from a Movie
 *
 * 	@author Alvaro Octal | <a href="https://twitter.com/Alvaro_Octal">Twitter</a>
 * 	@version 0.1
 * 	@date 09/01/2015
 * 	@link https://github.com/Alvaroctal/TMDB-PHP-API
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */

class Movie{

	//------------------------------------------------------------------------------
	// Class Variables
	//------------------------------------------------------------------------------

	private $_data;
	private $_tmdb;

	/**
	 * 	Construct Class
	 *
	 * 	@param array $data An array with the data of the Movie
	 */
	public function __construct($data) {
		$this->_data = $data;
	}

	//------------------------------------------------------------------------------
	// Get Variables
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the Movie's id
	 *
	 * 	@return int
	 */
	public function getID() {
		return $this->_data['id'];
	}

	/** 
	 * 	Get the Movie's title
	 *
	 * 	@return string
	 */
	public function getTitle() {
		return $this->_data['title'];
	}

	/**
	 * 	Get the Movie's title
	 *
	 * 	@return string
	 */
	public function getOriginalTitle() {
		return $this->_data['original_title'];
	}

	/** 
	 * 	Get the Movie's tagline
	 *
	 * 	@return string
	 */
	public function getTagline() {
		return $this->_data['tagline'];
	}

	/** 
	 * 	Get the Movie's Poster
	 *
	 * 	@return string
	 */
	public function getPoster() {
		return $this->_data['poster_path'];
	}

	/** 
	 * 	Get the Movie's vote average
	 *
	 * 	@return int
	 */
	public function getVoteAverage() {
		return $this->_data['vote_average'];
	}

	/** 
	 * 	Get the Movie's vote count
	 *
	 * 	@return int
	 */
	public function getVoteCount() {
		return $this->_data['vote_count'];
	}

    public function getKewords() {

        if (empty($this->_data['keywords']) && isset($this->_tmdb)){
            $this->loadKeywords();
        }

        if (isset($this->_data['keywords']['keywords'])) {
            return $this->_data['keywords']['keywords'];
        } else {
            return false;
        }
    }

    public function getVideos() {

        if (empty($this->_data['videos']) && isset($this->_tmdb)){
            $this->loadVideos();
        }

        if (isset($this->_data['videos']['results'])) {
            return $this->_data['videos']['results'];
        } else {
            return false;
        }
    }

	/** 
	 * 	Get the Movie's trailers
	 *
	 * 	@return array
	 */
	public function getTrailers() {

		if (empty($this->_data['trailers']) && isset($this->_tmdb)){
			$this->loadTrailer();
		}

		return $this->_data['trailers'];
	}

	/** 
	 * 	Get the Movie's trailer
	 *
	 * 	@return string
	 */
	public function getTrailer() {
		$trailers = $this->getTrailers();
		return $trailers['youtube'][0]['source'];
	}

	/**
	 * 	Get the Movie's credits
	 *
	 * 	@return array
	 */
	public function getCredits() {
		if (empty($this->_data['credits']) && isset($this->_tmdb)){
			$this->loadCredits();
		}
		return $this->_data['credits'];
	}

	/**
	 * 	Get the Movie's Casting
	 *
	 * 	@return array
	 */
	public function getCasting() {
		if (empty($this->_data['credits']) && isset($this->_tmdb)){
			$this->loadCredits();
		}

		if (isset($this->_data['credits']['cast'])) {
			return $this->_data['credits']['cast'];
		} else {
			return false;
		}
	}

	public function getCrew() {
		if (empty($this->_data['credits']) && isset($this->_tmdb)){
			$this->loadCredits();
		}

		if (isset($this->_data['credits']['crew'])) {
			return $this->_data['credits']['crew'];
		} else {
			return false;
		}
	}

    public function getBackdrops() {
        if (empty($this->_data['imagenes']) && isset($this->_tmdb)){
            $this->loadImages();
        }

        if (isset($this->_data['imagenes']['backdrops'])) {
            return $this->_data['imagenes']['backdrops'];
        } else {
            return false;
        }
    }

	/**
	 *  Get Generic.<br>
	 *  Get a item of the array, you should not get used to use this, better use specific get's.
	 *
	 * 	@param string $item The item of the $data array you want
	 * 	@return array
	 */
	public function get($item = ''){
		return (empty($item)) ? $this->_data : $this->_data[$item];
	}

	//------------------------------------------------------------------------------
	// Load Variables
	//------------------------------------------------------------------------------

	/**
	 * 	Load the images of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadImages(){
		$this->_data['imagenes'] = $this->_tmdb->getMovieInfo($this->getID(), 'images', false, false);
	}

	/**
	 * 	Load the trailer of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTrailer() {
		$this->_data['trailers'] = $this->_tmdb->getMovieInfo($this->getID(), 'trailers', false);
	}

    public function loadVideos() {
        $this->_data['videos'] = $this->_tmdb->getMovieInfo($this->getID(), 'videos', false);
    }

	/**
	 * 	Load the casting of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadCasting(){
		$this->_data['casts'] = $this->_tmdb->getMovieInfo($this->getID(), 'casts', false);
	}

	public function loadCredits(){
		$this->_data['credits'] = $this->_tmdb->getMovieInfo($this->getID(), 'credits', false);
	}

    public function loadKeywords(){
        $this->_data['keywords'] = $this->_tmdb->getMovieInfo($this->getID(), 'keywords', false, false);
    }

	/**
	 * 	Load the translations of the Movie
	 *	Used in a Lazy load technique
	 */
	public function loadTranslations(){
		$this->_data['translations'] = $this->_tmdb->getMovieInfo($this->getID(), 'translations', false);
	}

	//------------------------------------------------------------------------------
	// Import an API instance
	//------------------------------------------------------------------------------

	/**
	 *	Set an instance of the API
	 *
	 *	@param TMDB $tmdb An instance of the api, necessary for the lazy load
	 */
	public function setAPI($tmdb){
		$this->_tmdb = $tmdb;
	}

	//------------------------------------------------------------------------------
	// Export
	//------------------------------------------------------------------------------

	/** 
	 * 	Get the JSON representation of the Movie
	 *
	 * 	@return string
	 */
	public function getJSON() {
		return json_encode($this->_data, JSON_PRETTY_PRINT);
	}
}
?>