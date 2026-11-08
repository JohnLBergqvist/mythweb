<?php
/**
 * Recording history
 *
 * @license     GPL
 *
 * @package     MythWeb
 *
 **/

// Print the main page header
    $page_title = 'MythWeb - '.t('Recording History');
    require_once 'modules/_shared/tmpl/'.tmpl.'/header.php';

// Print the page contents
    if (empty($history))
        echo t('No matches found'), '<br />';

    foreach ($history as $row) {
        $recstatus = $GLOBALS['RecStatus_Types'][$row['recstatus']];
        $entry_url = root_url.'tv/recording_history?chanid='.urlencode($row['chanid']).'&starttime='.$row['starttime'];
    // The title, linked to the guide entry while it's still in the guide
        $title = html_entities($row['title']);
        if ($row['in_guide'])
            $title = '<a href="'.root_url.'tv/detail/'.$row['chanid'].'/'.$row['starttime'].'">'.$title.'</a>';
        echo '<b>', $title, '</b><br />';
        if (strlen($row['subtitle']))
            echo html_entities($row['subtitle']), '<br />';
        $seasep = Program::format_season_episode($row['season'], $row['episode'], $row['syndicatedepisodenumber']);
        if (strlen($seasep))
            echo t('Seas/Ep'), ': ', html_entities($seasep), '<br />';
        echo html_entities($row['station']), ', ', strftime($_SESSION['date_listing_key'], $row['starttime']), '<br />';
        echo t('Status'), ': ', $recstatus ? $GLOBALS['RecStatus_Names'][$recstatus] : $row['recstatus'], '<br />';
    // Recording attempts and never record entries show whether they count
        echo t('Prevents Recording of Future Showings'), ': ';
        if ($row['duplicate'])
            echo t('Yes');
        elseif (in_array($recstatus, array('Recorded', 'Failed', 'Aborted', 'NeverRecord')))
            echo t('No');
        else
            echo t('N/A');
        echo '<br />';
    // The same commands as the default template
        $links = array();
        if ($row['duplicate'])
            $links[] = '<a href="'.$entry_url.'&duplicate=0">'
                      .($recstatus == 'NeverRecord' ? t('Undo Never Record') : t('Forget Old Episode')).'</a>';
        elseif ($recstatus == 'Recorded')
            $links[] = '<a href="'.$entry_url.'&undo_forget_old=yes">'.t('Undo Forget Old').'</a>';
        else
            $links[] = '<a href="'.$entry_url.'&duplicate=1">'.t('Never Record Episode').'</a>';
        if ($row['recordedid'])
            $links[] = '<a href="'.root_url.'tv/recording/'.$row['recordedid'].'">'.t('Recording Details').'</a>';
        elseif ($recstatus != 'NeverRecord')
            $links[] = '<a href="'.$entry_url.'&remove=yes">'.t('Remove from the list').'</a>';
        echo implode(' | ', $links), '<br /><br />';
    }

// Print the main page footer
    require_once 'modules/_shared/tmpl/'.tmpl.'/footer.php';
