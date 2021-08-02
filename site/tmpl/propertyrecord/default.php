<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;


?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_HOUSENO'); ?></th>
			<td><?php echo $this->item->houseno; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_HOUSENAME'); ?></th>
			<td><?php echo $this->item->housename; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_STREETNAME'); ?></th>
			<td><?php echo $this->item->streetname; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_STREETNAME2'); ?></th>
			<td><?php echo $this->item->streetname2; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_TOWN'); ?></th>
			<td><?php echo $this->item->town; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_POSTCODE'); ?></th>
			<td><?php echo $this->item->postcode; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_MARKETVALUE'); ?></th>
			<td><?php echo $this->item->marketvalue; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_SALEPRICE'); ?></th>
			<td><?php echo $this->item->saleprice; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_AQUIREDDATE'); ?></th>
			<td><?php echo $this->item->aquireddate; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_COMPLETEDDATE'); ?></th>
			<td><?php echo $this->item->completeddate; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_XWS_PROPERTY_FORM_LBL_PROPERTYRECORD_HASH'); ?></th>
			<td><?php echo $this->item->hash; ?></td>
		</tr>

	</table>

</div>

