<?php
require_once '/home/robuwikipix/art.robnugen.com/includes/mysql.php';
require_once '/home/robuwikipix/art.robnugen.com/includes/lilurl.php';

$wgExtensionFunctions[] = 'efPermalinksForArt';

function efPermalinksForArt() {
    global $wgParser;
    $wgParser->setHook( 'permalink', 'efRenderPermalinkLine' );
}

function efRenderPermalinkLine( $input, $args, $parser ) {
	global $wgRequest;
	$prefix = "The permalink for this page is ";

	$fullURL = $wgRequest->getFullRequestURL();

	$actualURL = preg_replace('/\?(.)*/','',$fullURL);	// wipe any URL params

	$mysql_safeURL = trim($actualURL);

	$lilurl = new lilURL();
	
	$permalink_id = $lilurl->get_id($mysql_safeURL);

	if($permalink_id != -1)
	{
		$permalink = $parser->recursiveTagParse("http://art.robnugen.com/" . $permalink_id);
		return implode(" ",array($prefix,$permalink));
	}
	else
	{
		return "This page has no permalink on art.robnugen.com";
	}
}
?>