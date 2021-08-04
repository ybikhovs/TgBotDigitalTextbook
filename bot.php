<?php

// https://imakebots.ru/article/klaviatura-kak-sposob-vzaimodeystviya-s-botom
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
    private $botToken = "1982672684:AAGhdgfjshGFGofColX8Sez8IWL_tWhgd768P8Y";
    // адрес для запросов к API Telegram
    private $apiUrl = "https://api.telegram.org/bot";

    public function init($data)
    {
        // Пароль для дуступа к боту
		$pswd = '111';
		
		// создаем массив из пришедших данных от API Telegram
        $arrData = $this->getData($data);
		$username = $arrData['message']['from']['username'];
        // лог
        // $this->setFileLog($arrData);

        if (array_key_exists('message', $arrData)) {
            $chat_id = $arrData['message']['chat']['id'];
            $message = $arrData['message']['text'];

        } elseif (array_key_exists('callback_query', $arrData)) {
            $chat_id = $arrData['callback_query']['message']['chat']['id'];
            $message = $arrData['callback_query']['data'];
        }

		$justKeyboard = $this->getKeyBoard([[["text" => "Темы"]]]);
		
		if ($this->inBaseOk()!=1) {
			if ($this->inBase()==0) {
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
					$conn->query("INSERT INTO bot_spisok (logintg, first_name, second_name, class, ok, theme, math) VALUES ('".$username."', '0', '0', '0', '0', '0', '0')");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");
				}
			} else {
				if ($this->inBaseFirstName()==1) {
					$txt = "А теперь, ".$message.", введите Вашу фамилию.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET first_name='".$message."' WHERE logintg='".$username."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");			
				} elseif ($this->inBaseSecondName()==1) {
					$profile = $this->getProfile();
					$txt = "Теперь, ".$profile['first_name'].", выберите Ваш класс.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbd('class.txt', 4)),
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET second_name='".$message."' WHERE logintg='".$username."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");			
				} elseif ($this->inBaseClass()==1) {
					$profile = $this->getProfile();
					$txt = "Спасибо, ".$profile['first_name'].". 
					
					Нажми кнопку 'Темы', чтобы выбрать тему для изучения.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $justKeyboard,
					);
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET class='".$message."', ok='ok' WHERE logintg='".$username."'");
					mysqli_close($conn);				
					$this->requestToTelegram($dataSend, "sendMessage");	
				}
			}
		} else {
			switch ($message) {
				case 'Темы':
					$profile = $this->getProfile();
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
						'text' => "Помощь",
						'chat_id' => $chat_id,
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				case (preg_match('/^Задания/', $message) ? true : false):
				case (preg_match('/^Теория/', $message) ? true : false):
					require 'connect.php';
					$conn->query("UPDATE bot_spisok SET math='".$message."' WHERE logintg='".$username."'");
					$dataSend = array(
						'text' => $message,
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbdTh($message)),
					);
					$this->requestToTelegram($dataSend, "sendMessage");
					break;
				default:
					$n=substr_count($message, '-');
					if ($n==1) {
						if (file_exists(trim($message).'.txt')) {
							require 'connect.php';
							$conn->query("UPDATE bot_spisok SET theme='".$message."' WHERE logintg='".$username."'");
							mysqli_close($conn);
							$dataSend = array(
								'text' => 'Выберите, чем заняться. Поработать с теорией или порешать задачки?
								
								Тема: '.$this->themeTxt($message),
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
	
	private	function inBase()
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseFirstName()
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."' AND first_name = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseSecondName()
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."' AND second_name = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseClass()
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."' AND class = '0'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function inBaseOk()
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."' AND ok = 'ok'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function getProfile()
    {
        require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_spisok WHERE logintg = '".$username."'");
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
	
	private	function themeTxt($data)
    {
		$profile = $this->getProfile();
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

	private	function stroimKbdTh($data)
    {
		$profile = $this->getProfile();
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
		$knopka = ['text' => 'Темы'];
		array_push($str, $knopka);
		array_push($result, $str);
		return $result;
	} 


}


















