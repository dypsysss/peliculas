<?php
/**
 * Created by PhpStorm.
 * User: carless
 * Date: 5/02/16
 * Time: 19:28
 */

class Genre
{

    //------------------------------------------------------------------------------
    // Class Variables
    //------------------------------------------------------------------------------

    private $_data;

    /**
     * 	Construct Class
     *
     * 	@param array $data An array with the data of the Genre
     */
    public function __construct($data) {
        $this->_data = $data;
    }

    //------------------------------------------------------------------------------
    // Get Variables
    //------------------------------------------------------------------------------

    /**
     *  Get the Genre's name
     *
     *  @return string
     */
    public function getName() {
        return $this->_data['name'];
    }

    /**
     *  Get the Genre's id
     *
     *  @return int
     */
    public function getID() {
        return $this->_data['id'];
    }

    //------------------------------------------------------------------------------
    // Export
    //------------------------------------------------------------------------------

    /**
     *  Get the JSON representation of the Genre
     *
     *  @return string
     */
    public function getJSON() {
        return json_encode($this->_data, JSON_PRETTY_PRINT);
    }
}