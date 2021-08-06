<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Propertyprices\Component\Xws_property\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;


/**
 * Methods supporting a list of Xws_property records.
 *
 * @since  1.6
 */
class PropertyrecordsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'parish', 'a.parish',
				'houseno', 'a.houseno',
				'housename', 'a.housename',
				'streetname', 'a.streetname',
				'streetname2', 'a.streetname2',
				'town', 'a.town',
				'postcode', 'a.postcode',
				'marketvalue', 'a.marketvalue',
				'saleprice', 'a.saleprice',
				'aquireddate', 'a.aquireddate',
				'completeddate', 'a.completeddate',
				'hash', 'a.hash',
			);
		}

		parent::__construct($config);
	}

        
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = Factory::getApplication();
            
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;
		if(empty($ordering)){
		$ordering = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', $app->get('filter_order'));
		if (!in_array($ordering, $this->filter_fields))
		{
		$ordering = 'id';
		}
		$this->setState('list.ordering', $ordering);
		}
		if(empty($direction))
		{
		$direction = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', $app->get('filter_order_Dir'));
		if (!in_array(strtoupper($direction), array('ASC', 'DESC', '')))
		{
		$direction = 'DESC';
		}
		$this->setState('list.direction', $direction);
		}

		$list['limit']     = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'), 'uint');
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);
           
            
        // List state information.

        parent::populateState($ordering, $direction);

        $context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

        

        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   DatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
            // Create a new query object.
            $db    = $this->getDbo();
            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query->select(
                        $this->getState(
                                'list.select', 'DISTINCT a.*'
                        )
                );

            $query->from('`#__xws_property_records` AS a');
            
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
		// Join over the foreign key 'parish'
		$query->select('`#__xws_property_parishes_3606170`.`name` AS parishes_fk_value_3606170');
		$query->join('LEFT', '#__xws_property_parishes AS #__xws_property_parishes_3606170 ON #__xws_property_parishes_3606170.`name` = a.`parish`');
		// Join over the foreign key 'town'
		$query->select('`#__xws_property_towns_3606168`.`name` AS towns_fk_value_3606168');
		$query->join('LEFT', '#__xws_property_towns AS #__xws_property_towns_3606168 ON #__xws_property_towns_3606168.`id` = a.`town`');
            
		if (!Factory::getUser()->authorise('core.edit', 'com_xws_property'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

            // Filter by search in title
            $search = $this->getState('filter.search');

            if (!empty($search))
            {
                if (stripos($search, 'id:') === 0)
                {
                    $query->where('a.id = ' . (int) substr($search, 3));
                }
                else
                {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
					$query->where('( a.housename LIKE ' . $search . '  OR  a.streetname LIKE ' . $search . '  OR  a.streetname2 LIKE ' . $search . '  OR #__xws_property_towns_3606168.name LIKE ' . $search . '  OR  a.postcode LIKE ' . $search . ' )');
                }
            }
            

		// Filtering aquireddate
		$filter_aquireddate_from = $this->state->get("filter.aquireddate.from");

		if ($filter_aquireddate_from !== null && !empty($filter_aquireddate_from))
		{
			$query->where("a.`aquireddate` >= '".$db->escape($filter_aquireddate_from)."'");
		}
		$filter_aquireddate_to = $this->state->get("filter.aquireddate.to");

		if ($filter_aquireddate_to !== null  && !empty($filter_aquireddate_to))
		{
			$query->where("a.`aquireddate` <= '".$db->escape($filter_aquireddate_to)."'");
		}

		// Filtering completeddate
		$filter_completeddate_from = $this->state->get("filter.completeddate.from");

		if ($filter_completeddate_from !== null && !empty($filter_completeddate_from))
		{
			$query->where("a.`completeddate` >= '".$db->escape($filter_completeddate_from)."'");
		}
		$filter_completeddate_to = $this->state->get("filter.completeddate.to");

		if ($filter_completeddate_to !== null  && !empty($filter_completeddate_to))
		{
			$query->where("a.`completeddate` <= '".$db->escape($filter_completeddate_to)."'");
		}

            
            
            // Add the list ordering clause.
            $orderCol  = $this->state->get('list.ordering', 'completeddate');
            $orderDirn = $this->state->get('list.direction', 'DESC');

            if ($orderCol && $orderDirn)
            {
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }

            return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $item)
		{

			if (isset($item->parish))
			{

				$values    = explode(',', $item->parish);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = Factory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__xws_property_parishes_3606170`.`name`')
						->from($db->quoteName('#__xws_property_parishes', '#__xws_property_parishes_3606170'))
						->where($db->quoteName('#__xws_property_parishes_3606170.name') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->name;
					}
				}

				$item->parish = !empty($textValue) ? implode(', ', $textValue) : $item->parish;
			}


			if (isset($item->town))
			{

				$values    = explode(',', $item->town);
				$textValue = array();

				foreach ($values as $value)
				{
					$db    = Factory::getDbo();
					$query = $db->getQuery(true);
					$query
						->select('`#__xws_property_towns_3606168`.`name`')
						->from($db->quoteName('#__xws_property_towns', '#__xws_property_towns_3606168'))
						->where($db->quoteName('#__xws_property_towns_3606168.id') . ' = '. $db->quote($db->escape($value)));

					$db->setQuery($query);
					$results = $db->loadObject();

					if ($results)
					{
						$textValue[] = $results->name;
					}
				}

				$item->town = !empty($textValue) ? implode(', ', $textValue) : $item->town;
			}

		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_XWS_PROPERTY_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
