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
    $page_title = 'MythWeb - '.t('Recording History');

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_schedules.css">';
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_recording_history.css">';

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
    <th class="x-seasep"><?php    echo t('Seas/Ep')    ?></th>
    <th class="x-programid"><?php echo t('Program ID') ?></th>
    <th class="x-channel"><?php   echo t('Channel')    ?></th>
    <th class="x-date"><?php      echo t('Date')       ?></th>
    <th class="x-status"><?php    echo t('Status')     ?></th>
    <th class="x-duplicate"><?php echo t('Prevents Recording of Future Showings') ?></th>
    <th class="x-commands" colspan="2"><?php echo t('Commands') ?></th>
</tr>
</thead>
<?php
        foreach ($history as $row) {
            $recstatus = $GLOBALS['RecStatus_Types'][$row['recstatus']];
?><tr>
    <td class="x-title"><?php
        if ($row['in_guide'])
            echo '<a href="', root_url, 'tv/detail/', $row['chanid'], '/', $row['starttime'], '">',
                 html_entities($row['title']), '</a>';
        else
            echo html_entities($row['title']);
        ?></td>
    <td class="x-subtitle"><?php  echo html_entities($row['subtitle'])  ?></td>
    <td class="x-seasep"><?php if ($row['episode'] > 0) {echo $row['season'],'x',str_pad($row['episode'],2,"0",STR_PAD_LEFT);} ?></td>
    <td class="x-programid"><?php echo html_entities($row['programid']) ?></td>
    <td class="x-channel"><?php   echo html_entities($row['station'])   ?></td>
    <td class="x-date"><?php      echo strftime($_SESSION['date_listing_key'], $row['starttime']) ?></td>
    <td class="x-status"><?php    echo $recstatus ? $GLOBALS['RecStatus_Names'][$recstatus] : $row['recstatus'] ?></td>
    <td class="x-duplicate"><?php
    // Recording attempts and never record entries show whether they count;
    // showings that were never recorded, such as skipped ones, can't count
        if ($row['duplicate'])
            echo t('Yes');
        elseif (in_array($recstatus, array('Recorded', 'Failed', 'Aborted', 'NeverRecord')))
            echo t('No');
        else
            echo t('N/A');
        ?></td>
    <td class="x-commands commands"><?php
    // A successful recording that no longer counts can be made to count again
        if ($row['duplicate'])
            $action = 'duplicate=0';
        elseif ($recstatus == 'Recorded')
            $action = 'undo_forget_old=yes';
        else
            $action = 'duplicate=1';
        echo '<a href="', root_url, 'tv/recording_history?', $action,
             '&chanid=', urlencode($row['chanid']),
             '&starttime=', $row['starttime'], '">',
             $row['duplicate'] ? ($recstatus == 'NeverRecord' ? t('Undo Never Record') : t('Forget Old Episode'))
                 : ($recstatus == 'Recorded' ? t('Undo Forget Old') : t('Never Record Episode')),
             '</a>';
        ?></td>
    <td class="x-commands commands"><?php
    // An entry is kept while its recording still exists, and Undo Never Record
    // already deletes never record entries
        if ($row['recstartts'])
            echo '<a href="', root_url, 'tv/detail/', $row['chanid'], '/', $row['recstartts'], '">',
                 t('Recording Details'), '</a>';
        elseif ($recstatus != 'NeverRecord')
            echo '<a href="', root_url, 'tv/recording_history?remove=yes',
                 '&chanid=', urlencode($row['chanid']),
                 '&starttime=', $row['starttime'], '"',
                 ' onclick="return confirm(\'', html_entities(addslashes(t('This will permanently remove this entry from the history. Are you sure?'))), '\')">',
                 t('Remove from the list'), '</a>';
        ?></td>
</tr><?php
        }
?>
</table>
<?php
    }

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
