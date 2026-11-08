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

// Allow the entry's episode to be recorded again, as Forget Old does, undo
// that, or mark it never to record, as Never Record Episode does
    if (isset($_REQUEST['duplicate']) || isset($_REQUEST['undo_forget_old'])) {
        $row = $db->query_assoc('SELECT *
                                   FROM oldrecorded
                                  WHERE chanid    = ?
                                    AND starttime = FROM_UNIXTIME(?)
                                    AND future    = 0',
                                $_REQUEST['chanid'],
                                $_REQUEST['starttime']);
        if ($row) {
            $program = new Program($row);
            if (isset($_REQUEST['undo_forget_old']))
                $program->rec_undo_forget_old();
            elseif ($_REQUEST['duplicate'])
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
// guide and the ID of its recording if that still exists.  A kept recording's
// season and episode, which a metadata lookup may have corrected, replace the
// history's.  The scheduler's own notes about upcoming showings are left out.
    $history = $db->query_list_assoc('SELECT o.chanid, o.station, o.title, o.subtitle, o.programid,
                                             COALESCE((SELECT r.season FROM recorded r
                                                        WHERE r.chanid    = o.chanid
                                                          AND r.progstart = o.starttime
                                                          AND r.recgroup NOT IN ("Deleted", "LiveTV")
                                                          AND r.episode > 0
                                                        LIMIT 1), o.season) AS season,
                                             COALESCE((SELECT r.episode FROM recorded r
                                                        WHERE r.chanid    = o.chanid
                                                          AND r.progstart = o.starttime
                                                          AND r.recgroup NOT IN ("Deleted", "LiveTV")
                                                          AND r.episode > 0
                                                        LIMIT 1), o.episode) AS episode,
                                             COALESCE((SELECT rp.syndicatedepisodenumber FROM recorded r
                                                         JOIN recordedprogram rp
                                                           ON rp.chanid    = r.chanid
                                                          AND rp.starttime = r.progstart
                                                        WHERE r.chanid    = o.chanid
                                                          AND r.progstart = o.starttime
                                                          AND r.recgroup NOT IN ("Deleted", "LiveTV")
                                                        LIMIT 1),
                                                      (SELECT p.syndicatedepisodenumber FROM program p
                                                        WHERE p.chanid    = o.chanid
                                                          AND p.starttime = o.starttime
                                                        LIMIT 1), "") AS syndicatedepisodenumber,
                                             o.recstatus, o.duplicate,
                                             UNIX_TIMESTAMP(o.starttime) AS starttime,
                                             EXISTS(SELECT 1 FROM program p
                                                     WHERE p.chanid    = o.chanid
                                                       AND p.starttime = o.starttime) AS in_guide,
                                             (SELECT r.recordedid FROM recorded r
                                               WHERE r.chanid    = o.chanid
                                                 AND r.progstart = o.starttime
                                                 AND r.recgroup NOT IN ("Deleted", "LiveTV")
                                               LIMIT 1) AS recordedid
                                        FROM oldrecorded o
                                       WHERE o.future = 0
                                    ORDER BY o.starttime DESC, o.title');

// Display the page
    require_once tmpl_dir.'recording_history.php';

// Exit
    exit;
