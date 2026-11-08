<?php
/**
 * Upcoming recordings
 *
 * @license     GPL
 *
 * @package     MythWeb
 *
 **/

        $page_title = 'MythWeb - '.t('Upcoming Recordings');
        require_once 'modules/_shared/tmpl/'.tmpl.'/header.php';

    // Print the page contents
        global $all_shows, $forgotten_episodes;

    // Row headers
        echo "\n    <hr />\n",
             "    Sort by:<br />\n",
             get_sort_link('title',   t('title')),   "\n",
             get_sort_link('channum', t('channum')), "\n",
             get_sort_link('airdate', t('airdate')), "\n",
             get_sort_link('length',  t('length')),  "\n",
             "    <table>\n";

        $row = 0;
        foreach ($all_shows as $show) {
            $row++;
            // Set the class to be used to display the recording status character
            $rec_class = implode(' ', array(recstatus_class($show), $show->recstatus));
            // Reset the command variable to a default URL
            $commands = array();
            $urlstr = 'chanid='.$show->chanid.'&starttime='.$show->starttime;
            // Set the recording status character, class and any applicable commands for each show
            switch ($show->recstatus) {
                case 'Recording':
                case 'WillRecord':
                    $rec_char   = $show->inputname;
                    $css_class  = 'scheduled';
                    $commands[] = '<a href="tv/upcoming?dontrec=yes&'
                                  .$urlstr.'">'.t('Don\'t Record Showing').'</a>';
                    // Offer to suppress any recordings that have enough info to do so.
                    if (preg_match('/\\S/', $show->title)
                            && (preg_match('/\\S/', $show->programid.$show->subtitle.$show->description))) {
                        $commands[] = '<a href="tv/upcoming?'
                                      .'never_record=yes&'.$urlstr.'">'
                                      .t('Never Record Episode').'</a>';
                    }
                // Offer to undo a Forget Old that let this episode record again
                    if (isset($forgotten_episodes[Program::episode_key($show->title, $show->programid,
                                                                       $show->subtitle, $show->description)]))
                        $commands[] = '<a href="tv/upcoming?undo_forget_old=yes&'
                                      .$urlstr.'">'.t('Undo Forget Old').'</a>';
                    break;
                case 'PreviousRecording':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'duplicate';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?'
                                  .'forget_old=yes&'.$urlstr.'">'
                                  .t('Forget Old Episode').'</a>';
                    break;
                case 'CurrentRecording':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'duplicate';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?'
                                  .'forget_old=yes&'.$urlstr.'">'
                                  .t('Forget Old Episode').'</a>';
                    break;
                case 'Repeat':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class= 'duplicate';
                    break;
                case 'EarlierShowing':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class= 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Default').'</a>';
                    break;
                case 'TooManyRecordings':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class= 'deactivated';
                    break;
                case 'NeverRecord':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?forget_old=yes&'
                                  .$urlstr.'">'.t('Undo Never Record').'</a>';
                    break;
                case 'DontRecord':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Schedule Normally').'</a>';
                    break;
                case 'Cancelled':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Default').'</a>';
                    break;
                case 'Conflict':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    // We normally use the recstatus value as the name of the class
                    //  used when displaying the recording status character.
                    // However, there is already a class named 'conflict' so we
                    //  need to modify this specific recstatus to avoid a conflict.
                    $rec_class = implode(' ', array(recstatus_class($show),
                                         'conflicting'));
                    $css_class  = 'conflict';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?dontrec=yes&'
                                  .$urlstr.'">'.t('Don\'t Record Showing').'</a>';
                    break;
                case 'LaterShowing':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class= 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Default').'</a>';
                    break;
                case 'LowDiskSpace':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'deactivated';
                    $commands[] = 'Not Enough Disk Space';
                    break;
                case 'TunerBusy':
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus];
                    $css_class  = 'deactivated';
                    $commands[] = 'Tuner is busy';
                    break;
                case 'Overlap':
                    $rec_char   = t('Override');
                    $css_class  = 'conflict';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?dontrec=yes&'
                                  .$urlstr.'">'.t('Don\'t Record Showing').'</a>';
                    break;
                case 'ManualOverride':
                    $rec_char   = t('Override');
                    $css_class  = 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Default').'</a>';
                    break;
                case 'ForceRecord':
                    $rec_char   = $show->inputname ? $show->inputname : t('Forced');
                    $css_class  = 'scheduled';
                    $commands[] = '<a href="tv/upcoming?dontrec=yes&'
                                  .$urlstr.'">'.t('Don\'t Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?default=yes&'
                                  .$urlstr.'">'.t('Schedule Normally').'</a>';
                    break;
                default:
                    $rec_char   = $GLOBALS['RecStatus_Names'][$show->recstatus] ?? '&nbsp;';
                    $rec_class  = '';
                    $css_class  = 'deactivated';
                    $commands[] = '<a href="tv/upcoming?record=yes&'
                                  .$urlstr.'">'.t('Force Record Showing').'</a>';
                    $commands[] = '<a href="tv/upcoming?dontrec=yes&'
                                  .$urlstr.'">'.t('Don\'t Record Showing').'</a>';
                    break;
            }
?>
    <tr class='<?php echo $css_class ?>'><td><?php echo $rec_char ?></td><td>
    <?php
        // Print a link to record this show
            echo '<a id="program_'.$program_id_counter.'_anchor" href="tv/detail/'.$show->chanid.'/'.$show->starttime.'"'
                 .'>'.$show->title
                 .(preg_match('/\\w/', $show->subtitle) ? ":  $show->subtitle" : '')
                 .'</a></td></tr>';
        ?>
    <tr><td></td><td>
      <?php echo $show->channel->name ?><br />
      <?php echo date('D, M j', $show->starttime) ?>
      <?php echo '('.strftime($_SESSION['time_format'], $show->starttime).')' ?> <?php echo nice_length($show->length) ?></td></tr>
      <tr><td></td><td>
<?php
            echo implode(' | ', $commands);
            $prev_group = $cur_group;
            $row++;
      }
      echo "    </table>\n";

// Print the main page footer
      require_once 'modules/_shared/tmpl/'.tmpl.'/footer.php';
