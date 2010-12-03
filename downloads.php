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

require_once("ui.php");
require_once("utils.php");

$gTitle = "Downloads";

/*
Here's how httparchive_mysqldump.gz was generated:
mysqldump --where='pageid >= 1 and pageid <= 2833' --no-create-db --no-create-info --skip-add-drop-table -u miscadmin -pmiscdb4me -h mysql.stevesouders.com stevesouderscom_misc pages requests > httparchive_Oct_2010
mysqldump --where='pageid >= 5281 and pageid <= 8679' --no-create-db --no-create-info --skip-add-drop-table -u miscadmin -pmiscdb4me -h mysql.stevesouders.com stevesouderscom_misc pages requests > httparchive_Oct_22_2010
mysqldump --where='pageid >= 8733 and pageid <= 10280' --no-create-db --no-create-info --skip-add-drop-table -u miscadmin -pmiscdb4me -h mysql.stevesouders.com stevesouderscom_misc pages requests > httparchive_Nov_6_2010
mysqldump --where='pageid >= 10281 and pageid <= 27599' --no-create-db --no-create-info --skip-add-drop-table -u miscadmin -pmiscdb4me -h mysql.stevesouders.com stevesouderscom_misc pages requests > httparchive_Nov_15_2010
mysqldump --where='pageid >= 27613 and pageid <= 45047' --no-create-db --no-create-info --skip-add-drop-table -u miscadmin -pmiscdb4me -h mysql.stevesouders.com stevesouderscom_misc pages requests > httparchive_Nov_29_2010

Here's how I restored it:
  mysql -v -u miscadmin -pPASSWORD_NO_SPACE -h mysql.stevesouders.com stevesouderscom_misc < httparchive_mysqldump

Here's how I did a CSV of Fortune 100 requestsdev:
select pagesdev.urlShort, requestsdev.urlShort, method, redirectUrl, firstReq, firstHtml, reqHttpVersion, reqHeadersSize, reqBodySize, reqCookieLen, status, respHttpVersion, respHeadersSize, respBodySize, respSize, respCookieLen, mimeType, req_accept, req_accept_charset, req_accept_encoding, req_accept_language, req_connection, req_host, req_if_modified_since, req_if_none_match, req_referer, req_user_agent, resp_accept_ranges, resp_age, resp_cache_control, resp_connection, resp_content_encoding, resp_content_language, resp_content_length, resp_content_location, resp_content_type, resp_date, resp_etag, resp_expires, resp_keep_alive, resp_last_modified, resp_location, resp_pragma, resp_server, resp_transfer_encoding, resp_vary, resp_via, resp_x_powered_by into outfile 'fortune1000.csv' fields terminated by ',' optionally enclosed by '"' lines terminated by '\n' from pagesdev, requestsdev where pagesdev.pageid=requestsdev.pageid and archive="Fortune 1000" and label="Oct 2010";

mysqldump -u miscadmin -p -h mysql.stevesouders.com --fields-terminated-by ',' --fields-optionally-enclosed-by '"' --lines-terminated-by '\n' select pagesdev.urlShort, requestsdev.urlShort, method, redirectUrl, firstReq, firstHtml, reqHttpVersion, reqHeadersSize, reqBodySize, reqCookieLen, status, respHttpVersion, respHeadersSize, respBodySize, respSize, respCookieLen, mimeType, req_accept, req_accept_charset, req_accept_encoding, req_accept_language, req_connection, req_host, req_if_modified_since, req_if_none_match, req_referer, req_user_agent, resp_accept_ranges, resp_age, resp_cache_control, resp_connection, resp_content_encoding, resp_content_language, resp_content_length, resp_content_location, resp_content_type, resp_date, resp_etag, resp_expires, resp_keep_alive, resp_last_modified, resp_location, resp_pragma, resp_server, resp_transfer_encoding, resp_vary, resp_via, resp_x_powered_by from pagesdev, requestsdev where pagesdev.pageid=requestsdev.pageid and archive="Fortune 1000" and label="Oct 2010" stevesouderscom_misc pagesdev requestsdev;

*/
?>
<!doctype html>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="style.css" />
	
	<title><?php echo $gTitle ?></title>
	<meta charset="UTF-8">
</head>

<body>

<?php echo uiHeader($gTitle); ?>
<h1>Downloads</h1>

<p>
There's a download file for each run:
</p>

<ul class=indent>
  <li> <a href="downloads/httparchive_Oct_2010.gz">Oct (5) 2010</a> (<?php echo round(filesize("./downloads/httparchive_Oct_2010.gz")/(1024*1024)) ?>MB)
  <li> <a href="downloads/httparchive_Oct_22_2010.gz">Oct 22 2010</a> (<?php echo round(filesize("./downloads/httparchive_Oct_22_2010.gz")/(1024*1024)) ?>MB)
  <li> <a href="downloads/httparchive_Nov_6_2010.gz">Nov 6 2010</a> (<?php echo round(filesize("./downloads/httparchive_Nov_6_2010.gz")/(1024*1024)) ?>MB)
  <li> <a href="downloads/httparchive_Nov_15_2010.gz">Nov 15 2010</a> (<?php echo round(filesize("./downloads/httparchive_Nov_15_2010.gz")/(1024*1024)) ?>MB)
  <li> <a href="downloads/httparchive_Nov_29_2010.gz">Nov 29 2010</a> (<?php echo round(filesize("./downloads/httparchive_Nov_29_2010.gz")/(1024*1024)) ?>MB)
</ul>

<p>
The downloaded file was generated by <a href="http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html">mysqldump</a> and then gzipped.
The mysqldump file does <em>not</em> contain the commands to create the MySQL database and tables.
To restore these mysqldump downloads:
</p>

<ol class=indent>
  <li> Install the <a href="http://code.google.com/p/httparchive/source/checkout">HTTP Archive source code</a>.
  <li> Modify <code>settings.inc</code> to have the appropriate MySQL settings.
  <li> Open the <code>admin.php</code> page in your browser and click on the link to create the MySQL tables.
  <li> Ungzip the downloaded mysqldump file.
  <li> Import the mysqldump file using this command:<br><code>mysql -v -u MYSQL_USERNAME -pMYSQL_PASSWORD -h MYSQL_HOSTNAME MYSQL_DB < MYSQLDUMP_FILE</code>
</ol>

<?php echo uiFooter() ?>

</body>

</html>

