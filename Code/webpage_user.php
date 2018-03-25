<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>assignment 7 User</title>
		
		<!-- ----------CSS---------- -->
		<link rel="stylesheet" type="text/css" href="css/cssCommons.css">
		<link rel="stylesheet" type="text/css" href="css/cssUserPage.css">
		
		<!-- ----------JavaScript---------- -->
		<script type="text/javascript" src="js/scriptCommons.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		
		<!-- ----------PHP Includes (Commons) ---------- -->
		<!-- Database -->
		<?php include 'php-commons/database.php'; ?>
		<!-- Collection of all records from database -->
		<?php include 'php-commons/collection.php'; ?>
		
		<!-- Variables -->
		<?php include 'php-commons/constants.php'; ?>
		<!-- Filters -->
		<?php include 'php-commons/filters.php'; ?>
		
		<!-- Functions -->
		<?php include 'php-commons/functions.php'; ?>
	</head>
	<body>
		<!-- ------------------------------Page Container (Wrapper)------------------------------ -->
		<div class="page_container">

				<!-- ------------------------------Header Section------------------------------ -->
				<div class="header">
					<div>
						<div class="header_content">
							<!-- Header Title -->
							<div class="header_title"><a href="/">ISIT307 Assignment 7</a></div>
					
							<!-- Header Shortcuts -->
							<div class="header_shortcuts"><a href="webpage_dev.php">Dev Login</a></div>
						</div>
					</div>
				</div>
				
				<!-- ------------------------------Content Section------------------------------ -->
				<?php 
					include 'webpage_user_content.php';
				?>

				<!-- ------------------------------Footer Section------------------------------ -->
				<div class="footer">
					<div>
						<div class="footer_content">
							<!-- Footer Copyright -->
							<div class="footer_copyright">
								<p>Created by Teh Jingwang</p>
								<p>Year 2017</p>
							</div>
						</div>
					</div>
				</div>

		</div>
		<!-- ------------------------------End of Page Container------------------------------ -->
	</body>
</html>
