<?php
/**
 * This displays details about a recording and its file, as well as provides
 * the commands that act on the recording itself.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Set the desired page title
    $page_title = 'MythWeb - '.t('Recording Details').":  $program->title";

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_detail.css" />';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

// The commands that act on the recording and its episode
    $can_dupcheck = preg_match('/\S/', $program->title)
                    && preg_match('/\S/', $program->programid.$program->subtitle.$program->description);
    $episode_name = $program->title.($program->subtitle ? ': '.$program->subtitle : '');
    $delete_url   = root_url.'tv/recorded?delete=yes&chanid='.$program->chanid.'&starttime='.$program->recstartts;
    $status_links = array();
    if ($can_dupcheck && $program->has_counting_history())
        $status_links[] = '<a href="'.$recording_page_url.'?forget_old=yes">'.t('Forget Old Episode').'</a>';
    elseif ($can_dupcheck && $program->has_forgotten_history())
        $status_links[] = '<a href="'.$recording_page_url.'?undo_forget_old=yes">'.t('Undo Forget Old').'</a>';
    if ($program->can_delete) {
        $status_links[] = '<a href="'.$delete_url.'" title="'.html_entities(t('Delete $1', $episode_name)).'">'
                         .t('Delete').'</a>';
        $status_links[] = '<a href="'.$delete_url.'&forget_old=yes"'
                         .' title="'.html_entities(t('Delete and forget old episode: $1', $episode_name)).'">'
                         .t('Delete + Forget Old Episode').'</a>';
    }
?>
<div id="program_info" class="clearfix">
    <div id="program_header">
<?php   if ($channel) { ?>
        <div id="channel_info" class="menu menu_border_t menu_border_b menu_border_l menu_border_r">
            <a href="<?php echo root_url; ?>tv/channel/<?php echo $channel->chanid, '/', $program->starttime; ?>">
                <span class="preferred"><?php echo _or($_SESSION["prefer_channum"] ? $channel->channum : $channel->callsign, '&nbsp') ?></span><br />
                <?php echo ($_SESSION["prefer_channum"] ? $channel->callsign : $channel->channum)."\n" ?>
            </a>
        </div>
<?php   } ?>
        <div id="program_title">
            <h1><?php echo $program->title ?></h1>
            <div id="program_time"><?php
                echo strftime('%a, %b %e', $program->starttime);
                if ($program->previouslyshown)
                    echo ' ('.t('Repeat').')';
                echo '<br />',
                     t('$1 to $2', strftime($_SESSION['time_format'], $program->starttime),
                                   strftime($_SESSION['time_format'], $program->endtime)),
                     ' (', tn('$1 min', '$1 mins', intval(($program->endtime - $program->starttime)/60)), ')';
                ?></div>
        </div>
    </div>
    <div id="program_details">
        <dl>
<?php   if (strlen($program->subtitle)) { ?>
            <dt><?php echo t('Episode') ?>:&nbsp;</dt>
            <dd><?php echo $program->subtitle ?></dd>
<?php   }
        if (strlen($schedule->fancy_description)) { ?>
            <dt><?php echo t('Description') ?>:&nbsp;</dt>
            <dd><?php echo nl2br($schedule->fancy_description) ?></dd>
<?php   } ?>
            <dt><?php echo t('MythTV Status') ?>:&nbsp;</dt>
            <dd><?php
                echo $GLOBALS['RecStatus_Names'][$program->recstatus], ': ',
                     $GLOBALS['RecStatus_Reasons'][$program->recstatus];
                if (count($status_links))
                    echo '<br />', implode(' &nbsp;-&nbsp; ', $status_links);
                ?></dd>
        </dl>
    </div>
    <div id="program_extra_details">
        <dl>
<?php   if (strlen($program->category)) { ?>
            <dt><?php echo t('Category') ?>:&nbsp;</dt>
            <dd><?php echo $program->category ?></dd>
<?php   }
        if (strlen($program->season_episode())) { ?>
            <dt><?php echo t('Seas/Ep') ?>:&nbsp;</dt>
            <dd><?php echo html_entities($program->season_episode()) ?></dd>
<?php   }
        if (strlen($program->airdate)) { ?>
            <dt><?php echo t('Original Airdate') ?>:&nbsp;</dt>
            <dd><?php echo $program->airdate ?></dd>
<?php   }
        if (strlen($program->programid)) { ?>
            <dt><?php echo t('Program ID') ?>:&nbsp;</dt>
            <dd><?php echo $program->programid ?></dd>
<?php   } ?>
            <dt><?php echo t('File Length') ?>:&nbsp;</dt>
            <dd><?php echo nice_length($program->length), ' (',
                           t('$1 to $2', strftime($_SESSION['time_format'], $program->recstartts),
                                         strftime($_SESSION['time_format'], $program->recendts)), ')' ?></dd>
            <dt><?php echo t('File Size') ?>:&nbsp;</dt>
            <dd><?php echo nice_filesize($program->filesize) ?></dd>
            <dt><?php echo t('Recording Group') ?>:&nbsp;</dt>
            <dd><?php echo $program->recgroup ?></dd>
        </dl>
    </div>
</div>

<div id="program_links">
<?php   if ($in_guide) { ?>
    <a href="<?php echo root_url ?>tv/detail/<?php echo $program->chanid, '/', $program->starttime ?>"><?php
        echo t('Program Detail') ?></a>
<?php   } ?>
    <a href="<?php echo $program->url ?>"><?php echo t('Direct Download') ?></a>
    <a href="<?php echo root_url ?>tv/search/<?php echo str_replace('%2F', '/', rawurlencode($program->title)) ?>?search_title=1"><?php
        echo t('Find other showings of this program') ?></a>
    <a href="<?php echo root_url ?>tv/recorded"><?php
        echo t('Back to the recorded programs') ?></a>
</div>
<?php

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
