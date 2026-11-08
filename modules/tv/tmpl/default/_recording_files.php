<?php // A recording's preview, downloads, jobs and frontends, inside the x-downloads div ?>
        <div id="x-downloads">

            <div class="x-pixmap">
<?php   if (setting('WebFLV_on')) { ?>
<?php       if (file_exists('js/libs/flowplayer/flowplayer.swf')) { ?>


          <!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
            <a href=""
                style="display:block;width:<?php echo $flv_w ?>px;height:<?php echo $flv_h ?>px"
                id="player">
            </a>

            <!-- this will install flowplayer inside previous A- tag. -->
            <script>
                flowplayer(
                    "player",
                    "<?php echo root_url ?>js/libs/flowplayer/flowplayer.swf", {
                    playlist: [
                        // this first PNG clip works as a splash image
                        {
                            url: '<?php echo $program->thumb_url($flv_w,0) ?>',
                            scaling: 'orig'
                            },
                        // Then we have the video
                        {
                            url: "<?php echo video_url($program, 'flv'); ?>",
                            duration: <?php echo $program->length ?>,
                            autoPlay: false,
                            scaling: 'fit',
                            // Would be nice to auto-buffer, but we don't want to
                            // waste bandwidth and CPU on the remote machine.
                            autoBuffering: false
                            }
                        ]}
                    );
            </script>
<?php       } elseif (file_exists('modules/tv/MFPlayer.swf')) { ?>
                    <script language="JavaScript" type="text/javascript">
                    <!--
                    // Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
                    var hasProductInstall = DetectFlashVer(6, 0, 65);

                    // Version check based upon the values defined in globals
                    var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

                    // Check to see if a player with Flash Product Install is available and the version does not meet the requirements for playback
                    if ( hasProductInstall && !hasRequestedVersion ) {
                        // MMdoctitle is the stored document.title value used by
                        // the installation process to close the window that
                        // started the process.   This is necessary in order to
                        // close browser windows that are still utilizing the
                        // older version of the player after installation has
                        // completed

                        // DO NOT MODIFY THE FOLLOWING FOUR LINES
                        // Location visited after installation is complete if
                        // installation is required
                        var MMPlayerType  = (isIE == true) ? "ActiveX" : "PlugIn";
                        var MMredirectURL = window.location;
                        var MMdoctitle    = document.title;
                        document.title    = document.title.slice(0, 47)
                                            +" - Flash Player Installation";

                        AC_FL_RunContent(
                                "src",              "playerProductInstall",
                                "FlashVars",        "MMredirectURL="+MMredirectURL
                                                    +'&MMplayerType='+MMPlayerType
                                                    +'&MMdoctitle='+MMdoctitle,
                                "width",            "<?php echo $flv_w ?>",
                                "height",           "<?php echo $flv_h ?>",
                                "align",            "middle",
                                "id",               "MFPlayer",
                                "quality",          "high",
                                "bgcolor",          "#869ca7",
                                "name",             "MFPlayer",
                                "allowScriptAccess","sameDomain",
                                "movie",            "<?php echo root_url; ?>tv/playerProductInstall",
                                "type",             "application/x-shockwave-flash",
                                "pluginspage",      "http://www.adobe.com/go/getflashplayer"
                            );
                    } else if (hasRequestedVersion) {
                        // If we've detected an acceptable version, embed
                        // the Flash Content SWF when all tests are passed
                        AC_FL_RunContent(
                                "src",              "MFPlayer",
                                "width",            "<?php echo $flv_w ?>",
                                "height",           "<?php echo $flv_h ?>",
                                "align",            "middle",
                                "id",               "MFPlayer",
                                "quality",          "high",
                                "bgcolor",          "#869ca7",
                                "name",             "MFPlayer",
                                "flashvars",'file=<?php     echo video_url($program, 'flv');
                                         ?>&still=<?php     echo $program->thumb_url($flv_w,$flv_h);
                                         ?>&totalTime=<?php echo $program->length;
                                         ?>&width=<?php     echo $flv_w;
                                         ?>&height=<?php    echo $flv_h;
                                         ?>&styles=<?php    echo root_url ?>tv/MFPlayer_styles.swf',
                                "allowScriptAccess","sameDomain",
                                "allowFullScreen",  "true",
                                "movie",            "<?php echo root_url; ?>tv/MFPlayer",
                                "type",             "application/x-shockwave-flash",
                                "pluginspage",      "http://www.adobe.com/go/getflashplayer"
                            );
                    } else {  // flash is too old or we can't detect the plugin
                        document.write('<img src="<?php echo $program->thumb_url($flv_w,0) ?>"'
                                      +' width="<?php echo $flv_w ?>"><br>'
                                      +'Web-based video playback requires the '
                                      +'<a href=http://www.adobe.com/go/getflash/>Adobe Flash Player</a>.'
                                      );
                    }
                    // -->
                    </script>
                    <noscript>
                    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                        id="MFPlayer" width="<?php echo $flv_w ?>" height="<?php echo $flv_h ?>"
                        codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
                        <param name="movie" value="<?php echo root_url; ?>tv/MFPlayer.swf" />
                        <param name="quality" value="high" />
                        <param name="bgcolor" value="#869ca7" />
                        <param name="allowScriptAccess" value="sameDomain" />
                        <param name="allowFullScreen" value="true" />
                        <param name="FlashVars" value="file=<?php     echo video_url($program, 'flv');
                                                   ?>&still=<?php     echo $program->thumb_url($flv_w,$flv_h);
                                                   ?>&totalTime=<?php echo $program->length;
                                                   ?>&width=<?php     echo $flv_w;
                                                   ?>&height=<?php    echo $flv_h;
                                                   ?>&styles=<?php    echo root_url ?>tv/MFPlayer_styles.swf"
                                                   >
                        <embed src="<?php echo root_url; ?>tv/MFPlayer.swf" quality="high" bgcolor="#869ca7"
                            width="<?php echo $flv_w ?>" height="<?php echo $flv_h ?>" name="MFPlayer" align="middle"
                            play="true"
                            loop="false"
                            quality="high"
                            allowScriptAccess="sameDomain"
                            allowFullScreen="true"
                            FlashVars="file=<?php     echo video_url($program, 'flv');
                                   ?>&still=<?php     echo $program->thumb_url($flv_w,0);
                                   ?>&totalTime=<?php echo $program->length;
                                   ?>&width=<?php     echo $flv_w;
                                   ?>&height=<?php    echo $flv_h;
                                   ?>&styles=<?php    echo root_url ?>tv/MFPlayer_styles.swf"
                            type="application/x-shockwave-flash"
                            pluginspage="http://www.adobe.com/go/getflashplayer">
                        </embed>
                    </object>
                    </noscript>
<?php       } ?>
<?php   } else { ?>
                <a href="<?php echo $program->url ?>" title="<?php echo t('Direct Download') ?>"
                    ><img src="<?php echo $program->thumb_url($flv_w,0) ?>" width="<?php echo $flv_w ?>"></a>
<?php   } ?></td>
            </div>
            <div class="x-links">
                <a href="<?php echo video_url($program, 'asx') ?>" title="<?php echo t('ASX Stream') ?>"
                    ><img src="<?php echo skin_url ?>/img/play_sm.png">
                    <?php echo t('ASX Stream') ?></a>
                <a href="<?php echo $program->url ?>" title="<?php echo t('Direct Download') ?>"
                    ><img src="<?php echo skin_url ?>/img/video_sm.png">
                    <?php echo t('Direct Download') ?></a>
                <?php if (setting('WebFLV_on') && file_exists('modules/tv/MFPlayer.swf')) { ?>
                    <a onclick="openFlashPlayerInNewWindow(); return false;" title="<?php echo t('Pop-out player'); ?>">
                        <?php echo t('Pop-out player'); ?>
                    </a>
                <?php } ?>
            </div>
            <div class="x-jobs">
<?php
        if ($program->jobs_possible && count($program->jobs_possible)) {
            echo t('Queue a job'), ':',
                 '            <ul class="x-queue">';
            foreach ($program->jobs_possible as $id => $job) {
                echo '                <li>',
                     '<a href="',
                     $recording_page_url,
                     '?job=', $id,
                     '">', $job, "</a></li>";
            }
            echo '            </ul>';
        }
        if (is_array($program->jobs['queue']) && count($program->jobs['queue'])) {
            echo t('Queued jobs'), ':',
                 '            <ul class="-queued">';
            foreach ($program->jobs['queue'] as $job) {
                echo '                <li>',
                     $Jobs[$job['type']],
                     ' (', $Job_Status[$job['status']],
                     ':  ', strftime($_SESSION['date_listing_key'], $job['statustime']),
                     ')<br>',
                     html_entities($job['comment']),
                     '</li>';
            }
            echo '            </ul>';
        }
        if (is_array($program->jobs['done']) && count($program->jobs['done'])) {
            echo t('Recently completed jobs'), ':',
                 '            <ul class="-done">';
            foreach ($program->jobs['done'] as $job) {
                echo '                <li>',
                     $Jobs[$job['type']],
                     ' (', $Job_Status[$job['status']],
                     ':  ', strftime($_SESSION['date_listing_key'], $job['statustime']),
                     ')<br>',
                     html_entities($job['comment']),
                     '</li>';
            }
            echo '            </ul>';
        }
?>
            </div>

        <?php
            flush();
            $frontends = MythFrontend::findFrontends();
            if (is_array($frontends)) {
                echo '<div class="x-frontends">'.t('Play Recording on Frontend').':<ul>';
                    foreach ($frontends as $frontend)
                        echo '<li><a onclick="watchShow(\''.urlencode($frontend->getHost()).'\', \''.urlencode($program->chanid).'\', \''.urlencode($program->recstartts).'\');">'.$frontend->getHost().'</a><br>';
                echo '</ul></div>';
            }
        ?>

        </div>
