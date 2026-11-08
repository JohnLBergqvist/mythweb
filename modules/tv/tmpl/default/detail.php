<?php
/**
 * This displays details about a program, as well as provides recording
 * commands.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Set the desired page title
    $page_title = 'MythWeb - '.t('Program Detail').":  $schedule->title";

// Custom headers
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_schedule.css">';
    $headers[] = '<link rel="stylesheet" type="text/css" href="'.skin_url.'/tv_detail.css">';
    $headers[] = '<script type="text/javascript" src="'.root_url.'js/libs/flowplayer/flowplayer.js"></script>';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

    if ($program && $program->filename) {
        $flv_w = setting('WebFLV_w');
        $flv_h = intVal($flv_w / $program->getAspect()) + 25;  // +25px for the playback controls
    }

// The page that handles a recording's auto-expire and job requests
    $recording_page_url = root_url.'tv/detail/'.$program->chanid.'/'.$program->recstartts;

/*
 * Print the page contents:
 * I really hate tables, but this layout just doesn't work right with pure-css,
 * considering that different languages and setups will all have varied widths
 * that make it too hard to lay out. In its defense, it *is* somewhat tabular.
/*/
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
        if (!empty($program->recstatus) || $can_dupcheck) {
        ?><tr id="x-status">
            <th><?php echo t('MythTV Status') ?>:</th>
            <td><?php
                if (!empty($program->recstatus)) {
                    echo $GLOBALS['RecStatus_Names'][$program->recstatus],
                         ' (', $GLOBALS['RecStatus_Borders'][recstatus_class($program)], ')<br>';
                    echo $GLOBALS['RecStatus_Reasons'][$program->recstatus], '<br>';
                    if ($can_dupcheck && $program->recstatus == 'NeverRecord') {
                        echo '<a href="'.root_url.'tv/detail/'.$program->chanid
                            .'/'.$program->starttime.'?forget_old=yes">'
                            .t('Undo Never Record').'</a>';
                    }
                    elseif ($can_dupcheck && in_array($program->recstatus, array('Recorded', 'PreviousRecording'))
                            && $program->has_counting_history()) {
                        echo '<a href="'.root_url.'tv/detail/'.$program->chanid
                            .'/'.$program->starttime.'?forget_old=yes"'
                            .' title="'.html_entities(t('info:forget old')).'">'
                            .t('Forget Old Episode').'</a>';
                    }
                }
            // Offer to undo Forget Old whenever nothing counts but a successful recording did
                if ($can_dupcheck && $program->recstatus != 'NeverRecord'
                        && !$program->has_counting_history() && $program->has_forgotten_history()) {
                    echo '<a href="'.root_url.'tv/detail/'.$program->chanid
                        .'/'.$program->starttime.'?undo_forget_old=yes">'
                        .t('Undo Forget Old').'</a>';
                }
                if ($can_dupcheck && !in_array($program->recstatus, array('Recorded', 'NeverRecord'))) {
                    echo '<a href="'.root_url.'tv/detail/'.$program->chanid
                        .'/'.$program->starttime.'?never_record=yes"'
                        .'title="'.html_entities(t('info:never record')).'">'
                        .t('Never Record Episode').'</a>';
                }
                if ($program->filename && $program->can_delete) {
                    echo '<a onclick="javascript:confirm_delete(false)"',
                         ' title="',html_entities(t('Delete $1',
                                                    $program->title
                                                    .($show->subtitle
                                                        ? ': '.$show->subtitle
                                                        : '')
                                                 )).'">',
                         t('Delete'), '</a>',
                         '<a onclick="javascript:confirm_delete(true)"',
                         ' title="',html_entities(t('Delete and forget old episode: $1',
                                                    $program->title
                                                    .($show->subtitle
                                                        ? ': '.$show->subtitle
                                                        : '')
                                                 )).'">',
                         t('Delete + Forget Old Episode'), '</a>';
                }
                ?></td>
        </tr><?php
        }
        if ($conflicting_shows && count($conflicting_shows)) {
        ?><tr id="x-conflicts">
            <th><?php echo t('Possible conflicts') ?>:<br><br>
        <div style="text-align: left;">
                <?php echo t('Filters'); ?><br>
            <form id="change_display" name="change_display" action="<?php echo root_url; ?>tv/detail<?php if ($_GET['recordid'])
                             echo '?recordid='.urlencode($_GET['recordid']);
                      else
                         echo '/'.urlencode($_GET['chanid']).'/'.urlencode($_GET['starttime']) ?>" method="post">
            <input type="hidden" name="change_display" value="1">

                <label for="CurrentRecording"><input type="checkbox" id="CurrentRecording" name="CurrentRecording" onclick="$('change_display').submit()" <?php if ($_SESSION['recording_details']['show_CurrentRecording']) echo "CHECKED"; ?>> <?php echo t('Current Recording'); ?></label><br>

                <label for="EarlierShowing"><input type="checkbox" id="EarlierShowing" name="EarlierShowing" onclick="$('change_display').submit()" <?php if ($_SESSION['recording_details']['show_EarlierShowing']) echo "CHECKED"; ?>> <?php echo t('Earlier Showing'); ?></label><br>

                <label for="PreviousRecording"><input type="checkbox" id="PreviousRecording" name="PreviousRecording" onclick="$('change_display').submit()" <?php if ($_SESSION['recording_details']['show_PreviousRecording']) echo "CHECKED"; ?>> <?php echo t('Previous Recording'); ?></label><br>

                <label for="WillRecord"><input type="checkbox" id="WillRecord" name="WillRecord" onclick="$('change_display').submit()" <?php if ($_SESSION['recording_details']['show_WillRecord']) echo "CHECKED"; ?>> <?php echo t('Will Record'); ?></label><br>

                <label for="Conflict"><input type="checkbox" id="Conflict" name="Conflict" onclick="$('change_display').submit()" <?php if ($_SESSION['recording_details']['show_Conflict']) echo "CHECKED"; ?>> <?php echo t('Conflicts'); ?></label><br>
                </div>
        </form>
        </th>
            <td><?php
        // A program id counter for popup info
            $program_id_counter = 0;
            foreach ($conflicting_shows as $show) {
                if (!isset($_SESSION['recording_details']['show_'.$show->recstatus]))
                    continue;
                $program_id_counter++;
            // Print the link to edit this scheduled recording
                echo '<a class="', $show->css_class,
                     '" title="', html_entities(t('$1 to $2',
                                                  strftime($_SESSION['time_format'], $show->starttime),
                                                  strftime($_SESSION['time_format'], $show->endtime))
                                                .', '.($_SESSION["prefer_channum"] ? $show->channel->channum : $show->channel->callsign)
                                                .' - '.$show->channel->name).'"';
                if ($_SESSION["show_popup_info"])
                    echo show_popup("program_$program_id_counter", $show->details_list(), NULL, 'popup');
                echo ' href="'.root_url.'tv/detail/'.$show->chanid.'/'.$show->starttime.'">'
                    .$show->title
                    .(preg_match('/\\w/', $show->subtitle) ? ":  $show->subtitle" : '')
                    .'</a>';
            }
            ?></td>
        </tr><?php
        }
        ?><tr class="x-links">
            <th><?php echo t('More') ?>:</th>
            <td>
<?php           if ($schedule->title) { ?>
                <a href="http://www.themoviedb.org/search/movie?query=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'themoviedb') ?></a>
                <a href="http://www.imdb.com/search/title?title=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'IMDB') ?></a>
                <a href="http://www.thetvdb.com/?string=<?php echo urlencode($schedule->title) ?>&searchseriesid=&tab=listseries&function=Search"><?php echo t('Search $1', 'TheTVDB') ?></a>
                <a href="http://www.tv.com/search.php?type=11&stype=all&qs=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'TV.com') ?></a>
                <a href="http://www.google.com/search?q=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'Google') ?></a>
                <a href="<?php echo root_url ?>tv/search/<?php echo str_replace('%2F', '/', rawurlencode('^'.$schedule->title.'$')) ?>?field=title"><?php
                    if ($_GET['recordid'])
                        echo t('Find showings of this program');
                    else
                        echo t('Find other showings of this program');
                ?></a>
<?php           }
                if ($_GET['recordid']) {
                    echo '<a href="',  root_url, 'tv/schedules">',
                         t('Back to the recording schedules'),
                         '</a>';
                    echo '<a href="', root_url, 'tv/list?time=', $_SESSION['list_time'], '">',
                         t('Back to the program listing'),
                         '</a>';
                }
                else {
                    if ($program->endtime > time()) {
                        echo '<a href="', root_url, 'tv/list?time=', $program->starttime, '">',
                             t('What else is on at this time?'),
                             '</a>';
                    }
                    if ($program->filename) {
                        echo '<a href="', root_url, 'tv/recorded">',
                             t('Back to the recorded programs'),
                             '</a>';
                    }
                    else {
                        echo '<a href="', root_url, 'tv/list?time=', $_SESSION['list_time'], '">',
                             t('Back to the program listing'),
                             '</a>';
                    }
                } ?>
                </td>
        </tr>
        </table>
    </div>

<?php if (!$program || !$program->filename || ($program->filename && $program->recendts > time())) { ?>
    <div id="schedule">
        <form name="program_detail" method="post" action="<?php echo root_url ?>tv/detail<?php
            if ($_GET['recordid'])
                echo '?recordid='.urlencode($_GET['recordid']);
            else
                echo '/'.urlencode($_GET['chanid']).'/'.urlencode($_GET['starttime'])
            ?>">

<?php   if (!$schedule || $schedule->type != rectype_override && $schedule->type != rectype_dontrec) { ?>
        <div class="x-options">
            <h3><?php echo t('Schedule Options') ?>:</h3>
<?php
            if ($program && $schedule) {
                echo '(<a href="'.root_url.'tv/';
            // Link to different places for different kinds of schedules
                if ($schedule->search) {
                    echo 'schedules/',
                         ($schedule->search == searchtype_manual) ? 'manual'
                                                                  : 'custom',
                         '/', $schedule->recordid;
                }
                else
                    echo 'detail?recordid='.$schedule->recordid;
                echo '">'.t('View').'</a>)';
            }
?>
            <ul>
                <li><input type="radio" class="radio" name="record" value="0" id="record_never"<?php
                        if (!$schedule->recordid || $schedule->search) echo ' CHECKED' ?> />
                    <label for="record_never"><?php
                        if ($schedule->search) {
                            echo t('Schedule via $1.',
                                   '<a href='.root_url.'tv/schedules/'
                                   .($schedule->search == searchtype_manual
                                        ? 'manual'
                                        : 'custom'
                                    )
                                   .'/'.$schedule->recordid.'>'
                                   .$schedule->search_title.'</a>');
                        }
                        elseif ($schedule->recordid)
                            echo t('Cancel this schedule.');
                        else
                            echo t('Don\'t record this program.');
                        ?></label></li>
                <li><input type="radio" class="radio" name="record" value="<?php echo rectype_once ?>" id="record_once"<?php
                        echo $schedule->type == rectype_once ? ' CHECKED' : '' ?> />
                    <label for="record_once"><?php echo t('rectype-long: once') ?></label></li>
                <li><input type="radio" class="radio" name="record" value="<?php echo rectype_findone ?>" id="record_findone"<?php
                        echo $schedule->type == rectype_findone ? ' CHECKED' : '' ?> />
                    <label for="record_findone"><?php echo t('rectype-long: findone') ?></label></li>
                <li><input type="radio" class="radio" name="record" value="<?php echo rectype_always ?>" id="record_always"<?php
                        echo $schedule->type == rectype_always ? ' CHECKED' : '' ?> />
                    <label for="record_always"><?php echo t('rectype-long: always') ?></label></li>
            </ul>
        </div>
<?php
        }
        if ($schedule && $schedule->type != rectype_once && ($schedule->search || $schedule->type)) {
?>
        <div class="x-options">
            <h3><?php echo t('Schedule Override') ?>:</h3>
            <ul>
<?php       if ($schedule->type == rectype_override || $schedule->type == rectype_dontrec) { ?>
                <li><input type="radio" class="radio" name="record" value="0" id="schedule_default"<?php
                        if ($schedule->type != rectype_override && $schedule->type != rectype_dontrec) echo ' CHECKED' ?> />
                    <label for="schedule_default"><?php
                        echo t('Schedule normally.') ?></label></li>
<?php       } ?>
                <li><input type="radio" class="radio" name="record" value="<?php echo rectype_override ?>" id="record_override"<?php
                        if ($schedule->type == rectype_override) echo ' CHECKED' ?> />
                    <label for="record_override"><?php
                        echo t('rectype-long: override') ?></label></li>
                <li><input type="radio" class="radio" name="record" value="<?php echo rectype_dontrec ?>" id="record_dontrec"<?php
                        if ($schedule->type == rectype_dontrec) echo ' CHECKED' ?> />
                    <label for="record_dontrec"><?php
                        echo t('rectype-long: dontrec') ?></label></li>

            </ul>
        </div>
<?php      } ?>

        <div class="x-options">
<?php    require_once tmpl_dir.'_advanced_options.php' ?>
        </div>

        <div id="x-schedule_submit">
            <input type="submit" class="submit" name="save" value="<?php echo t('Update Recording Settings') ?>">
        </div>

        </form>

    </div>

<?php
    }
    if ($program && $program->filename) {
?>

<?php require tmpl_dir.'_recording_files.php' ?>

<?php
    }
?>
    </div>
    </div>
<?php

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
