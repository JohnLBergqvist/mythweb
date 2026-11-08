<?php // JavaScript for a recording's auto-expire, delete, pop-out player and frontend playback ?>
// Keep track of the autoexpire flag
    var autoexpire = <?php echo $program->auto_expire ? 1 : 0 ?>;

// Set the autoexpire flag
    function set_autoexpire() {
        var r = new Ajax.Request('<?php echo $recording_page_url ?>',
                                 {
                                    parameters: 'toggle_autoexpire='+(1 - autoexpire),
                                  asynchronous: false
                                 });
        if (r.transport.responseText == 'success') {
        // Update to the new value
            autoexpire = 1 - autoexpire;
        // Fix the images
            $('autoexpire').src = '<?php echo skin_url, '/img/flags/' ?>'
                                  + (autoexpire
                                     ? ''
                                     : 'no_')
                                  + 'autoexpire.png';
            if (autoexpire)
                $('autoexpire').title = '<?php echo addslashes(t('Click to disable Auto Expire')) ?>';
            else
                $('autoexpire').title = '<?php echo addslashes(t('Click to enable Auto Expire')) ?>';
        }
        else if (r.transport.responseText) {
            alert('Error: '+r.transport.responseText);
        }
    }

    function confirm_delete(forget_old) {
        if (confirm("<?php echo str_replace('"', '\\"',
                                            t('Are you sure you want to delete the following show?')
                                            .'\n\n     '
                                            .$program->title
                                            .($program->subtitle
                                              ? ': '.$program->subtitle
                                              : '')) ?>")) {
            location.href = '<?php echo root_url ?>tv/recorded?delete=yes&chanid=<?php
                            echo $program->chanid
                            ?>&starttime=<?php echo $program->recstartts ?>'
                            +(forget_old
                                ? '&forget_old=yes'
                                : '');
        }
    }

    function openFlashPlayerInNewWindow() {
        player = window.open('', 'Flash Player', 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,width=<?php echo $flv_w; ?>,height=<?php echo $flv_h; ?>,left=20,top=20');
        player.document.write('<html><body style="background-color: black; margin: 0px; padding: 0px;">'+$$('.x-pixmap')[0].textContent+'</body></html>');
    }

    function watchShow(host, chanid, starttime) {
        new Ajax.Request('<?php echo root_url;?>remote/play_program_on_frontend', {
                         method: 'get',
                         parameters: {exit: 1,
                                      host: host,
                                      chanid: chanid,
                                      starttime: starttime
                                      }
                        }
                        );
    }
