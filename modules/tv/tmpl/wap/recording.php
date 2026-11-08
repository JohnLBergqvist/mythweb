<?php
/**
 * Recording detail
 *
 * @license     GPL
 *
 * @package     MythWeb
 *
 **/

// Print the main page header
    $page_title = t('Recording Details');
    require_once 'modules/_shared/tmpl/'.tmpl.'/header.php';

// The commands that act on the recording and its episode
    $can_dupcheck = preg_match('/\S/', $program->title)
                    && preg_match('/\S/', $program->programid.$program->subtitle.$program->description);
    $delete_url   = root_url.'tv/recorded?delete=yes&chanid='.$program->chanid.'&starttime='.$program->recstartts;
    $status_links = array();
    if ($can_dupcheck && $program->has_counting_history())
        $status_links[] = '<a href="'.$recording_page_url.'?forget_old=yes">'.t('Forget Old Episode').'</a>';
    elseif ($can_dupcheck && $program->has_forgotten_history())
        $status_links[] = '<a href="'.$recording_page_url.'?undo_forget_old=yes">'.t('Undo Forget Old').'</a>';
    if ($program->can_delete) {
        $status_links[] = '<a href="'.$delete_url.'">'.t('Delete').'</a>';
        $status_links[] = '<a href="'.$delete_url.'&forget_old=yes">'.t('Delete + Forget Old Episode').'</a>';
    }

// Print the page contents
?>
<a href="<?php echo root_url ?>tv/channel/<?php echo $channel->chanid ?>" >
<?php echo $_SESSION["prefer_channum"] ? $channel->channum : $channel->callsign ?> &nbsp;
<?php echo $_SESSION["prefer_channum"] ? $channel->callsign : $channel->channum ?></a><br />

<b><?php echo $program->title ?></b><br />
<?php echo date('D m/d/y', $program->starttime) ?><br />
<?php echo strftime($_SESSION['time_format'], $program->starttime) ?> to <?php echo strftime($_SESSION['time_format'], $program->endtime) ?> (<?php echo (int)(($program->endtime - $program->starttime)/60) ?> minutes)<br />
<?php
    if ($program->previouslyshown)
        echo '('.t('Repeat').')<br />';
    if (strlen($program->subtitle))
        echo t('Episode'), ': <b>', $program->subtitle, '</b><br />';
    if (strlen($program->description))
        echo t('Description'), ': ', $program->description, '<br />';
    if (strlen($program->category))
        echo t('Category'), ': ', $program->category, '<br />';
    if (strlen($program->season_episode()))
        echo t('Seas/Ep'), ': ', html_entities($program->season_episode()), '<br />';
    if (strlen($program->airdate))
        echo t('Original Airdate'), ': ', $program->airdate, '<br />';
    echo t('File Length'), ': ', nice_length($program->length), ' (',
         t('$1 to $2', strftime($_SESSION['time_format'], $program->recstartts),
                       strftime($_SESSION['time_format'], $program->recendts)), ')<br />';
    echo t('File Size'), ': ', nice_filesize($program->filesize), '<br />';
    echo t('MythTV Status'), ': ', $GLOBALS['RecStatus_Names'][$program->recstatus], '<br />',
         $GLOBALS['RecStatus_Reasons'][$program->recstatus], '<br />';
    if (count($status_links))
        echo implode(' | ', $status_links), '<br />';
    echo '<br />';
    if ($in_guide)
        echo '<a href="', root_url, 'tv/detail/', $program->chanid, '/', $program->starttime, '">',
             t('Program Detail'), '</a><br />';
    echo '<a href="', $program->url, '">', t('Direct Download'), '</a><br />';
    echo '<a href="', root_url, 'tv/recorded">', t('Back to the recorded programs'), '</a><br />';

// Print the main page footer
    require_once 'modules/_shared/tmpl/'.tmpl.'/footer.php';
