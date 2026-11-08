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

// The recording is identified by its recordedid, as in tv/recording/<recordedid>
    $recordedid = intVal($Path[2]);

// The page's own address, for its auto-expire, job and forget requests
    $recording_page_url = root_url.'tv/recording/'.$recordedid;

// Load the recording from the backend's list of recordings, which also says
// whether the file is still being written
    $program = null;
    foreach (MythBackend::find()->queryProgramRows('QUERY_RECORDINGS Unsorted') as $row) {
        if ($row[49] == $recordedid) {
            $program = new Program($row);
            break;
        }
    }

// Unknown recording?  Forward the user back to the recorded programs
    if (!$program) {
        add_warning(t('Unknown Program.'));
        save_session_errors();
        header('Location: '.root_url.'tv/recorded');
        exit;
    }

// A file still being written is recording, not recorded
    if ($program->is_recording) {
        $program->recstatus = 'Recording';
        $program->css_class = category_class($program);
    }

// Auto-expire -- only available for javascript templates
    if (isset($_REQUEST['toggle_autoexpire'])) {
        $sh = $db->query('UPDATE recorded
                             SET autoexpire = ?
                           WHERE recordedid = ?',
                         $_REQUEST['toggle_autoexpire'] ? 1 : 0,
                         $recordedid);
    // Report back, and then exit.
        if ($sh->affected_rows())
            echo 'success';
        else
            echo "Failed to update the database for recording $recordedid!";
        exit;
    }

// Load the jobqueue info
    require_once 'includes/jobqueue.php';

// Queue a job?
    if ($_REQUEST['job']) {
        $host = "";
        if (setting("JobsRunOnRecordHost")){
            $host = $program->hostname;
        }
        $db->query('INSERT INTO jobqueue
                       SET chanid       = ?,
                           starttime    = FROM_UNIXTIME(?),
                           inserttime   = NOW(),
                           type         = ?,
                           hostname     = ?,
                           args         = "",
                           status       = ?,
                           statustime   = NOW(),
                           schedruntime = NOW(),
                           comment      = "Queued via MythWeb",
                           flags        = ?',
                   $program->chanid,
                   $program->recstartts,
                   $_REQUEST['job'],
                   $host,
                   JOB_QUEUED,
                   JOB_USE_CUTLIST
                  );
        MythBackend::find()->rescheduleRecording();
    // Redirect back to the page again, but without the query string, so the
    // user doesn't accidentally repost this request on a page reload.
        redirect_browser($recording_page_url);
    }

// Forget the episode's history, or undo that, as the details page does
    if ($_REQUEST['forget_old'] || $_REQUEST['undo_forget_old']) {
        if ($_REQUEST['forget_old'])
            $program->rec_forget_old();
        else
            $program->rec_undo_forget_old();
    // Redirect back to the page again, but without the query string, so reloads are cleaner
        redirect_browser($recording_page_url);
    }

// The programme information templates describe the programme through a
// schedule object, so fill one in from the recording
    $schedule = new Schedule(NULL);
    $schedule->chanid            = $program->chanid;
    $schedule->starttime         = $program->starttime;
    $schedule->endtime           = $program->endtime;
    $schedule->title             = $program->title;
    $schedule->subtitle          = $program->subtitle;
    $schedule->description       = $program->description;
    $schedule->fancy_description = $program->fancy_description;
    $schedule->category          = $program->category;
    $schedule->seriesid          = $program->seriesid;
    $schedule->programid         = $program->programid;

// Load the channel
    $channel =& $program->channel;

// Is the broadcast still in the programme guide, so its details page can be linked to?
    $in_guide = $db->query_col('SELECT COUNT(*)
                                  FROM program
                                 WHERE chanid = ? AND starttime = FROM_UNIXTIME(?)',
                               $program->chanid,
                               $program->starttime) > 0;

// Load the jobqueue info before displaying
    $program->load_jobs();

// Display the page
    require_once tmpl_dir.'recording.php';

// Exit
    exit;
