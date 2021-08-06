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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/src/Helper/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_xws_property.admin')
    ->useScript('com_xws_property.admin');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_xws_property');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_xws_property&task=propertyrecords.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}

// $sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_xws_property&view=propertyrecords'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
            	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<div class="clearfix"></div>
				<table class="table table-striped" id="propertyrecordList">
					<thead>
					<tr>
						<?php if (isset($this->items[0]->ordering)): ?>
							<th width="1%" class="nowrap center hidden-phone">
	                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
	                        </th>
						<?php endif; ?>
						<th width="1%" >
							<input type="checkbox" name="checkall-toggle" value=""
								   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						<?php if (isset($this->items[0]->state)): ?>
							<th width="1%" class="nowrap center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
</th>
						<?php endif; ?>

										<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENO', 'a.houseno', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENAME', 'a.housename', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME', 'a.streetname', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME2', 'a.streetname2', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_TOWN', 'a.town', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_POSTCODE', 'a.postcode', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_MARKETVALUE', 'a.marketvalue', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_SALEPRICE', 'a.saleprice', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_AQUIREDDATE', 'a.aquireddate', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo HTMLHelper::_('searchtools.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_COMPLETEDDATE', 'a.completeddate', $listDirn, $listOrder); ?>
				</th>

						
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
					<?php foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_xws_property');
						$canEdit    = $user->authorise('core.edit', 'com_xws_property');
						$canCheckin = $user->authorise('core.manage', 'com_xws_property');
						$canChange  = $user->authorise('core.edit.state', 'com_xws_property');
						?>
						<tr class="row<?php echo $i % 2; ?>">

							<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
							<?php if ($canChange) :
								$disableClassName = '';
								$disabledLabel    = '';

								if (!$saveOrder) :
									$disabledLabel    = Text::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
									  title="<?php echo $disabledLabel ?>">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none" name="order[]" size="5"
									   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
							<?php else : ?>
								<span class="sortable-handler inactive">
									<i class="icon-menu"></i>
								</span>
							<?php endif; ?>
							</td>
							<?php endif; ?>
							<td >
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<?php if (isset($this->items[0]->state)): ?>
								<td class="center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'propertyrecords.', $canChange, 'cb'); ?>
</td>
							<?php endif; ?>

											<td>

					<?php echo $item->id; ?>
				</td>				<td>

					<?php echo $item->houseno; ?>
				</td>				<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'propertyrecords.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo Route::_('index.php?option=com_xws_property&task=propertyrecord.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->housename); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->housename); ?>
				<?php endif; ?>

				</td>				<td>

					<?php echo $item->streetname; ?>
				</td>				<td>

					<?php echo $item->streetname2; ?>
				</td>				<td>

					<?php echo $item->town; ?>
				</td>				<td>

					<?php echo $item->postcode; ?>
				</td>				<td>

					<?php echo $item->marketvalue; ?>
				</td>				<td>

					<?php echo $item->saleprice; ?>
				</td>				<td>

					<?php
					$date = $item->aquireddate;
					echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC6')) : '-';
					?>
				</td>				<td>

					<?php
					$date = $item->completeddate;
					echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC6')) : '-';
					?>
				</td>

						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
	            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>