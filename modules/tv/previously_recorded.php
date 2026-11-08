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

// Change whether one entry counts as recorded, as the backend's UpdateOldRecorded does
    if (isset($_REQUEST['duplicate'])) {
        $db->query('UPDATE oldrecorded
                       SET duplicate = ?
                     WHERE chanid    = ?
                       AND starttime = FROM_UNIXTIME(?)',
                   $_REQUEST['duplicate'] ? 1 : 0,
                   $_REQUEST['chanid'],
                   $_REQUEST['starttime']);
    // Notify the backend of the changes
        MythBackend::find()->rescheduleRecording();
    // Redirect back to the page again, but without the query string, so reloads are cleaner
        redirect_browser(root_url.'tv/previously_recorded');
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
        redirect_browser(root_url.'tv/previously_recorded');
    }

// Load the history, newest first
    $history = $db->query_list_assoc('SELECT chanid, station, title, subtitle, programid,
                                             recstatus, duplicate,
                                             UNIX_TIMESTAMP(starttime) AS starttime
                                        FROM oldrecorded
                                    ORDER BY starttime DESC, title');

// Display the page
    require_once tmpl_dir.'previously_recorded.php';

// Exit
    exit;
