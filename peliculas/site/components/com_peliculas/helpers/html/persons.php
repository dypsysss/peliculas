<?php
/**
 * @version     16/05/15 18:31
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
use Joomla\Utilities\ArrayHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Peliculas Component JHTML Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
abstract class JHtmlPersons
{
    static protected $persons = null;

    /**
     * Method to get extras list values from a list of extras ids
     *
     * @param 	mixed 	$value  	string or array of extras ids
     * @param 	string 	$format   	The wanted format (ol, li, raw (default))
     *
     * @return  string  HTML for the list.
     */
    static public function bindList($value=0, $format='raw')
    {
        if (is_string($value) && !empty($value)) {
            if (substr($value, -1)==",") {
                $value = substr($value, 0, -1);
            }
            $ids = explode(',' , $value);
        } elseif (empty($value)) {
            $ids = array();
        } else {
            $value = ArrayHelper::toInteger($value);
            $ids = $value;
        }

        $html = '';
        $personas = self::getPersons($ids);
        $items = array();

        foreach ($personas as $row) {
            if (in_array($row->id, $ids)) {
                if ($format == 'ul') {
                    $items[] = "<li>{$row->name}</li>";
                } elseif ($format == 'img') {
                    $items[] = "<li>" . '<img src="' . JUri::root() . $row->image . '"  alt="'.$row->value.'" title="'.$row->name.'" />' . "</li>";
                } else {
                    $items[] = $row->name;
                }
            }
        }

        if ($format == 'ul') {
            $html = "<ul>\n" . implode("\n", $items) . "</ul>\n";
        } elseif ($format == 'img') {
            $html = "<ul>\n" . implode("\n", $items) . "</ul>\n";
        } else {
            $html = implode(', ', $items);
        }

        return $html;
    }

    static public function getPersons($arrayIDs)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('p.id, p.name, p.image');
        $query->from('#__peliculas_persons AS p');
        $query->where('p.id IN (' . implode(',',$arrayIDs) . ')');
        $query->order('p.name ASC');

        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (RuntimeException $e) {
            return false;
        }

        return $options;
    }

    static public function getAllPersons()
    {
        if (self::$persons === null) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('p.id, p.name, p.image');
            $query->from('#__peliculas_persons AS p');
            $query->order('p.name ASC');

            $db->setQuery($query);

            self::$persons = $db->loadObjectList();
        }
        return self::$persons;
    }
}