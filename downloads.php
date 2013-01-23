<?php 
/*
Copyright 2010 Google Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once("ui.inc");
require_once("utils.inc");

$gTitle = "Downloads";

function listFiles($hFiles) {
	$sHtml = "";
	$aKeys = array_keys($hFiles);
	sort($aKeys, SORT_NUMERIC);
	foreach( array_reverse($aKeys) as $epoch ) {
		$sHtml .= "  <li> " . date("M j, Y", $epoch) . ": " .
			( array_key_exists('desktop', $hFiles[$epoch]) ? $hFiles[$epoch]['desktop'] : "" ) .
			( array_key_exists('mobile', $hFiles[$epoch]) ? ( array_key_exists('desktop', $hFiles[$epoch]) ? ", " : "" ) . $hFiles[$epoch]['mobile'] : "" ) .
			"\n";
	}

	return $sHtml;
}
?>
<!doctype html>
<html>
<head>
<title><?php echo $gTitle ?></title>
<meta charset="UTF-8">

<?php echo headfirst() ?>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>

<body>

<?php echo uiHeader($gTitle); ?>
<h1>Downloads</h1>

<?php
// hash of files where we can sort them by time:
//   - the key is epoch time from the filename (eg "Oct 15 2011")
//   - the value is the actual HTML to put into a list (hacky)
$ghFiles = array();

// Add files that are on the Internet Archive storage.
if (is_file("downloads/archived.json")) {
    $archived = json_decode(file_get_contents("downloads/archived.json"), true);
    foreach ($archived as $filename => $fileData) {
        if (array_key_exists('verified', $fileData) && $fileData['verified']) {
			addFile($ghFiles, $filename, $fileData['url'], $fileData['size']);
		}
	}
}

// Add files from the local directory (if any).
foreach ( glob("downloads/httparchive_*.gz") as $filename ) {
	addFile($ghFiles, $filename, $filename, filesize($filename));
}


// Given a dump file's info, create the HTML to be put in a list.
// Add it to hash of files passed in.
function addFile(&$hFiles, $filename, $url, $size) {
	$epoch = dumpfileEpochTime($filename);
	if ( $epoch ) {
		if ( ! array_key_exists($epoch, $hFiles) ) {
			$hFiles[$epoch] = array();
		}

        $browser = ( strpos($filename, "_mobile_") ? 'mobile' : 'desktop' );
		if ( strpos($filename, "_requests.csv") ) {
			// There should be 4 files: _pages.gz, _pages.csv.gz, _requests.gz, _requests.csv.gz
			// If we see _requests.csv we assume the other 3 exist and format accordingly and
			// we'll overwrite any previously saved results.
			$hFiles[$epoch][$browser] = formatDumpfileItem($epoch, $browser, str_replace("_requests.csv", "_pages", $url), $size, "pages") . ", " .
				formatDumpfileItem($epoch, $browser, str_replace("_requests.csv", "_pages.csv", $url), $size, "pages", "CSV") . ", " .
				formatDumpfileItem($epoch, $browser, str_replace("_requests.csv", "_requests", $url), $size, "requests") . ", " .
				formatDumpfileItem($epoch, $browser, $url, $size, "requests", "CSV");
		}
        else if ( ! array_key_exists($browser, $hFiles[$epoch]) ) {
			// You can only have 1 set of files for a given epoch & browser.
			// If we've already saved one, don't save another.
			// This logic allows us to add unexpected dump files perhaps stored locally.
			$hFiles[$epoch][$browser] = formatDumpfileItem($epoch, $browser, $url, $size);
		}
	}
}


// Format the actual HTML to be added to a list (but we do NOT include the "<li>" tag!).
function formatDumpfileItem($epoch, $browser, $url, $filesize, $table=null, $format=null) {
	$browser = ( "mobile" === $browser ? "iPhone" : "IE" );
	$size = ( $filesize > 1024*1024 ? round($filesize/(1024*1024)) . " MB" : round($filesize/(1024)) . " kB" );
	return "<a href='$url'>$browser" . ( $table ? " $table" : "" ) . ( $format ? " ($format)" : "" ) . "</a>";
}
?>

<style>
.indent LI { margin-bottom: 2px; }
</style>

<p>
In addition to the HTTP Archive <a href="http://code.google.com/p/httparchive/source/browse">source code</a> being open source,
all of the data gathered is also available for download.
</p>

<h2>Instructions</h2>
<p>
The downloaded files were generated by <a href="http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html">mysqldump</a>.
The mysqldump files do <em>not</em> contain the commands to create the MySQL database and tables.
To restore these mysqldumps:
</p>

<ol class=indent>
  <li> Import the <a href="downloads/httparchive_schema.sql">schema</a> dump to create the tables.
  <li> Import the desired crawl dump using this command:<br><code>gunzip -c MYSQLDUMP_FILE.gz | mysql -u MYSQL_USERNAME -pMYSQL_PASSWORD -h MYSQL_HOSTNAME MYSQL_DB</code>
  <li> If you want to run a private instance of the source code, you need to also import the stats and crawls dumps.
</ol>

<h2>Files</h2>

<p>
These files define the schema and the meta-level tables:
</p>
<ul class=indent>
  <li> <a href="downloads/httparchive_schema.sql">schema</a> - the schema for the tables referenced in the data dumps
  <li> <a href="downloads/httparchive_stats.gz">stats</a> - the aggregated stats for <em>all</em> crawls
  <li> <a href="downloads/httparchive_urls.gz">urls</a> - the URLs used in <em>the most recent</em> crawl
  <li> <a href="downloads/httparchive_crawls.gz">crawls</a> - meta-information about all of the crawls
</ul>


<p>
There's a download file for each crawl for desktop ("IE") and mobile ("iPhone"):
</p>
<ul class=indent>
<?php echo listFiles($ghFiles) ?>
</ul>

<?php echo uiFooter() ?>

</body>

</html>

