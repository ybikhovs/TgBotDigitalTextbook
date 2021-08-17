<?php
			if (isset($_GET['content'])) {
				switch ($_GET['content']) {
					case 'class': ?>
						<p>Класс: цифра параллели, например, 7</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='1'>Класс: <input name='class' value=''maxlength='5'> <input type='submit' value='добавить'></form>
					<?php
						break;
					case 'theme':?>
						<p>Код темы: в формате 7-1, где 7 - класс, 1 - номер темы в планировании<br />Название темы: название темы из планиорвания, например, Архитектура ПК<br />Оба поля обязательны!</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='2'>
						<p>Код темы: <input name='theme_code' value=''></p>
						<p>Название темы: <input name='theme_name' value=''></p>
						<p><input type='submit' value='добавить'></p</form>
					<?php
						break;
					case 'level':?>
						<p>Код темы: в формате 7-1, где 7 - класс, 1 - номер темы в планировании<br />Ссылка: ссылка на документ в облаке с описанием данного уровня<br />Все поля обязательны!</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='3'>
						<p>Код темы: <input name='theme_code' value=''></p>
						<p><input type="radio" name="level" value="tb"> Теория обязательная</p>
						<p><input type="radio" name="level" value="ts"> Теория основная</p>
						<p><input type="radio" name="level" value="td"> Теория дополнительная</p>
						<p><input type="radio" name="level" value="zb"> Задания обязательные</p>
						<p><input type="radio" name="level" value="zs"> Задания основные</p>
						<p><input type="radio" name="level" value="zd"> Задания дополнительные</p>
						<p>Ссылка: <input name='link' value=''></p>
						<p><input type='submit' value='добавить'></p</form>
					<?php
						break;
				}
			}	
