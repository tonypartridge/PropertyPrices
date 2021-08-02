<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Propertyprices\Component\Xws_property\Api\View\Propertyrecords;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * The Propertyrecords view
 *
 * @since  4.0.0
 */
class JsonApiView extends BaseApiView
{
	/**
	 * The fields to render item in the documents
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $fieldsToRenderItem = [
		'id', 
		'ordering', 
		'state', 
		'houseno', 
		'housename', 
		'streetname', 
		'streetname2', 
		'town', 
		'postcode', 
		'marketvalue', 
		'saleprice', 
		'aquireddate', 
		'completeddate', 
	];

	/**
	 * The fields to render items in the documents
	 *
	 * @var  array
	 * @since  4.0.0
	 */
	protected $fieldsToRenderList = [
		'id', 
		'ordering', 
		'state', 
		'houseno', 
		'housename', 
		'streetname', 
		'streetname2', 
		'town', 
		'postcode', 
		'marketvalue', 
		'saleprice', 
		'aquireddate', 
		'completeddate', 
	];
}