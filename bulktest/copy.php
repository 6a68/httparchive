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

require_once("../settings.inc");
require_once("../utils.inc");

$gArchive = "All";
$gLabel = $argv[1];
if ( !$gLabel ) {
	echo "You must specify a label.\n";
	exit();
}


// find min & max pageid of the latest run
$row = doRowQuery("select min(pageid) as minid, max(pageid) as maxid from $gPagesTable where label='$gLabel';");
$minid = $row['minid'];
$maxid = $row['maxid'];
echo "Run \"$gLabel\": min pageid = $minid, max pageid = $maxid\n";


// copy the rows to production
if ( ! $gbMobile ) {
	$count = doSimpleQuery("select count(*) from $gPagesTableDesktop where pageid >= $minid and pageid <= $maxid;");
	if ( $count ) {
		echo "Rows already copied.\n";
	}
	else {
		echo "Copy 'pages' rows to production...\n";
		doSimpleCommand("insert into $gPagesTableDesktop select * from $gPagesTableDev where pageid >= $minid and pageid <= $maxid;");

		echo "Copy 'requests' rows to production...\n";
		doSimpleCommand("insert into $gRequestsTableDesktop select * from $gRequestsTableDev where pageid >= $minid and pageid <= $maxid;");

		// TODO - should we do this for $gbMobile too???
		echo "Copy 'urls' rows to production...\n";
		doSimpleCommand("insert into $gUrlsTable select * from $gUrlsTableDev;");

		echo "...DONE.\n";
	}
}



// mysqldump file
$dumpfile = "../downloads/httparchive_" . ( $gbMobile ? "mobile_" : "" ) . str_replace(" ", "_", $gLabel);
if ( file_exists("$dumpfile.gz") ) {
	echo "Mysqldump file already exists.\n";
}
else {
	echo "Creating mysqldump file $dumpfile ...\n";
	if ( $gbMobile ) {
		$cmd = "mysqldump --where='pageid >= $minid and pageid <= $maxid' --no-create-db --no-create-info --skip-add-drop-table -u $gMysqlUsername -p$gMysqlPassword -h $gMysqlServer $gMysqlDb $gPagesTableMobile $gRequestsTableMobile > $dumpfile";
	}
	else {
		$cmd = "mysqldump --where='pageid >= $minid and pageid <= $maxid' --no-create-db --no-create-info --skip-add-drop-table -u $gMysqlUsername -p$gMysqlPassword -h $gMysqlServer $gMysqlDb $gPagesTableDesktop $gRequestsTableDesktop > $dumpfile";
	}
	exec($cmd);
	exec("gzip $dumpfile");

	if ( $gbMobile ) {
		exec("cp -p $dumpfile.gz ~/httparchive.org/downloads/");
	}
	else {
		exec("cp -p $dumpfile.gz ~/httparchive.org/downloads/");
		exec("cp -p $dumpfile.gz ~/mobile.httparchive.org/downloads/");
	}

	echo "...mysqldump file created and copied: $dumpfile\n";
}


// Compute stats
require_once("../stats.inc");
require_once("../dbapi.inc");
$device = ( $gbMobile ? "iphone" : "IE8" );

if ( getStats($gLabel, "All", $device) ) {
	echo "Stats already computed.\n";
}
else {
	echo "Computing stats...\n";

	// remove any incomplete cache files that might have been created during the crawl
	removeStats($gLabel, NULL, $device);

	// remove intersection files since the intersection might have changed
	removeStats(NULL, "intersection", $device);

	computeMissingStats($device, true);

	if ( ! $gbMobile ) {
		echo "COPY STATS TO PRODUCTION!!!!!!!!!!!!!!!!!!!!!";
	}
	echo "...stats computed and copied.\n";
}

echo "DONE copying latest run to production.\n";
