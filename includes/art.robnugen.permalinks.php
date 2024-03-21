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
	//  @param string $input The content of the tag
	//   (unused for art.robnugen.com).
	//  @param array $args The attributes of the tag
	//   (unused for art.robnugen.com).
	//  @param Parser $parser Parser instance available to render
	//   wikitext into html, or parser methods.
	//  @param PPFrame $frame Can be used to see what template
	//   this hook was used with.
	//  @return string HTML to insert in the page.
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
