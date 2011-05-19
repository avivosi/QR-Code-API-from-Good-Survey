<?php
include_once("include/QRCodeAPI.class.php");

$api = new QRCodeAPI();

$action = $_REQUEST["action"];
$message = $_REQUEST["message"];

switch ($action)
{
	case "generate":
		header("Content-Type: image/png");
		echo $api->generate($message, "png");
		exit();
		break;
		
	case "encode":
		header("Content-Type: image/png");
		echo $api->encode($message, "png", null, 8, 4, "byte", "M");
		exit();
		break;
		
	case "decode":
		$json = $api->decode("images/http-www-good-survey-com.png");
		$model = json_decode($json);
		echo $model->content;
		exit();
		break;
}
?>
<html>
<head>
	<title>QR Code API sample</title>
<head>
<body>
	<form action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" method="GET">
		<label for="action">API method:</label>
		<select id="action" name="action">
			<option value="generate" <?php echo $action == "generate" ? 'selected="selected"' : ""; ?>>generate</option>
			<option value="encode" <?php echo $action == "encode" ? 'selected="selected"' : ""; ?>>encode</option>
			<option value="decode" <?php echo $action == "decode" ? 'selected="selected"' : ""; ?>>decode</option>
		</select>
		
		<label for="message">Message:</label>
		<input type="text" id="message" name="message" value="<?php echo $message; ?>" />
		
		<input type="submit" value="Submit" />
	</form>
</body>
</html>