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

$gTitle = "HTTP Archive";
?>
<!doctype html>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="style.css" />
	
	<title><?php echo $gTitle ?></title>
	<meta charset="UTF-8">
	
<style>
.column {
	float: left;
	width: 50%; }
h2 {
	clear: both; }
#interestingnav { margin-top: 39px; margin-bottom: 30px; font-size: 0.9em; font-weight: bold; text-align: center; }
</style>
</head>

<body>
<?php echo uiHeader($gTitle); ?>

<p class="summary">The <a href="http://httparchive.org">HTTP Archive</a> tracks how the Web is built.</p>

<ul class="even-columns">
  <li>Trends in web technology&mdash;use of JavaScript, CSS, and new image formats
  <li>Performance of the Web&mdash;page speed, size, and errors
  <li>Open&mdash;the <a href="http://code.google.com/p/httparchive/source/checkout">code</a> is open source, the data is <a href="downloads.php">downloadable</a>
</ul>

<div id=interesting style="text-align: center;">
<!-- interesting.js will insert interesting stats here -->
</div>

<script type="text/javascript">
var script2 = document.createElement('script');
script2.src = "interesting.js";
script2.onload = function() { showSnippet('interesting'); };
document.getElementsByTagName('head')[0].appendChild(script2);
</script>

<?php echo uiFooter() ?>

</body>
</html>
