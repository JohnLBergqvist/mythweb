<?php // The programme's header, description, flags and details, as rows of the x-info table ?>
        <tr>
<?php   if ($channel) { ?>
            <td class="x-channel">
                <a href="<?php echo root_url ?>tv/channel/<?php echo $channel->chanid, '/', $program->starttime ?>"
                        title="<?php
                            echo t('Details for: $1',
                                   html_entities($channel->name))
                        ?>">
<?php       if ($_SESSION["show_channel_icons"] == true && !empty($channel->icon)) { ?>
                    <img src="<?php echo $channel->icon ?>">
<?php       } ?>
                <br>
                    <?php echo ($_SESSION["prefer_channum"] ? $channel->callsign : $channel->channum), "\n" ?>
                </a>
            </td>
<?php   } ?>
            <td id="x-title"<?php
                    if (!$channel)
                        echo ' colspan="2"';
                    if ($program && $program->css_class)
                        echo ' class="', $program->css_class, '"';
                    ?>>
                <a href="<?php echo root_url ?>tv/search/<?php echo str_replace('%2F', '/', rawurlencode('^'.$schedule->title.'$')) ?>?field=title"><?php
                    echo $schedule->title;
                    if ($schedule->subtitle)
                        echo ':<br>', $schedule->subtitle;
                    ?></a>
                <div id="x-time"><?php
                echo strftime('%a, %b %e', $schedule->starttime);
                echo ', '
                    .t('$1 to $2', strftime($_SESSION['time_format'], $schedule->starttime),
                                   strftime($_SESSION['time_format'], $schedule->endtime));
                if ($program)
                    echo ' ('.tn('$1 min', '$1 mins', intval(($program->endtime - $program->starttime)/60)).')';
                echo "<br>\n";
                ?></div>
                </td>
        </tr><?php
        if (!empty($schedule->fancy_description)) {
        ?><tr>
            <td id="x-description" colspan="2">
                <?php echo nl2br($schedule->fancy_description) ?>
            </td>
        </tr><?php
        }
        if ($program) {
        ?><tr id="x-progflags">
            <td colspan="2">
			<?php if (setting('recommend_enabled', null) && strlen($program->inetref) > 0) { ?>
				<div id="feelings" inetref="<?php echo $program->inetref; ?>">
					<div inetref="<?php echo $program->inetref; ?>" class="dislike feeling"></div>
					<div inetref="<?php echo $program->inetref; ?>" class="meh feeling"></div>
					<div inetref="<?php echo $program->inetref; ?>" class="like feeling"></div>
				</div>
			<?php } ?>
			
			<?php
        // Auto expire is interactive for recordings
            if ($program->filename) {
                echo '<a onclick="set_autoexpire()">',
                     '<img id="autoexpire" src="', skin_url, '/img/flags/';
                if ($program->auto_expire)
                    echo 'autoexpire.png" title="', t('Click to disable Auto Expire'), '"';
                else
                    echo 'no_autoexpire.png" title="', t('Click to enable Auto Expire'), '"';
                echo '></a>';
            }
            elseif ($program->auto_expire) {
                echo '<img src="', skin_url, '/img/flags/autoexpire.png" title="', t('Auto Expire'), '">';
            }
        // The rest of the flags are just for display
            if ($program->closecaptioned)
                echo '<img src="'.skin_url.'/img/flags/cc.png" title="'.t('Closed Captioning').'">';
            if ($program->stereo)
                echo '<img src="'.skin_url.'/img/flags/stereo.png" title="'.t('Stereo').'">';
            if ($program->hdtv)
                echo '<img src="'.skin_url.'/img/flags/hd.png" title="'.t('HD').'">';
            if ($program->hd_ready)
                echo '<img src="'.skin_url.'/img/flags/hd720.png" height=18 title="'.t('720').'">';
            if ($program->fullhd)
                echo '<img src="'.skin_url.'/img/flags/hd1080.png" height=18 title="'.t('1080').'">';
            if ($program->damaged)
                echo '<img src="'.skin_url.'/img/flags/damaged.png" title="'.t('Damaged').'">';
            if ($program->has_commflag)
                echo '<img src="'.skin_url.'/img/flags/commflagged.png" title="'.t('Commercials Flagged').'">';
            if ($program->has_cutlist)
                echo '<img src="'.skin_url.'/img/flags/cutlist.png" title="'.t('Has Cutlist').'">';
            if ($program->bookmark)
                echo '<img src="'.skin_url.'/img/flags/bookmark.png" title="'.t('has Bookmark').'">';
            if ($program->is_watched)
                echo '<img src="'.skin_url.'/img/flags/watched.png" title="'.t('Watched').'">';

            // Report transcoded status for recordings
            if ($program->filename) {
                if ($program->is_transcoded)
                    echo '<img src="'.skin_url.'/img/flags/transcoded.png" title="'.t('Transcoded').'">';
            }
            ?>
			</td>
        </tr><?php
            if (strlen($program->category)) {
        ?><tr class="x-extras">
            <th><?php echo t('Category') ?>:</th>
            <td><?php echo $program->category ?></td>
        </tr><?php
            }
            if (strlen($program->category_type)) {
        ?><tr class="x-extras">
            <th><?php echo t('Type') ?>:</th>
            <td><?php echo $program->category_type;
                          if ($program->seriesid)
                              echo ' (', $program->seriesid, ')' ?></td>
        </tr><?php
            }
            if (strlen($program->season_episode()) > 0) {
        ?><tr class="x-extras">
            <th><?php echo t('Seas/Ep') ?>:</th>
            <td><?php echo html_entities($program->season_episode()) ?></td>
        </tr><?php
            }
            if (strlen($program->inetref) > 0) {
        ?><tr class="x-extras">
            <th><?php echo t('Internet Reference #') ?>:</th>
            <td><?php echo $program->inetref ?> <span class="commands" id="metadata-home-page"></span></td>
        </tr><?php
            }
            if (strlen($program->airdate)) {
        ?><tr class="x-extras">
            <th><?php echo t('Original Airdate') ?>:</th>
            <td><?php echo $program->airdate ?></td>
        </tr><?php
            }
            if (strlen($program->programid) > 0) {
        ?><tr class="x-extras">
            <th><?php echo t('Program ID') ?>:</th>
            <td><?php echo $program->programid ?></td>
        </tr><?php
            }
            if ($program->get_credits('host', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Hosted by') ?>:</th>
            <td><?php echo $program->get_credits('host', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('presenter', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Presented by') ?>:</th>
            <td><?php echo $program->get_credits('presenter', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('actor', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Cast') ?>:</th>
            <td><?php echo $program->get_credits('actor', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('guest_star', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Guest Starring') ?>:</th>
            <td><?php echo $program->get_credits('guest_star', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('director', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Directed by') ?>:</th>
            <td><?php echo $program->get_credits('director', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('producer', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Produced by') ?>:</th>
            <td><?php echo $program->get_credits('producer', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('executive_producer', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Exec. Producer') ?>:</th>
            <td><?php echo $program->get_credits('executive_producer', TRUE) ?></td>
        </tr><?php
            }
            if ($program->get_credits('writer', TRUE)) {
        ?><tr class="x-extras">
            <th><?php echo t('Written by') ?>:</th>
            <td><?php echo $program->get_credits('writer', TRUE) ?></td>
        </tr><?php
            }
            if (strlen($program->starstring) > 0) {
        ?><tr class="x-extras">
            <th><?php echo t('Guide rating') ?>:</th>
            <td><?php echo $program->starstring ?></td>
        </tr><?php
            }
        ?><tr class="x-extras">
            <th><?php echo t('Length') ?>:</th>
            <td><?php echo nice_length($program->endtime - $program->starttime) ?></td>
        </tr><?php
            if ($program->filename) {
        ?><tr class="x-extras">
            <th><?php echo t('File Length') ?>:</th>
            <td><?php echo nice_length($program->length), ' (',
                           t('$1 to $2', strftime($_SESSION['time_format'], $program->recstartts),
                                         strftime($_SESSION['time_format'], $program->recendts)), ')' ?></td>
        </tr>
        <tr class="x-extras">
            <th><?php echo t('File Size') ?>:</th>
            <td><?php echo nice_filesize($program->filesize) ?></td>
        </tr><?php
            }
            if ($program->inputname) {
        ?><tr class="x-extras">
            <th><?php echo t('Input Name') ?>:</th>
            <td><?php echo $program->inputname ?></td>
        </tr><?php
            }
            if (strlen($program->recgroup)) {
        ?><tr class="x-extras">
            <th><?php echo t('Recording Group') ?>:</th>
            <td><?php echo $program->recgroup ?></td>
        </tr><?php
            }
            if (strlen($program->playgroup)) {
        ?><tr class="x-extras">
            <th><?php echo t('Playback Group') ?>:</th>
            <td><?php echo $program->playgroup ?></td>
        </tr><?php
            }
        }
