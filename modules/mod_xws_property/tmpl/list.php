<?php
/**
 * @version     CVS: 1.0.0
 * @package     com_xws_property
 * @subpackage  mod_xws_property
 * @author      Tony Partridge <tony@xws.im>
 * @copyright   2021 Tony Partridge - Xtech Web Services Ltd
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Propertyprices\Module\Xws_property\Site\Helper\Xws_propertyHelper;

$elements = Xws_propertyHelper::getList($params);

$tableField = explode(':', $params->get('field'));
$table_name = !empty($tableField[0]) ? $tableField[0] : '';
$field_name = !empty($tableField[1]) ? $tableField[1] : '';
?>

<?php if (!empty($elements)) : ?>
	<table class="jcc-table">
		<?php foreach ($elements as $element) : ?>
			<tr>
				<th><?php echo Xws_propertyHelper::renderTranslatableHeader($table_name, $field_name); ?></th>
				<td><?php echo Xws_propertyHelper::renderElement(
						$table_name, $params->get('field'), $element->{$field_name}
					); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif;
