<?php
/**
 * @package    Joomla.Cli
 *
 * @copyright  (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// We are a valid entry point.
const _JEXEC = 1;

// Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
const JOOMLA_MINIMUM_PHP = '7.2.5';

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
    echo 'Sorry, your PHP version is not supported.' . PHP_EOL;
    echo 'Your host needs to use PHP version ' . JOOMLA_MINIMUM_PHP . ' or newer to run this version of Joomla!' . PHP_EOL;
    echo 'You are currently running PHP version ' . PHP_VERSION . '.' . PHP_EOL;

    exit;
}

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Check for presence of vendor dependencies not included in the git repository
if (!file_exists(JPATH_LIBRARIES . '/vendor/autoload.php') || !is_dir(JPATH_ROOT . '/media/vendor'))
{
    echo 'It looks like you are trying to run Joomla! from our git repository.' . PHP_EOL;
    echo 'To do so requires you complete a couple of extra steps first.' . PHP_EOL;
    echo 'Please see https://docs.joomla.org/Special:MyLanguage/J4.x:Setting_Up_Your_Local_Environment for further details.' . PHP_EOL;

    exit;
}

// Check if installed
if (!file_exists(JPATH_CONFIGURATION . '/configuration.php')
    || (filesize(JPATH_CONFIGURATION . '/configuration.php') < 10))
{
    echo 'Install Joomla to run cli commands' . PHP_EOL;

    exit;
}

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

echo "\r\nChecking if Land Registry File Exits";

//$month  = strtolower(date('M'));
$month  = 'jul';
$year   = '2021';
$filename	= 'landtransactions' . $month . $year . '.csv';

/*
** Isle of Man Government Land Registry CSV File formatting as follows
** - SubUnit_Name - Not really used in the last 32K results when checked
** - House_Number -
** - Street_Name -
** - Locality -
** - Town
** - Postcode
** - Parish_
** - Market_Value
** - Consideration - The price the land/property sold for
** - Acquisition_Date
** - Completion_Date
*/

$landRegistryUrl    = 'https://www.gov.im/media/1357981/' . $filename;

$fileExists		= file_exists(JPATH_BASE . '/tmp/' . $filename);

if(!$fileExists && !doesUrlExists($landRegistryUrl))  {

    echo "\r\nError, could not find the file";

} else {

    if (!$fileExists) {
        echo "\r\nDownloading Import File from Govt Website";
        file_put_contents("../tmp/" . $filename, fopen($landRegistryUrl, 'r'));
    }

    echo "\r\nProcessing CSV File to Import Properties";

    $csvFile    = __DIR__ . '/../tmp/' . $filename;
    $row        = 1;
    $data       = array();
    $columnKeyedData = array();
    $parishes   = array();
    $cities     = array();
    $towns      = array();
    $locality   = array();
    $streets    = array();

    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            // Set the headers
            if ($row === 1) {

                // Set the headers
                $headers = $data;

                // Add a custom indeitifer column
                $headers[] = 'Hash';
                ++$row;

                // Get out of here
                continue;

            } else {

                ++$row;

                $num = count($data);
                $row++;
                $columnKeyedDataRow = array();
                for ($c = 0; $c < $num; $c++) {
                    $columnKeyedDataRow[$headers[$c]] = $data[$c] . "";
                }

                $hash = sha1(
                    $columnKeyedDataRow['Street_Name'] .
                    $columnKeyedDataRow['Postcode'] .
                    $columnKeyedDataRow['Consideration'] .
                    $columnKeyedDataRow['Completion_Date']
                );

                $columnKeyedDataRow['Hash'] = $hash;


                // Build a custom unique idenitfier of the dataset
                $columnKeyedData[$hash] = $columnKeyedDataRow;

                $town = wordReplacements(trim($columnKeyedData[$hash]['Town']));
                if(!empty($town)) {
                    $towns[$town] = $town;
                }

                $parish = trim($columnKeyedData[$hash]['Parish_']);
                $parishes['Parish_'][$parish] = $parish;
                $streetName = trim(preg_replace('/[0-9]+/', '', $columnKeyedData[$hash]['Street_Name']));
                $streets['Street_Name'][$streetName] = $streetName;
            }
        }

        sort($columnKeyedData);

        fclose($handle);
    }

    // Fetch All the Towns first
    $db = Factory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'name')))->from($db->quoteName('#__xws_property_towns'));
    $db->setQuery($query);
    $dbTowns = $db->loadAssocList('name');

    foreach($dbTowns AS $town)
    {
        unset($towns[$town['name']]);
    }

    if (count($towns) > 0)
    {

        $dbo = Factory::getDbo();
        $query = $dbo->getQuery(true);

        $columns = array('ordering','state','name', 'created_by', 'modified_by');
        $values = array();


        foreach ($towns as $tta) {

            // Catch empty rows
            if ($tta !== '') {
                $values[] = '1,1,' . $dbo->quote($tta) . ',909,909';
            }
        }

        $query->insert($dbo->quoteName('#__xws_property_towns'));
        $query->columns($columns);
        $query->values($values);
        $dbo->setQuery($query);
        $dbo->execute();

    }

    // Re-fetch the towns for mapping
    $db = Factory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'name')))->from($db->quoteName('#__xws_property_towns'));
    $db->setQuery($query);

    $allDbTowns = $db->loadObjectList('name');


    // Now to insert the rows...
    // First get the last inserted hash in the DB
    $db = Factory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select($db->quoteName(array('id', 'hash')))
        ->from($db->quoteName('#__xws_property_records'))
        ->order([$db->quoteName('id') . ' DESC' ])
        ->setLimit(1);
    $db->setQuery($query);

    $lastInsertedRowHash = $db->loadObject()->hash;
    $foundLastRow        = false;

    foreach ($columnKeyedData as $key => $record) {

        // Lets skip the records until we find the last inserted, then skip it and continue
        if ($record['Hash'] === $lastInsertedRowHash) {
            $foundLastRow = true;
            continue;
        }

        // Will be in the same original order, so lets continue until we cannot find the hash
        if($foundLastRow || !$lastInsertedRowHash)
        {
            $dbo = Factory::getDbo();
            $query = $dbo->getQuery(true);

            $columns = array(
                'ordering',
                'state',
                'created_by',
                'modified_by',
                'houseno',
                'housename',
                'streetname',
                'streetname2',
                'town',
                'postcode',
                'parish',
                'marketvalue',
                'saleprice',
                'aquireddate',
                'completeddate',
                'hash'
            );

var_dump($record);
            $values = array();

            $marketValue    = str_replace(',', '', empty($record['Market_Value']) ? 0 : $record['Market_Value']);
            $saleValue      = str_replace(',', '', empty($record['Consideration']) ? 0 : $record['Consideration']);
            $town           = (int) empty($allDbTowns[$record['Town']]->id) ? 0 : $allDbTowns[$record['Town']]->id;

            $values[] ='
                1,
                1,
                909,
                909,
                ' . (int) $record['House_Number'] . ',
                ' . $db->quote( $record['House_Name'] ) . ',
                ' . $db->quote( $record['Street_Name'] ) . ',
                "",
                ' . $town . ',
                ' . $db->quote( $record['Postcode']) . ',
                ' . $db->quote( $record['Parish_'] ) . ',
                ' . $db->quote( $marketValue ) . ',
                ' . $db->quote( $saleValue)  . ',
                ' . $db->quote( date('Y-m-d H:i:s', strtotime(strtr($record['Acquisition_Date'], '/', '-')))) . ',
                ' . $db->quote( date('Y-m-d H:i:s', strtotime(strtr($record['Completion_Date'], '/', '-')))) . ',
                ' . $db->quote( $record['Hash']
                );


            $query->insert($dbo->quoteName('#__xws_property_records'));
            $query->columns($columns);
            $query->values($values);

            //echo $query->dump();

            $dbo->setQuery($query);
            $dbo->execute();

        }
    }

    echo "\r\nScript has finished running";
}
    

function doesUrlExists($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200)
    {
        $status = true;
    }
    else
    {
        $status = false;
    }
    curl_close($ch);
    return $status;
}

function wordReplacements(String $string) {

    // Do two arrays for easy reading.

    // Remove spaces from string / word
    $string = trim($string);

    // Array of words to find
    $wtf   = array(
        1  => 'Pee1',
        2  => 'Tromode, Douglas',
        3  => 'Nr Lower',
        4  => 'Port Saint',
        5  => 'St.Johns',
        6  => "St John's",
        7  => 'St.',
        8  => 'Ballasalla, Malew',
        9  => 'Ballabeg, Arbory',
        10 => 'Andreas And Lezayre',
        11 => 'Ballawattleworth Estate',
        12 => 'Birch Hill Park',
        13 => 'Balthane Industrial Estate',
        14 => 'Bride Road',
        15 => 'Cronk Y Voddy',
        16 => 'Cregneish',
        17 => 'Cstletown',
        18 => 'Dougls',
        19 => 'Porst',
        20 => 'Lezaye',
        21 => 'Lezary',
        22 => 'Kik Michael',
        23 => 'Kirk Andreas',
        24 => 'Lezayre And Ballaugh',
        25 => 'Maugthold',
        26 => 'Onhan',
        27 => 'Poer Erin',
        28 => 'Port Errin',
        29 => 'Patrick Village',
        30 => 'Strang',
        31 => 'Bradddan',
        32 => 'Im4 3Ez',
        33 => 'Im5 3Aq',
        34 => 'Isle Of Man Business Park Extension',
        35 => 'Part Of The Estate Of Mullinaragher',
        36 => 'Sanron',
        37 => 'Second Avenue',
        38 => 'The The Strang',
        39 => 'The Strang',
        40 => 'Vernon Road',
    );

    // Array of words to replace with
    $wtr   = array(
        1  => 'Peel',
        2  => 'Tromode',
        3  => 'Lower',
        4  => 'Port St',
        5  => 'St Johns',
        6  => 'St Johns',
        7  => 'St',
        8  => 'Ballasalla',
        9  => 'Ballabeg',
        10 => 'Andreas',
        11 => 'Ballawattleworth',
        12 => 'Birch Hill',
        13 => 'Balthane',
        14 => 'Bride',
        15 => 'Cronk-Y-Voddy',
        16 => 'Cregneash',
        17 => 'Castletown',
        18 => 'Douglas',
        19 => 'Port',
        20 => 'Lezayre',
        21 => 'Lezayre',
        22 => 'Kirk Michael',
        23 => 'Andreas',
        24 => 'Lezayre',
        25 => 'Maughold',
        26 => 'Onchan',
        27 => 'Port Erin',
        28 => 'Port Erin',
        29 => 'Patrick',
        30 => 'The Strang',
        31 => 'Braddan',
        32 => 'Foxdale',
        33 => 'Patrick',
        34 => 'Isle Of Man Business Park',
        35 => 'St Marks',
        36 => 'Santon',
        37 => 'Onchan',
        38 => 'Strang',
        39 => 'Strang',
        40 => 'Maughold',

    );

    // Array of exact word matches i.e. St John would match against St Johns so we want to do an exact replacement here.
    $ewtf   = array(
        1 => 'St John',
        2 => 'Johns',
    );

    $ewtr   = array(
        1 => 'St Johns',
        2 => 'St Johns',
    );

    foreach ($ewtf as $key => $word) {
        if($word === $string) {
            $string = $ewtr[$key];
        }
    }

    // Do the word replacements now
    $string = str_replace($wtf, $wtr, $string);


    return $string;
}