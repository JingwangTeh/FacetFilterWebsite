<?php
	include 'php-commons/functions.php';
	include 'php-commons/database.php';
	
	/* Get file name of the file to download */
	if (array_key_exists("fileToDownload", $_POST)) {
		$fileToDownload = (!empty($_POST['fileToDownload']))? sanitize_input($_POST['fileToDownload']) : null;
		
		// check if file name is not empty, and file is readable
		if (!empty($fileToDownload)){
			if(is_readable($dir_log_folder . '/' . $fileToDownload))
			{	// build download document
				header ("Content-type: text/plain");
				header ("Content-Disposition: attachment; filename='$fileToDownload'");
				
				// place file in download document
				readfile($dir_log_folder . '/' . $fileToDownload);
				// redirect back to previous page
			} else {
				?>
		
				<!DOCTYPE html>
				<html>
					<head>
						<meta charset="UTF-8"/>
						<title>File Download Error</title>
					</head>
					<body>
						<p>ERROR: <?php echo $fileToDownload ?> does not exist, or is not readable.</p>
						<a href="assignment6dev.php">Go Back</a>
					</body>
				</html>
				
				<?php
			}
		} else {
			// Invalid File Name (empty)
			?>
		
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset="UTF-8"/>
					<title>File Download Error</title>
				</head>
				<body>
					<p>ERROR: Invalid Name</p>
					<a href="assignment6dev.php">Go Back</a>
				</body>
			</html>
			
			<?php
		}
	} else {
		// Invalid POST
		?>

		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8"/>
				<title>File Download Error</title>
			</head>
			<body>
				<p>ERROR: Invalid Path</p>
				<a href="assignment6dev.php">Go Back</a>
			</body>
		</html>

		<?php
	}
?>