<?php 
require '../connect.php';
$conn->query("UPDATE bot_work SET provereno='да', review='".htmlspecialchars($_POST['review'])."' WHERE time='".htmlspecialchars($_POST['time'])."'");
mysqli_close($conn);
if ($_POST['review']) {
	$text = "Отзыв на работу ".htmlspecialchars($_POST['idwork']).": ".htmlspecialchars($_POST['review']);
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
