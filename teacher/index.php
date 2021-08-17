<?php
	require 'post.php';			
?>
<html>
<body>
	<table>
		<tr style="font-weight: bold;">
			<td width=100>Добавить</td>
			<td width=100>Посмотреть</td>
			<td width=100>Учащиеся</td>
		</tr>
		<tr>
			<td> - <a href="?job=add&content=class">Класс</a></td>
			<td> - <a href="?job=view&content=class">Классы</a></td>
			<td> - <a href="?job=pupil&content=list">Список</a></td>
		</tr>
		<tr>
			<td> - <a href="?job=add&content=theme">Тему</a></td>
			<td> - <a href="?job=view&content=theme">Темы</a></td>
			<td> - <a href="?job=pupil&content=task">Работы</a></td>
		</tr>
		<tr>
			<td> - <a href="?job=add&content=level">Уровень</a></td>
			<td> - <a href="?job=view&content=level">Уровни</a></td>
			<td> - <a href="?job=pupil&content=message">Отправить сообщение</a></td>
		</tr>
	</table>
<hr />

<?php
if ($message) {
	echo "<h2 style='font-weight: bold; color:".$message[1]."'>".$message[0]."<br /></h2>";
}
if (isset($_GET['job'])) {
	switch ($_GET['job']) {
		case 'add':
			require 'add.php';			
			break;
		case 'view':
			require 'view.php';			
			break;
		case 'pupil':
			require 'pupil.php';			
			break;
	}
}	
?>



</body>
</html>