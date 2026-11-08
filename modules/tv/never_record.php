<?php
/**
 * Lists the episodes marked never to record, with a command to lift each mark.
 *
 * @license     GPL
 *
 * @package     MythWeb
 * @subpackage  TV
 *
 **/

// Lift one never record mark?  The history table's key is the station, start time and title.
    if ($_REQUEST['schedule_normally']) {
        $db->query('DELETE FROM oldrecorded
                     WHERE recstatus = 11
                       AND station   = ?
                       AND starttime = FROM_UNIXTIME(?)
                       AND title     = ?',
                   $_REQUEST['station'],
                   $_REQUEST['starttime'],
                   $_REQUEST['title']);
    // Notify the backend of the changes
        MythBackend::find()->rescheduleRecording();
    // Redirect back to the page again, but without the query string, so reloads are cleaner
        redirect_browser(root_url.'tv/never_record');
    }

// Load the episodes marked never to record
    $never_records = $db->query_list_assoc('SELECT title, subtitle, programid, station,
                                                   UNIX_TIMESTAMP(starttime) AS starttime
                                              FROM oldrecorded
                                             WHERE recstatus = 11
                                          ORDER BY title, subtitle');

// Display the page
    require_once tmpl_dir.'never_record.php';

// Exit
    exit;
