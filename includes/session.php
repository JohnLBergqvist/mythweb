<?php
/*
 * $Date$
 * $Revision$
 * $Author$
 *
 * session.php
 *
 *   This file handles all of the basic session-related information.
 *
 *   It also uses the global variable $db, so it must be called from init.php
 *   *after* the database connection has been established.
/*/

// Start the session
    session_name('mythweb_id');
    session_set_cookie_params(365 * 24 * 60 * 60, '/', server_domain);    // sessions should last for a year
    session_set_save_handler('sess_do_nothing', 'sess_do_nothing', 'sess_read', 'sess_write', 'sess_destroy', 'sess_gc');
    ini_set('session.gc_maxlifetime', 60 * 60 * 30);    // 30 day timeout
    session_start();

/*
 *  The functions defined below are referenced above in session_set_save_handler()
/*/

// We don't actually have to do anything for open and close, since we connected to the database in init.php
    function sess_do_nothing() {
        return true;
    }

// Read the session data from the database
    function sess_read($id) {
        global $db;
        $sh = $db->query('SELECT data FROM mythweb_sessions WHERE id=?', $id);
        list($data) = $sh->fetch_row($result);
        $sh->finish();
        if ($data)
            return $data;
        return '';
    }

// Write the session data to the database
    function sess_write($id, $data) {
        global $db;
        $db->query('REPLACE INTO mythweb_sessions (id, modified, data) VALUES(?,NULL,?)',
                   $id, $data);
        if (!$db->affected_rows())
            return false;
    // Return true
        return true;
    }

// Destroy the session
    function sess_destroy($id) {
        global $db;
        $db->query('DELETE FROM mythweb_sessions WHERE id=?', $id);
        if (!$db->affected_rows())
            return true;
        return false;
    }

// Clear out any old sessions (we override $maxlifetime with our own variable)
    function sess_gc($maxlifetime) {
        global $db;
        $db->query('DELETE FROM mythweb_sessions WHERE NOW() > DATE_ADD(modified, INTERVAL ? SECOND)',
                   $maxlifetime);
        return true;
    }
