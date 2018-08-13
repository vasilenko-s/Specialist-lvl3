<?php

require_once "INewsDB.class.php";

class NewsDB implements INewsDB {
    //имя базы
    const DB_NAME = "../news.db";
    //сама база, хранение экземпляра класса
    private $_db = null;

    //обеспечиваем доступ к базе для классов наследников
    function __get($name){
        try {
            if ($name == 'db')
                return $this->_db;
            throw new Exception('Wrong parametr');
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    function __construct()
    {
        //создаем бд (или подключение)
        $this->_db = new SQLite3(self::DB_NAME);
        if (filesize(self::DB_NAME) == 0){
            //создание таблицы msgs
            $sql="CREATE TABLE msgs(
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT,
                    category INTEGER,
                    description TEXT,
                    source TEXT,
                    datetime INTEGER
                    )";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());
            //создание таблицы category
            $sql="CREATE TABLE category(
                    id INTEGER,
                    name TEXT
                   )";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());
            //Заполнение таблицы category
            $sql="INSERT INTO category(id, name)
                    SELECT 1 as id, 'Политика' as name
                    UNION SELECT 2 as id, 'Культура' as name
                    UNION SELECT 3 as id, 'Спорт' as name ";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());

        }

    }
    function __destruct()
    {
        //удаляем объект
        unset($_db);
    }

    function saveNews($title, $category, $description, $source){
        $dt=time();
        //формируем запрос на вставку записи
        $sql="INSERT INTO msgs
                      (title,
                       category,
                       description,
                       source,
                       datetime                      
                      )
                      VALUES
                      ('$title',
                        $category,
                       '$description',
                        '$source',
                         $dt                      
                      )";

        //выполняем запрос
        return $this->_db->exec($sql);
    }

    private function db2Arr(SQLite3Result $data){
        $arr=[];
        //обработка стандартной выборки данных типа "результат запроса"
        while ($row=$data->fetchArray(SQLITE3_ASSOC))
            $arr[]=$row;
        return $arr;
}
    function getNews(){
         $sql="SELECT msgs.id as id,
                    title,
                    category.name as category,
                    description,
                    source,
                    datetime
                FROM msgs, category
                WHERE category.id = msgs.category
                ORDER BY msgs.id DESC";
        $res=$this->_db->query($sql);
        if(!$res) echo "Ошибка выполнения запроса!";
            return  $this->db2Arr($res);
    }

    function deleteNews($id){
        //cформировать запрос
        $sql = "DELETE
                FROM msgs
                WHERE id={$id}";
        //выполнить
        $res=$this->_db->exec($sql);
        //возвратить значение успех/ошибка
        return $res; 
    }

    //фильтрация данных
    function clearInt($data){
        return abs((int)$data);
    }
    function clearStr($data){
        $data=strip_tags($data);
        return $this->_db->escapeString($data);
    }

}

