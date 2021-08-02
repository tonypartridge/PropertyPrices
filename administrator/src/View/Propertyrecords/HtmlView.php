<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Propertyprices\Component\Xws_property\Administrator\View\Propertyrecords;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Propertyprices\Component\Xws_property\Administrator\Helper\Xws_propertyHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Xws_property.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Xws_propertyHelper::getActions();

		ToolbarHelper::title(Text::_('COM_XWS_PROPERTY_TITLE_PROPERTYRECORDS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Propertyrecords';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->addNew('propertyrecord.add');
			}
		}

		if ($canDo->get('core.edit.state')  || count($this->transitions))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if (isset($this->items[0]->state))
			{
				$childBar->publish('propertyrecords.publish')->listCheck(true);
				$childBar->unpublish('propertyrecords.unpublish')->listCheck(true);
				$childBar->archive('propertyrecords.archive')->listCheck(true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				$toolbar->delete('propertyrecords.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
			}

            $childBar->standardButton('duplicate')
                ->text('JTOOLBAR_DUPLICATE')
                ->icon('fas fa-copy')
                ->task('propertyrecords.duplicate')
                ->listCheck(true);

			if (isset($this->items[0]->checked_out))
			{
				$childBar->checkin('propertyrecords.checkin')->listCheck(true);
			}

			if (isset($this->items[0]->state))
			{
				$childBar->trash('propertyrecords.trash')->listCheck(true);
			}
		}

		

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{

			if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete'))
			{
				$toolbar->delete('propertyrecords.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_xws_property');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_xws_property&view=propertyrecords');
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => Text::_('JSTATUS'),
			'a.`houseno`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENO'),
			'a.`housename`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENAME'),
			'a.`streetname`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME'),
			'a.`streetname2`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME2'),
			'a.`town`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_TOWN'),
			'a.`postcode`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_POSTCODE'),
			'a.`marketvalue`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_MARKETVALUE'),
			'a.`saleprice`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_SALEPRICE'),
			'a.`aquireddate`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_AQUIREDDATE'),
			'a.`completeddate`' => Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_COMPLETEDDATE'),
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
