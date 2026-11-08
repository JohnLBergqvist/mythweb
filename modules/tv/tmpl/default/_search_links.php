<?php // Links that search other sites for the programme's title, inside the x-links row ?>
                <a href="http://www.themoviedb.org/search/movie?query=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'themoviedb') ?></a>
                <a href="http://www.imdb.com/search/title?title=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'IMDB') ?></a>
                <a href="http://www.thetvdb.com/?string=<?php echo urlencode($schedule->title) ?>&searchseriesid=&tab=listseries&function=Search"><?php echo t('Search $1', 'TheTVDB') ?></a>
                <a href="http://www.tv.com/search.php?type=11&stype=all&qs=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'TV.com') ?></a>
                <a href="http://www.google.com/search?q=<?php echo urlencode($schedule->title) ?>"><?php echo t('Search $1', 'Google') ?></a>
