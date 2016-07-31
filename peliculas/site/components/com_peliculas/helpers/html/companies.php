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
abstract class JHtmlCompanies
{
    static protected $companies = null;

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
        $companies = self::getCompanies($ids);
        $items = array();

        foreach ($companies as $row) {
            if (in_array($row->id, $ids)) {
                if ($format == 'ul') {
                    $items[] = "<li>{$row->name}</li>";
                } else {
                    $items[] = $row->name;
                }
            }
        }

        if ($format == 'ul') {
            $html = "<ul>\n" . implode("\n", $items) . "</ul>\n";
        } else {
            $html = implode(', ', $items);
        }

        return $html;
    }

    static public function getCompanies($arrayIDs)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('c.id, c.name');
        $query->from('#__peliculas_companies AS c');
        $query->where('c.id IN (' . implode(',',$arrayIDs) . ')');
        $query->order('c.name ASC');

        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (RuntimeException $e) {
            return false;
        }

        return $options;
    }

    static public function getAllCompanies()
    {
        if (self::$companies === null) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('c.id, c.name');
            $query->from('#__peliculas_companies AS c');
            $query->order('c.name ASC');

            $db->setQuery($query);

            self::$companies = $db->loadObjectList();
        }
        return self::$companies;
    }
}