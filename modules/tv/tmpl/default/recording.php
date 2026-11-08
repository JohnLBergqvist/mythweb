<?php
/**
 * This displays details about a recording, its file and its jobs, as well as
 * provides the commands that act on the recording itself.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Set the desired page title
    $page_title = 'MythWeb - '.t('Recording Details').":  $schedule->title";

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_schedule.css">';
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_detail.css">';
    $headers[] = '<script type="text/javascript" src="'.root_url.'js/libs/flowplayer/flowplayer.js"></script>';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

    $flv_w = setting('WebFLV_w');
    $flv_h = intVal($flv_w / $program->getAspect()) + 25;  // +25px for the playback controls
?>

<script type="text/javascript">
<!--

<?php require tmpl_dir.'_recording_scripts.php' ?>

<?php require tmpl_dir.'_metadata_scripts.php' ?>
// -->
</script>

    <div id="content">
        <div id="contentBlock">
        <div id="div-x-info">
        <table id="x-info" width="100%" border="0" cellspacing="0" cellpadding="0">
<?php   require tmpl_dir.'_program_info.php';
    // Can we perform an accurate duplicate check?
        $can_dupcheck = preg_match('/\S/', $program->title)
                        && preg_match('/\S/', $program->programid.$program->subtitle.$program->description);
        $episode_name = $program->title.($program->subtitle ? ': '.$program->subtitle : '');
        ?><tr id="x-status">
            <th><?php echo t('MythTV Status') ?>:</th>
            <td><?php
                echo $GLOBALS['RecStatus_Names'][$program->recstatus],
                     ' (', $GLOBALS['RecStatus_Borders'][recstatus_class($program)], ')<br>';
                echo $GLOBALS['RecStatus_Reasons'][$program->recstatus], '<br>';
                if ($can_dupcheck && $program->has_counting_history()) {
                    echo '<a href="', $recording_page_url, '?forget_old=yes"',
                         ' title="', html_entities(t('info:forget old')), '">',
                         t('Forget Old Episode'), '</a>';
                }
                elseif ($can_dupcheck && $program->has_forgotten_history()) {
                    echo '<a href="', $recording_page_url, '?undo_forget_old=yes">',
                         t('Undo Forget Old'), '</a>';
                }
                if ($program->can_delete) {
                    echo '<a onclick="javascript:confirm_delete(false)"',
                         ' title="', html_entities(t('Delete $1', $episode_name)), '">',
                         t('Delete'), '</a>',
                         '<a onclick="javascript:confirm_delete(true)"',
                         ' title="', html_entities(t('Delete and forget old episode: $1', $episode_name)), '">',
                         t('Delete + Forget Old Episode'), '</a>';
                }
                ?></td>
        </tr>
        <tr class="x-links">
            <th><?php echo t('More') ?>:</th>
            <td>
<?php           if ($in_guide) { ?>
                <a href="<?php echo root_url ?>tv/detail/<?php echo $program->chanid, '/', $program->starttime ?>"><?php
                    echo t('Program Detail') ?></a>
<?php           } ?>
                <a href="<?php echo root_url ?>tv/search/<?php echo str_replace('%2F', '/', rawurlencode('^'.$program->title.'$')) ?>?field=title"><?php
                    echo t('Find other showings of this program') ?></a>
                <a href="<?php echo root_url ?>tv/recorded"><?php
                    echo t('Back to the recorded programs') ?></a>
            </td>
        </tr>
        </table>
    </div>

<?php require tmpl_dir.'_recording_files.php' ?>

    </div>
    </div>
<?php

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
