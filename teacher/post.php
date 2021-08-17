<?php
$message = [];
$pr = 0;
if ($_POST['class']) {
	$pr++;
	require '../connect.php';
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$_POST['class']."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такой класс уже есть!", "red"];
	} else {
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$_POST['class']."', '0', '".$_POST['class']."')");
		$message = ["Добавлено!", "green"];
	}
	mysqli_close($conn);
} elseif ($_POST['theme_code'] && $_POST['theme_name']) {
	require '../connect.php';
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$_POST['theme_code']."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такая тема уже есть!", "red"];
	} else {
		$th = explode('-', $_POST['theme_code']);
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$_POST['theme_code']."', '".$th[0]."', '".$_POST['theme_name']."')");
		$message = ["Добавлено!", "green"];
	}
	mysqli_close($conn);
} elseif ($_POST['theme_code'] && $_POST['level'] && $_POST['link']) {
	$razd = $_POST['theme_code']."-".$_POST['level'];
	require '../connect.php';
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$razd."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такой уровень уже есть!", "red"];
	} else {
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$razd."', '".$_POST['theme_code']."', '".$_POST['link']."')");
		$message = ["Добавлено!", "green"];
	}
	mysqli_close($conn);
} elseif ($_POST['del']) {
	require '../connect.php';
	if ($_POST['all'] == 1) {
		if (stripos($_POST['del'], '-')) {
			$conn->query("DELETE FROM bot_razdel WHERE parent = '".$_POST['del']."'");
		} else {
			$res = $conn->query("SELECT * FROM bot_razdel WHERE parent = '".$_POST['del']."'");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			if ($rows) { 
				foreach ($rows as $row) { 
					$conn->query("DELETE FROM bot_razdel WHERE parent = '".$row['razdel']."'");
				} 
			}
			$conn->query("DELETE FROM bot_razdel WHERE razdel = '".$row['razdel']."'");
		}
	}
	$conn->query("DELETE FROM bot_razdel WHERE razdel = '".$_POST['del']."'");
	mysqli_close($conn);
	$message = ["Удалено!", "green"];
}
if ($_POST['theme_code']) $pr++;
if ($_POST['theme_name']) $pr++;
if ($_POST['level']) $pr++;
if ($_POST['link']) $pr++;
if ($pr != $_POST['proverka']) $message = ["Заполните все поля!", "red"];
