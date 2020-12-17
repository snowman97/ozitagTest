<!DOCTYPE html>
<html>
	<head>
		<title>test</title>
		<link rel="stylesheet" type="text/css" href="css.css">
		<meta charset="utf-8">
	</head>
	<body style="width: 70%;margin: 30px auto;">

		<? 
		include_once('simple_html_dom.php');
		$html = file_get_html('https://www.realt.by/rent/cottage-for-long/?page=0', false);
		$count = $html->find('.uni-paging span a')[0]->innertext - 1;

		?>

		<label>Максимум <? echo $count; ?> страниц(ы)</label>
		<form method="POST">
		<p>С какой страницы:<br> 
		<input type="text" name="from" /></p>
		<p>Сколько страниц: <br> 
		<input type="text" name="before" value="1"/></p>
		<input style="display: none;" type="text" name="maxLength" value="<? echo $count; ?>"/>
		<input type="submit" value="Отобразить услуги">
		</form>


		<hr>
		<span>Фильтр</span>
		<div>
			<form method="POST">
				<input type="submit" name="filterPriceUp" value="по цене +">
				<input type="submit" name="filterPriceDown" value="по цене -">
				<input type="submit" name="filterIdUp" value="по ид +">
				<input type="submit" name="filterIdDown" value="по ид -">
			</form>
		</div>
		<hr>
		<?php
		require_once 'recording.php'; // подключаем скрипт
		require_once 'connection.php'; // подключаем скрипт
		 
		// подключаемся к серверу
		$link = mysqli_connect($host, $user, $password, $database) 
		    or die("Ошибка " . mysqli_error($link));
		 
		// выполняем операции с базой данных

	    if (isset($_POST['filterPriceUp'])) {
	     	$query = "SELECT * FROM `property` ORDER BY `property`.`price` ASC ";
	     } 
	    elseif (isset($_POST['filterPriceDown'])) {
	    	$query = "SELECT * FROM `property` ORDER BY `property`.`price` DESC";
	    }
	    elseif (isset($_POST['filterIdUp'])) {
	    	$query = "SELECT * FROM `property` ORDER BY `property`.`id` ASC ";	    	
	    }
	    elseif (isset($_POST['filterIdDown'])) {
	    	$query = "SELECT * FROM `property` ORDER BY `property`.`id` DESC ";	    	
	    }
	    else{     	
			$query ="SELECT * FROM property";
	    }

		$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
		if($result)
		{
		?>

		<div class="inner-center-content">
			<div class="csc-default">
			<?
			$rows = mysqli_num_rows($result); // количество полученных строк
			    for ($i = 0 ; $i < $rows ; ++$i)
			    {
			        $row = mysqli_fetch_row($result);        
			?>

					<div class="bd-item" data-mode="2" data-page="0" data-region="0">
					    <div class="title">
					        <div class="media">
					            <div class="media-body"><? echo html_entity_decode($row[1]); ?></div>
					        </div>
					    </div>
					    <div class="bd-item-left">
					        <div class="bd-item-left-top">
					                <img
					                    class="lazy"
					                    data-original="<? echo html_entity_decode($row[6]); ?>"
					                    src="<? echo html_entity_decode($row[6]); ?>"
					                    style="display: inline;"
					                    width="180"
					                    height="120"
					                />
					        </div>
					        <div class="bd-item-left-bottom">
					            <div class="bd-item-left-bottom-bottom">
					                <div class="bd-item-left-bottom-right">
					                    <p>
					                        <span class="price-byr">
					                        	<? 
							                        if ($row[2] == 0.000) {
							                        	echo "Цена договорная";
							                        }
							                        else{
							                        	echo $row[2]; 
													}
												?>
				                        	</span><br />			                        
					                    </p>
					                </div>
					            </div>
					        </div>
					    </div>
					    <div class="bd-item-right">
					        <div class="bd-item-right-top">
					            <span class="views fl mr10"><? echo html_entity_decode($row[0]); ?></span>
					            <p class="fl f11 grey"><? echo html_entity_decode($row[5]); ?></p>
					        </div>
					        <? echo html_entity_decode($row[4]); ?>

					        <div class="bd-item-right-bottom">
					            <div class="bd-item-right-bottom-left" style="background-color: #fff; padding: 0;">
					                <p class="mb0" style="color: #000; font-weight: bold;">
					                     <? echo html_entity_decode($row[3]); ?>  			                    
					                </p>
					            </div>
					        </div>
					    </div>
					</div>
				<?  }	?>
			</div>
		</div>

		<?
		    // очищаем результат
		    mysqli_free_result($result);
		}

		// закрываем подключение
		mysqli_close($link);

		?>
	</body>
</html>