<?php
require_once '/home/robuwikipix/art.robnugen.com/includes/mysql.php';
require_once '/home/robuwikipix/art.robnugen.com/includes/lilurl.php';

$wgHooks['ParserFirstCallInit'][] = 'ArtRobnugenComPermalinks::onParserSetup';

class ArtRobnugenComPermalinks {
    // Register any render callbacks with the parser
    function onParserSetup( Parser $parser ) {
        // When the parser sees the <permalink> tag, it executes renderTagPermalink (see below)
        $parser->setHook( 'permalink', 'ArtRobnugenComPermalinks::renderTagPermalink' );
        return true;
    }

    // Render <permalink>
    function renderTagPermalink( $input, array $args, Parser $parser, PPFrame $frame ) {
        $base_url = "https://art.robnugen.com/";

		global $wgRequest;
		$prefix = "The permalink for this page is ";

		$fullURL = $wgRequest->getFullRequestURL();

		$actualURL = preg_replace('/\?(.)*/','',$fullURL);	// wipe any URL params

		$mysql_safeURL = trim($actualURL);

		$lilurl = new lilURL();

		$permalink_id = $lilurl->get_id($mysql_safeURL);

		if($permalink_id != -1)
		{
			$permalink = $parser->recursiveTagParse($base_url . $permalink_id);
			return implode(" ",array($prefix,$permalink));
		}
		else
		{
			return "This page has no permalink on " . $parser->recursiveTagParse($base_url);
		}
    }
}
