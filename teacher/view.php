<?php
			if (isset($_GET['content'])) {
				switch ($_GET['content']) {
					case 'class': ?>
						<p>Классы</p> 
						<?php
						require '../connect.php';
						$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
						$rows = $res->fetch_all(MYSQLI_ASSOC);
						if ($rows) {
							foreach ($rows as $row) { ?>
								<form action='?job=view&content=class' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='<?=$row['razdel'];?>'><?=$row['razdel'];?>&nbsp;&nbsp;&nbsp; <input type='submit' value='удалить'> | <input type="checkbox" name="all" value="1"> Также удалить ВСЕ данные этого класса</form>
							<?php
							}
						}
						break;
					case 'theme':
						if (!$_POST['thclass']) {?>
							<p>Выберите класс</p> 
							<?php
							require '../connect.php';
							$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
							$rows = $res->fetch_all(MYSQLI_ASSOC);
							if ($rows) { ?>
								<form action='' method='post'><input type='hidden' name='proverka' value='0'>
								<?php
								foreach ($rows as $row) { ?>
									<p><input type="radio" name="thclass" value="<?=$row['razdel'];?>"> <?=$row['razdel'];?></p>
								
								<?php
								} ?>
								<input type='submit' value='выбрать'></form>
								
								<?php
							}
						} else {
							echo "<p>Темы ".$_POST['thclass']." класса</p>";
							require '../connect.php';
							$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='".$_POST['thclass']."' ORDER BY razdel");
							$rows = $res->fetch_all(MYSQLI_ASSOC);
							if ($rows) { ?>
								<table> <?php
								foreach ($rows as $row) { ?>
									<tr><td width=50><?=$row['razdel'];?></td><td width=300><?=$row['content'];?></td><td><form action='?job=view&content=theme' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='<?=$row['razdel'];?>'><input type='hidden' name='thclass' value='<?=$_POST['thclass'];?>'> <input type='submit' value='удалить'> | <input type="checkbox" name="all" value="1"> Также удалить ВСЕ уровни этой темы</form></td></tr>
								<?php
								} ?>
								</table>
							<?php
							}
						}
						break;
					case 'level':
						if (!$_POST['ltheme']) {
							if (!$_POST['lclass']) {?>
								<p>Выберите класс</p> 
								<?php
								require '../connect.php';
								$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
								$rows = $res->fetch_all(MYSQLI_ASSOC);
								if ($rows) { ?>
									<form action='' method='post'><input type='hidden' name='proverka' value='0'>
									<?php
									foreach ($rows as $row) { ?>
										<p><input type="radio" name="lclass" value="<?=$row['razdel'];?>"> <?=$row['razdel'];?></p>
									
									<?php
									} ?>
									<input type='submit' value='выбрать'></form>
									<?php
								}
							} else {?>
								<p>Выберите тему</p> 
								<?php
								require '../connect.php';
								$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='".$_POST['lclass']."' ORDER BY razdel");
								$rows = $res->fetch_all(MYSQLI_ASSOC);
								if ($rows) { ?>
									<form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='lclass' value='<?=$_POST['lclass'];?>'>
									<?php
									foreach ($rows as $row) { ?>
										<p><input type="radio" name="ltheme" value="<?=$row['razdel'];?>#<?=$row['content'];?>"> <?=$row['razdel'];?> - <?=$row['content'];?></p>
									
									<?php
									} ?>
									<input type='submit' value='выбрать'></form>
									<?php
								}
							}
						} else {
							$raz_id = explode('#',$_POST['ltheme'])[0]; 
							$raz_txt = explode('#',$_POST['ltheme'])[1]; 
							echo "<p>Уровни темы '".$raz_txt."' (".$raz_id.") ".$_POST['lclass']." класса</p>";
							require '../connect.php';
							$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='".$raz_id."' ORDER BY razdel");
							$rows = $res->fetch_all(MYSQLI_ASSOC);
							if ($rows) { ?>
								<table> <?php
								foreach ($rows as $row) { 
									if (stripos($row['razdel'], 'tb')) $txt='Теория обязательная';
									elseif (stripos($row['razdel'], 'ts')) $txt='Теория основная';
									elseif (stripos($row['razdel'], 'td')) $txt='Теория дополнительная';
									elseif (stripos($row['razdel'], 'zb')) $txt='Задания обязательные';
									elseif (stripos($row['razdel'], 'zs')) $txt='Задания основные';
									elseif (stripos($row['razdel'], 'zd')) $txt='Задания дополнительные';
									?>
									<tr><td width=50><?=$row['razdel'];?></td><td width=200><?=$txt;?></td><td width=100><a href='<?=$row['content'];?>' target='_blank'>посмотреть</a></td><td><form action='?job=view&content=level' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='<?=$row['razdel'];?>'><input type='hidden' name='lclass' value='<?=$_POST['lclass'];?>'><input type='hidden' name='ltheme' value='<?=$raz_id;?>'> <input type='submit' value='удалить'></form></td></tr>
								<?php
								} ?>
								</table>
							<?php
							}
						}
						break;
				}
			}	


