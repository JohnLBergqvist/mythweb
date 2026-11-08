<?php
/**
 * Lists MythTV's recording history, and whether each entry still counts as
 * recorded when checking for duplicates.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Set the desired page title
    $page_title = 'MythWeb - '.t('Previously Recorded');

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_schedules.css">';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

// No history?
    if (empty($history)) {
        echo '<div id="no_schedules">', t('No matches found'), '</div>';
    }
    else {
?>
<table id="listings" border="0" cellpadding="4" cellspacing="2" class="list small">
<thead>
<tr class="menu">
    <th class="x-title"><?php     echo t('Title')      ?></th>
    <th class="x-subtitle"><?php  echo t('Subtitle')   ?></th>
    <th class="x-programid"><?php echo t('Program ID') ?></th>
    <th class="x-channel"><?php   echo t('Channel')    ?></th>
    <th class="x-date"><?php      echo t('Date')       ?></th>
    <th class="x-status"><?php    echo t('Status')     ?></th>
    <th class="x-duplicate"><?php echo t('Duplicate')  ?></th>
    <th class="x-commands"><?php  echo t('Commands')   ?></th>
</tr>
</thead>
<?php
        foreach ($history as $row) {
            $recstatus = $GLOBALS['RecStatus_Types'][$row['recstatus']];
?><tr>
    <td class="x-title"><?php     echo html_entities($row['title'])     ?></td>
    <td class="x-subtitle"><?php  echo html_entities($row['subtitle'])  ?></td>
    <td class="x-programid"><?php echo html_entities($row['programid']) ?></td>
    <td class="x-channel"><?php   echo html_entities($row['station'])   ?></td>
    <td class="x-date"><?php      echo strftime($_SESSION['date_listing_key'], $row['starttime']) ?></td>
    <td class="x-status"><?php    echo $recstatus ? $GLOBALS['RecStatus_Names'][$recstatus] : $row['recstatus'] ?></td>
    <td class="x-duplicate"><?php echo $row['duplicate'] ? t('Yes') : t('No') ?></td>
    <td class="x-commands commands"><?php
        echo '<a href="', root_url, 'tv/previously_recorded?duplicate=', $row['duplicate'] ? 0 : 1,
             '&chanid=', urlencode($row['chanid']),
             '&starttime=', $row['starttime'], '">',
             $row['duplicate'] ? t('Allow Rerecord') : t('Never Record'), '</a>';
        ?></td>
</tr><?php
        }
?>
</table>
<?php
    }

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
