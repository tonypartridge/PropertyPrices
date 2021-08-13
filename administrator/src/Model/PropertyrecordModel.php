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

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Helper\TagsHelper;

/**
 * Xws_property model.
 *
 * @since  1.6
 */
class PropertyrecordModel extends AdminModel
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_XWS_PROPERTY';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_xws_property.propertyrecord';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

        
        
        
        
        
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    Table    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Propertyrecord', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form  A Form object on success, false on failure
	 *
	 * @since    1.6
     *
     * @throws
	 */
	public function getForm($data = array(), $loadData = true)
	{
            // Initialise variables.
            $app = Factory::getApplication();

            // Get the form.
            $form = $this->loadForm(
                    'com_xws_property.propertyrecord', 'propertyrecord',
                    array('control' => 'jform',
                            'load_data' => $loadData
                    )
            );

            

            if (empty($form))
            {
                return false;
            }

            return $form;
	}

	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
     *
     * @throws
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_xws_property.edit.propertyrecord.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
                        
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
            
            if ($item = parent::getItem($pk))
            {
            	if (isset($item->params))
            	{
            		$item->params = json_encode($item->params);
            	}
            	
                // Do any procesing on fields here if needed
            }

            return $item;
            
	}

	/**
	 * Method to duplicate an Propertyrecord
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
        $app = Factory::getApplication();
		$user = Factory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_xws_property'))
		{
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
                    
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new \Exception($table->getError());
				}
				
				if (!empty($table->parish))
				{
					if (is_array($table->parish))
					{
						$table->parish = implode(',', $table->parish);
					}
				}
				else
				{
					$table->parish = '';
				}

				if (!empty($table->town))
				{
					if (is_array($table->town))
					{
						$table->town = implode(',', $table->town);
					}
				}
				else
				{
					$table->town = '';
				}


				// Trigger the before save event.
				$result = $app->triggerEvent($this->event_before_save, array($context, &$table, true, $table));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new \Exception($table->getError());
				}

				// Trigger the after save event.
				$app->triggerEvent($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new \Exception($table->getError());
			}
                    
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__xws_property_records');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Save any changes on toggle button clicked on list view
	 *
	 * @param   int     $pk     Primary key of the item
	 *
	 * @param   string  $field  Name of the field to toggle
	 *
	 * @return bool
	 */
	public function toggle($pk, $field)
	{
		$result = false;

		// Obtain item data
		$item = $this->getItem($pk);

		if ($item)
		{
			$data         = get_object_vars($item);
			$data[$field] = ($item->$field == 1) ? 0 : 1;
			$result       = $this->save($data);
		}

		return $result;
	}
}
