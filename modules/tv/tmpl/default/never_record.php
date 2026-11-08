<?php
/**
 * Lists the episodes marked never to record, with a command to lift each mark.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Set the desired page title
    $page_title = 'MythWeb - '.t('Never Record');

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_schedules.css">';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

// Nothing marked never to record?
    if (empty($never_records)) {
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
    <th class="x-date"><?php      echo t('Date')       ?></th>
    <th class="x-commands"><?php  echo t('Commands')   ?></th>
</tr>
</thead>
<?php
        foreach ($never_records as $row) {
?><tr class="deactivated">
    <td class="x-title"><?php     echo html_entities($row['title'])     ?></td>
    <td class="x-subtitle"><?php  echo html_entities($row['subtitle'])  ?></td>
    <td class="x-programid"><?php echo html_entities($row['programid']) ?></td>
    <td class="x-date"><?php      echo strftime($_SESSION['date_listing_key'], $row['starttime']) ?></td>
    <td class="x-commands commands"><?php
        echo '<a href="', root_url, 'tv/never_record?schedule_normally=yes',
             '&station=', urlencode($row['station']),
             '&starttime=', $row['starttime'],
             '&title=', urlencode($row['title']), '">',
             rtrim(t('Schedule normally.'), '.'), '</a>';
        ?></td>
</tr><?php
        }
?>
</table>
<?php
    }

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
