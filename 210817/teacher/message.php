<?php 
if ($_POST['message']) {
	$text = "Сообщение от учителя: ".htmlspecialchars($_POST['message']);
	$url="https://api.telegram.org/bot1846272684:AAGjCyxFiDWofColX8Sez8IWL_tWWQ6zP8Y/sendMessage?chat_id=".htmlspecialchars($_POST['chatid'])."&text=".$text;
	print("<iframe src='".$url."' width=0 height=0></iframe>");
}

 ?>

 
<script>
function closeWindow(){
			window.close();
}
</script>
<html>
<body onload="closeWindow()">
</body>
</html>
