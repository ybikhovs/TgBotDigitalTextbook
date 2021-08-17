<?php
require '../connect.php';
if (isset($_GET['content'])) {
	switch ($_GET['content']) {
		case 'task':
			echo '<p>Непроверенные работы</p>';
			$res = $conn->query("SELECT * FROM bot_work WHERE provereno<>'да'");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			print ("<table>");
			foreach ($rows as $row ) {
				$profile = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$row['chatid']."'");
				$prof = $profile->fetch_all(MYSQLI_ASSOC);
				foreach ($prof as $pr ) {
					print ("<tr><td>".$row['time']." | ".$pr['first_name']." ".$pr['second_name']." | ".$pr['class']." | <a href=".$row['url']." target=_blank>работа</a> | </td><td><form action='provereno.php' method='post' target='_blank'><input name='idwork' value='".$row['idwork']."' readonly> - <input maxlength='500' size='50' name='review' value=''><input type='hidden' name='time' value='".$row['time']."'><input type='hidden' name='chatid' value='".$row['chatid']."'><input type='submit' value='Проверено'></form></td></tr>");
				}
			}
			print ("</table>");
			break;
		case 'list':
			if (!$_POST['pclass']) {?>
				<p>Выберите класс</p> 
				<?php
				require '../connect.php';
				$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
				$rows = $res->fetch_all(MYSQLI_ASSOC);
				if ($rows) { ?>
					<form action='' method='post'><input type='hidden' name='proverka' value='0'>
					<?php
					foreach ($rows as $row) { ?>
						<p><input type="radio" name="pclass" value="<?=$row['razdel'];?>"> <?=$row['razdel'];?></p>
					
					<?php
					} ?>
					<input type='submit' value='выбрать'></form>
					
					<?php
				}
			} else {
				echo "<p>Список ".$_POST['pclass']." класса</p>";
				require '../connect.php';
				$res = $conn->query("SELECT * FROM bot_spisok WHERE class='".$_POST['pclass']."' ORDER BY second_name");
				$rows = $res->fetch_all(MYSQLI_ASSOC);
				if ($rows) {
					echo '<ol>';
					foreach ($rows as $row ) {
						print ("<li>".$row['chatid']." - ".$row['second_name']." ".$row['first_name']." | Тема: ".$row['theme']."</li>");
					}
					echo '</ol>';
				}
			}
			break;
		case 'message':
			echo '<p>Отправка сообщения ученику</p>'; ?>
			<form action='message.php' method='post' target='_blank'>
			<p>ID чата в Telegram: <input maxlength='30' size='20' name='chatid' value=''></p>
			<p>Сообщение: <input maxlength='500' size='50' name='message' value=''></p>
			<input type='submit' value='Отправить'></form>
			<?php
			break;
	}
}	
mysqli_close($conn);
