<?php
require_once 'connection.php'; // подключаем скрипт
if(isset($_POST['from']) && isset($_POST['before']) && isset($_POST['maxLength'])){
  include_once('simple_html_dom.php'); // подключаем скрипт  
  $from = (int)$_POST['from'];
  $before = (int)$_POST['before'];
  $maxLength = (int)$_POST['maxLength'];
  $count = $from + $before - 1;
    // подключаемся к серверу
    $link = mysqli_connect($host, $user, $password, $database) 
        or die("Ошибка " . mysqli_error($link)); 

    mysqli_query($link, "DELETE FROM property");
    mysqli_query($link, "ALTER TABLE property AUTO_INCREMENT = 1");

    if ( $count > $maxLength || $from < 0 || $before <= 0) {
      echo "ошибка ввода";
      return  false;
    }

function recordingProperty ($countT, $link){

  $html = file_get_html('https://www.realt.by/rent/cottage-for-long/?page='. $countT, false);

  if (!empty($html)) {

    $content = $html->find('div.bd-item');

      if (!empty($content)) {
        foreach( $content as $element){
          $name = $element->find('.media-body a')[0]->innertext;
          $price = $element->find('.bd-item-left-bottom-right .price-byr')[0]->innertext;
          $price = preg_replace('/[^0-9]/', '', $price);
          if (strlen($price) > 3) {
           $price = substr_replace($price, ".", -3, 0);
          }
          elseif (strlen($price) == 3) {
            $price = substr_replace($price, "0.", 0, 0);
          }
          elseif (strlen($price) < 2) {
            $price = 0;
          }
          
          $phone = $element->find('.bd-item-right-bottom-left a')[0]->getAttribute('data-full');
          $description = $element->find('.bd-item-right-center')[0]->innertext;
          $date = $element->find('.bd-item-right-top p')[0]->innertext;
          $date = mb_strimwidth($date, -10, 10);
          $img = $element->find('img.lazy')[0]->getAttribute('data-original');

          // экранирования символов для mysql
          $item['name'] = htmlentities(mysqli_real_escape_string($link, $name));
          $item['price'] = htmlentities(mysqli_real_escape_string($link, $price));
          $item['phone'] = htmlentities(mysqli_real_escape_string($link, $phone));
          $item['description'] = htmlentities(mysqli_real_escape_string($link, $description));
          $item['date'] = htmlentities(mysqli_real_escape_string($link, $date));
          $item['img'] = htmlentities(mysqli_real_escape_string($link, $img));

          // $articles[] = $item;   
          // создание строки запроса
          $query = "INSERT INTO property (name, price, phone, description, date, images) VALUES('" . $item['name'] . "','" . $item['price'] . "','" . $item['phone'] . "','" . $item['description'] . "','" . $item['date'] . "','" .$item['img'] . "')";
           
          // выполняем запрос
          $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));      

        }
      }
      else{
        echo "false content";
      }
    }
    else{
        echo "Страница не найдена =/";
    } 

  }




    for ($i = $from; $i <= $count ; $i++) { 
      recordingProperty($i, $link);
    }
    echo "<span style='color:blue;'>Данные добавлены</span>";




    // закрываем подключение
    mysqli_close($link);
}
?>