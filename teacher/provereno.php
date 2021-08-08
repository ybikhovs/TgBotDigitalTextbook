<?php 
require '../connect.php';
$conn->query("UPDATE bot_work SET provereno='да' WHERE time='".htmlspecialchars($_POST['time'])."'");
mysqli_close($conn);


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
