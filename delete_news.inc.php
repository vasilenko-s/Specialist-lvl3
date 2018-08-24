<?php

$del=$news->clearInt($_GET['id']);

//проверяем коректные ли данные и выполняем удаление
if (!is_integer($del)){
    header("Location: news.php");
} else {
    $res=$news->deleteNews($del);
}
//проверяем бы ли запрос успешен
if (!$res) {
    $errMsg="Произошла ошибка при удалении новости";
} else {
    //избавляемся от информации переданой через адресную строку
    header("Location: news.php");
}
