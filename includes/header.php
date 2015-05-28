<?php

?>
<!DOCTYPE HTML>
<!--
	Minimaxing by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title><?php if(!empty($Page['title'])) { echo $Page['title'] . ' | '; } ?>Elements Tools by UTAlan</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="includes/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="includes/css/main.css" />
		<link rel="stylesheet" href="includes/css/jquery-ui.min.css" />
		<link rel="stylesheet" href="includes/css/jquery-ui.theme.min.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="includes/css/ie9.css" /><![endif]-->
	</head>
	<body>
		<div id="page-wrapper">
			<div id="header-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							<header id="header">
								<h1><a href="/" id="logo">Elements</a></h1>
								<nav id="nav">
									<a href="qi.php"<?php if($Page['title'] == 'Quantum Index') { echo ' class="current-page-item"'; } ?>>Quantum Index</a>
									<a href="discarder.php"<?php if($Page['title'] == 'Card Discarder') { echo ' class="current-page-item"'; } ?>>Card Discarder</a>
									<a href="code_generator.php"<?php if($Page['title'] == 'Code Generator') { echo ' class="current-page-item"'; } ?>>Code Generator</a>
									<a href="cards.php"<?php if($Page['title'] == 'Card Database') { echo ' class="current-page-item"'; } ?>>Card Database</a>
								</nav>
							</header>

						</div>
					</div>
				</div>
			</div>
