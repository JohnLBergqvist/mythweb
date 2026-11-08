<?php // JavaScript that looks up the programme's metadata and adds its home page links ?>
    // Tries to find metadata for the current item
    // If found adds a "Home Page" link to the page
    function detailLookupMetadata() {
        new Ajax.Request('<?php echo root_url ?>tv/lookup_metadata',
                         {
                            parameters: {
                                              'title'        : "<?php echo preg_replace('/[\n\r]/', '\\n', $schedule->title) ?>",
                                              'subtitle'     : "<?php echo preg_replace('/[\n\r]/', '\\n', $schedule->subtitle) ?>",
                                              'inetref'      : "<?php echo ($program ? $program->inetref : $schedule->inetref) ?>",
                                              'season'       : "<?php echo ($program ? $program->season : $schedule->season) ?>",
                                              'episode'      : "<?php echo ($program ? $program->episode : $schedule->episode) ?>",
                                              'allowgeneric' : "true"
                                        },
                            asynchronous: true,
                            method: 'get',
                            onSuccess: detailOnMetadata,
                            onFailure: detailOnMetadataFailure
                         }
                        );

    }

    // if metadata is found inserts a home page then update the links
    function detailOnMetadata(transport) {
        var list = transport.responseJSON.VideoLookupList;

        updateHomePage(list.VideoLookups[0] || {});

    }

    // silently fail (no need to disrupt the page)
    function detailOnMetadataFailure(transport) {
    }

    function updateHomePage(item) {
         var homePage = $("home-page");
         var homeButton = $("metadata-home-page-link");
         var traktPage = $("trakt-page");

         // if this item doesn't have a home page link then
         // remove the existing link or ignore
         if (!item.HomePage) {
             homePage && Element.remove(homePage);
             homeButton && Element.remove(homeButton);
             traktPage && Element.remove(traktPage);
             return;
         }
         var episodeID = item.HomePage.match(/.*&id=(\d+)/)[1];
         var traktHREF = "http://trakt.tv/search/tvdb/" + episodeID + "?id_type=episode"

         // update the link or create it if this item does have a trakt page
         if (traktPage) {
              traktPage.href = traktHREF;
         } else {
              $($$(".x-links")[0].children[1]).insert({top:
                  new Element("a",
                      {href: traktHREF,
                       target: "mythtwebtrakt", id: "trakt-page"}).
                      update(item.Title + " " + "<?php echo t("Trakt.tv Home Page") ?>")});
         }

         // update the link or create it if this item does have a home page
         if (homePage) {
              homePage.href = item.HomePage;
         } else {
              $($$(".x-links")[0].children[1]).insert({top:
                  new Element("a",
                      {href: item.HomePage,
                       target: "_new", id: "home-page"}).
                      update(item.Title + " " + "<?php echo t("Metadata Home Page") ?>")});
         }

         if (homeButton) {
              homeButton.href = item.HomePage;
         }  else {
              var mhp = $("metadata-home-page");
              mhp && mhp.insert(
                         new Element("a",
                              {id: "metadata-home-page-link",
                               href: item.HomePage,
                               target: "_new"}).
                              update("<?php echo t("Metadata Home Page")?>")
                          );
         }


    }


    // hook to look up data once the page has started
    detailLookupMetadata();
