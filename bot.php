<?php

// определяем кодировку
header('Content-type: text/html; charset=utf-8');
// Создаем объект бота
$bot = new Bot();
// Обрабатываем пришедшие данные
$bot->init('php://input');

/**
 * Class Bot
 */
class Bot
{
    // <bot_token> - созданный токен для нашего бота от @BotFather
    private $botToken = "112445570484:AAGjjhsdgfsydJGJHghX8Sez8IWL_tJHhj65hvj8Y";
    // адрес для запросов к API Telegram
    private $apiUrl = "https://api.telegram.org/bot";

    public function init($data)
    {
        // Пароль для дуступа к боту
		$pswd = '111';
		
		// создаем массив из пришедших данных от API Telegram
        $arrData = $this->getData($data);
		
        // лог
        // $this->setFileLog($arrData);

        if (array_key_exists('message', $arrData)) {
            $chat_id = $arrData['message']['chat']['id'];
            $message = $arrData['message']['text'];
			$username = $arrData['message']['from']['username'];

        } elseif (array_key_exists('callback_query', $arrData)) {
            $chat_id = $arrData['callback_query']['message']['chat']['id'];
            $message = $arrData['callback_query']['data'];
			$username = $arrData['callback_query']['from']['username'];
        }

		$justKeyboard = $this->getKeyBoard([[["text" => "Темы"]],
			[["text" => "Сдать работу"], ["text" => "Вопрос учителю"]]
		]);
		
		if ($this->inBaseOk($chat_id)!=1) {
			if ($this->inBase($chat_id)==0) {
				if ($message!=$pswd || $message=='/start') {
					$dataSend = array(
						'text' => "Введите пароль",
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
				} elseif ($message==$pswd) {
					$dataSend = array(
						'text' => "Отлично. Теперь давайте знакомиться. Введите Ваше имя.",
						'chat_id' => $chat_id,
					);
					require 'connect.php';
					$conn->query("INSERT INTO bot_spisok (chatid, first_name, second_name, class, ok, theme, math) VALUES ('".$chat_id."', '0', '0', '0', '0', '0', '0')");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");
				}
			} else {
				if ($this->inBaseFirstName($chat_id)==1) {
					$txt = "А теперь, ".$message.", введите Вашу фамилию.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET first_name='".$message."' WHERE chatid='".$chat_id."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");			
				} elseif ($this->inBaseSecondName($chat_id)==1) {
					$profile = $this->getProfile($chat_id);
					$txt = "Теперь, ".$profile['first_name'].", выберите Ваш класс.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbd('class.txt', 4)),
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET second_name='".$message."' WHERE chatid='".$chat_id."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");			
				} elseif ($this->inBaseClass($chat_id)==1) {
					$profile = $this->getProfile($chat_id);
					$txt = "Спасибо, ".$profile['first_name'].". 
					
					Нажми кнопку 'Темы', чтобы выбрать тему для изучения.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $justKeyboard,
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET class='".$message."', ok='ok' WHERE chatid='".$chat_id."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");	
				}
			}
		} else {
			switch ($message) {
				case 'Темы':
					$profile = $this->getProfile($chat_id);
					$adr = "themes".trim($profile['class']).".txt";
					$dataSend = array(
						'text' => 'Выберите тему по информатике '.$profile['class'].' класса.',
						'chat_id' => $chat_id,
						'reply_markup' => $justKeyboard,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					$dataSend = array(
						'text' => 'Темы:',
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbd($adr, 1)),
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case '/help':
					$dataSend = array(
						'text' => "Значения кнопок:
						Прослушать - Прослушать бесплатно первый подкаст, 
						Оплатить - Оплатить доступ и слушать далее, 
						Спросить - Получить ответы на важные вопросы, 
						Помощь - Получить эту инструкцию ещё раз",
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case (preg_match('/^Задания/', $message) ? true : false):
				case (preg_match('/^Теория/', $message) ? true : false):
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET math='".$message."' WHERE chatid='".$chat_id."'");
					$dataSend = array(
						'text' => $message,
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbdTh($message, $chat_id)),
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case 'Вопрос учителю':
					$dataSend = array(
						'text' => 'Чтобы отправить вопрос учителю, отправьте сообщение в следющем формате: #вопрос текст_вопроса. Например,
						#вопрос можно я доделаю практическую работу на следующем уроке?',
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;				
				case (preg_match('/^#вопрос/', $message) ? true : false):
					$profile = $this->getProfile($chat_id);
					$message = str_replace("#вопрос ", "", $message);
					$message = 'Чат: '.$chat_id.' ||| Имя: '.$profile['first_name'].' ||| Фамилия: '.$profile['second_name'].' ||| Класс: '.$profile['class'].' ||| Вопрос: '.$message;
					$dataSend = array(
						'text' => $message,
						'chat_id' => '391741304',
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case (preg_match('/^#ответ/', $message) ? true : false): // $ответ$391741304$текст ответа
					$data = explode('#', $message);
					$dataSend = array(
						'text' => 'Ответ учителя: '.$data[3],
						'chat_id' => $data[2],
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case 'Сдать работу':
					$dataSend = array(
						'text' => 'Чтобы сдать работу:
						1) загрузите вашу работу в любое облачное хранилище (Google Drive, Яндекс Диск и т.п.)
						2) создайте ссылку на документ с возможностью редактирования
						3) отправьте в этот чат сообщение в формате: #работа https://ссылка_на_ваш_документ',
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;				
				case (preg_match('/^#работа/', $message) ? true : false):
					$message = str_replace("#работа ", "", $message);
					$today = date("Y-m-d H:i:s");
					require 'connect.php';
					$conn->query("INSERT INTO bot_work (time, chatid, url) VALUES ('".$today."', '".$chat_id."', '".$message."')");
					mysqli_close($conn);
					$dataSend = array(
						'text' => 'Ваша работа принята',
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;				
				default:
					$n=substr_count($message, '-');
					if ($n==1) {
						if (file_exists(trim($message).'.txt')) {
							require 'connect.php';
							$conn->query("UPDATE bot_spisok SET theme='".$message."' WHERE chatid='".$chat_id."'");
							mysqli_close($conn);
							$dataSend = array(
								'text' => 'Выберите, чем заняться. Поработать с теорией или порешать задачки?
								
								Тема: '.$this->themeTxt($message, $chat_id),
								'chat_id' => $chat_id,
								'reply_markup' => $this->getKeyBoard($this->stroimKbdMath(trim($message).'.txt')),
							);
						}
					} else {
						$dataSend = array(
							'text' => $message,
							'chat_id' => $chat_id,
							'reply_markup' => $justKeyboard,
						);
					}
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				}
		}	
	} 
	

    /** Меняем клавиатуру Vote
     * @param $data
     * @param $emogi
     * @param $callback_query_id
     */
    private function changeVote($data, $emoji, $callback_query_id)
    {
        $text = $this->requestToTelegram($data, "editMessageReplyMarkup")
            ? "Вы проголосовали " . hex2bin($emoji)
            : "Непредвиденная ошибка, попробуйте позже.";

        $this->requestToTelegram([
            'callback_query_id' => $callback_query_id,
            'text' => $text,
            'cache_time' => 30,
        ], "answerCallbackQuery");
    }

    /**
     * создаем inline клавиатуру
     * @return string
     */
    private function getInlineKeyBoard($data)
    {
        $inlineKeyboard = array(
            "inline_keyboard" => $data,
        );
        return json_encode($inlineKeyboard);
    }

    /**
     * создаем клавиатуру
     * @return string
     */
    private function getKeyBoard($data)
    {
        $keyboard = array(
            "keyboard" => $data,
            "one_time_keyboard" => false,
            "resize_keyboard" => true
        );
        return json_encode($keyboard);
    }

    /**
     * Парсим что приходит преобразуем в массив
     * @param $data
     * @return mixed
     */
    private function getData($data)
    {
        return json_decode(file_get_contents($data), TRUE);
    }

    /** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->botToken . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
	
	private	function inBase($chat_id)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseFirstName($chat_id)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."' AND first_name = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseSecondName($chat_id)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."' AND second_name = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseClass($chat_id)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."' AND class = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseOk($chat_id)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."' AND ok = 'ok'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function getProfile($chat_id)
    {
        require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		foreach ($rows as $row ) {
			$result = $row; 
			break;
		}
		mysqli_close($conn); 
        return $result;
   }
   
   	private	function stroimKbd($adr, $kolStr)
    {
		$result = [];
		$str = [];
		$fp = file($adr);
		$ks = 0;
		foreach($fp as $f) {
			$fd = explode('#', $f);
			$knopka = ['text' => $fd[0], 'callback_data' => $fd[1]];
			array_push($str, $knopka);
			unset($knopka);
			$ks++;
			if ($ks == $kolStr) {
				array_push($result, $str);
				$ks = 0;
				unset($str);
				$str = [];
			}
		}
		if ($ks > 0) array_push($result, $str);
		return $result;
	}
	
	private	function themeTxt($data, $chat_id)
    {
		$profile = $this->getProfile($chat_id);
		$adr = "themes".trim($profile['class']).".txt";
		$fp = file($adr);
		foreach($fp as $f) {
			$fd = explode('#', $f);
			if (trim($data) == trim($fd[1])) {
				$result = $fd[0];
				break;
			}
		}
		return $result;
	}

	private	function stroimKbdTh($data, $chat_id)
    {
		$profile = $this->getProfile($chat_id);
		$adr = trim($profile['theme']).".txt";
		$str = [];
		$result = [];
		$fp = file($adr);
		$flag = 0;
		foreach($fp as $f) {
			if ($flag == 1 && trim($f) != '@@') {
				if (trim($f) == '@') break;
				$fd = explode('#', $f);
				$knopka = ['text' => $fd[0], 'url' => $fd[1]];
				array_push($str, $knopka);
				array_push($result, $str);
				unset($knopka);
				unset($str);
				$str = [];
			} 
			if ($flag == 0 && trim($f) == trim($data)) $flag = 1;
		} 
		return $result;
	} 

	private	function stroimKbdMath($data)
    {
		$str = [];
		$result = [];
		$fp = file($data);
		$flag = 0;
		foreach($fp as $f) {
			if(stripos($f, '#') === FALSE && stripos($f, '@') === FALSE) {
				$knopka = ['text' => $f];
			} elseif (!empty($knopka) && stripos($f, '#') !== FALSE) {
				array_push($str, $knopka);
				unset($knopka);				
			} elseif (trim($f) == '@@' && !empty($str)) {
				array_push($result, $str);
				unset($str);
				$str = [];				
			}
		}
		if ($str) array_push($result, $str);
		$str = [['text' => 'Темы']];
		array_push($result, $str);
		$str = 	[["text" => "Сдать работу"], ["text" => "Вопрос учителю"]];
		array_push($result, $str);
		return $result;
	} 


}


















