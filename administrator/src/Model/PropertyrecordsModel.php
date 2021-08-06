<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Propertyprices\Component\Xws_property\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
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
		'aquireddate.from', 'aquireddate.to',
				'completeddate', 'a.completeddate',
		'completeddate.from', 'completeddate.to',
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
        // List state information.
        parent::populateState('completeddate', 'DESC');

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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
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
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		// Join over the foreign key 'parish'
		$query->select('`#__xws_property_parishes_3606170`.`name` AS parishes_fk_value_3606170');
		$query->join('LEFT', '#__xws_property_parishes AS #__xws_property_parishes_3606170 ON #__xws_property_parishes_3606170.`name` = a.`parish`');
		// Join over the foreign key 'town'
		$query->select('`#__xws_property_towns_3606168`.`name` AS towns_fk_value_3606168');
		$query->join('LEFT', '#__xws_property_towns AS #__xws_property_towns_3606168 ON #__xws_property_towns_3606168.`id` = a.`town`');
                

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
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
				$query->where('(#__xws_property_parishes_3606170.name LIKE ' . $search . '  OR  a.houseno LIKE ' . $search . '  OR  a.housename LIKE ' . $search . '  OR  a.streetname LIKE ' . $search . '  OR  a.streetname2 LIKE ' . $search . '  OR #__xws_property_towns_3606168.name LIKE ' . $search . '  OR  a.postcode LIKE ' . $search . ' )');
			}
		}
                

		// Filtering parish
		$filter_parish = $this->state->get("filter.parish");

		if ($filter_parish !== null && !empty($filter_parish))
		{
			$query->where("a.`parish` = '".$db->escape($filter_parish)."'");
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
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->town))
			{
				$values    = explode(',', $oneItem->town);
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

				$oneItem->town = !empty($textValue) ? implode(', ', $textValue) : $oneItem->town;
			}
		}

		return $items;
	}
}
