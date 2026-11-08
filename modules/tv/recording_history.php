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

// Allow the entry's episode to be recorded again, as Forget Old does, or
// mark it never to record, as Never Record Episode does
    if (isset($_REQUEST['duplicate'])) {
        $row = $db->query_assoc('SELECT *
                                   FROM oldrecorded
                                  WHERE chanid    = ?
                                    AND starttime = FROM_UNIXTIME(?)
                                    AND future    = 0',
                                $_REQUEST['chanid'],
                                $_REQUEST['starttime']);
        if ($row) {
            $program = new Program($row);
            if ($_REQUEST['duplicate'])
                $program->rec_never_record();
            else
                $program->rec_forget_old();
        }
    // Redirect back to the page again, but without the query string, so reloads are cleaner
        redirect_browser(root_url.'tv/recording_history');
    }

// Remove one entry from the history, as the backend's RemoveOldRecorded does
    if (isset($_REQUEST['remove'])) {
        $db->query('DELETE FROM oldrecorded
                     WHERE chanid    = ?
                       AND starttime = FROM_UNIXTIME(?)',
                   $_REQUEST['chanid'],
                   $_REQUEST['starttime']);
    // Notify the backend of the changes
        MythBackend::find()->rescheduleRecording();
    // Redirect back to the page again, but without the query string, so reloads are cleaner
        redirect_browser(root_url.'tv/recording_history');
    }

// Load the history, newest first, with whether each showing is still in the
// guide and the start time of its recording if that still exists.  The
// scheduler's own notes about upcoming showings are left out.
    $history = $db->query_list_assoc('SELECT o.chanid, o.station, o.title, o.subtitle, o.programid,
                                             o.recstatus, o.duplicate,
                                             UNIX_TIMESTAMP(o.starttime) AS starttime,
                                             EXISTS(SELECT 1 FROM program p
                                                     WHERE p.chanid    = o.chanid
                                                       AND p.starttime = o.starttime) AS in_guide,
                                             (SELECT UNIX_TIMESTAMP(r.starttime) FROM recorded r
                                               WHERE r.chanid    = o.chanid
                                                 AND r.progstart = o.starttime
                                                 AND r.recgroup NOT IN ("Deleted", "LiveTV")
                                               LIMIT 1) AS recstartts
                                        FROM oldrecorded o
                                       WHERE o.future = 0
                                    ORDER BY o.starttime DESC, o.title');

// Display the page
    require_once tmpl_dir.'recording_history.php';

// Exit
    exit;
