<?php
/*!
@file sql/user.php 
@brief
Функції користувача.
Задум створити файл який буде відповідати за всі можливості користувача, а також надавати відповідні права.
*/

// змінна для вказання терміну зберігання пароля і логіна на стороні користувача
$timeCookieAccess = 1000;
/*!
@brief Функція для з'єднання з базою даних.
Відповідає за з'єднання з базою даних, а також виконання заклику до бази даних, 
Зберігає в собі параметри з'єднання з базою.
@param $query сформований заклик до бази даних.
@return  повертає результат заклику із бази даних MySQL 
*/
function querySQL($query){

    $database = 'shop';
    $host = '127.0.0.1';
    $user = 'client';
    $password = '';
    $conn = new mysqli($host, $user,$password, $database);
    if ($conn->connect_error) {
        die("Помилка підключення : " . $conn->connect_error);
    }
    $result  = $conn->query($query);
    $conn->close();
    return $result;

}
/*!
@brief 
Добавлення користувача.<br>
Функція  була створена для добавлення нового користувача в базу даних, яка бере інформацію із
Збереженої інформації на стороні користувача її COOKIE.
@return тип  Boolean  для інформування спілкування с другими частинами коду.
*/
function insertUserSQL(){
    if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null | $_COOKIE['mail']== null){
        setcookie("access","not",time()+$timeCookieAccess);
        return;
    }
    $sql = "insert into users(mail, password, login, admin) values (\"".$_COOKIE['mail'].
    "\", \"".$_COOKIE['pwd']."\",\"".$_COOKIE['login']."\",0);";
   if(querySQL($sql) == true){
   return true;
     }else{
        return false;
     }
    
}
/*!
@brief Функція перевірки користувача.<br>
Перевіряє  наявність користувач в базі даних.
@param $login Вхідне поле для пошуку користувача.
@param $password пароль для порівняння із записаним в базі даних.
@return Тип Boolean для повідомлення результату успішності заклику.
*/
function accessUser($login, $password, $proverka){
    switch($proverka){
        case true:{  
            if($login == null ){
            return false;
        }
        $sql = null;
        $sql = "SELECT  id  FROM users WHERE  login = \"".$login."\""; 
            break;
        }
        case false:{  
             if( $login == null & $password == null){
            return false;}
            $sql = null;
            $sql = "SELECT  id  FROM users WHERE  password = \"".$password."\" and login = \"".$login."\"";
            break;
        }
    }
   $result =  querySQL($sql);
    if ($result->num_rows > 0) {
       return true;
    } else {
        return false;
    }
}
/*!
@brief
Перевірка прав адміністратора<br>
Перевіряє додаткові права користувача.
@return повертає типу Boolean для повідомлення результату заклику.
*/
function isAdmin(){
        if (querySQL("select id from users where login = \"".$_COOKIE["login"]."\" and admin = 1")->num_rows > 0) {
           return true;
        } else {
            return false;
        }
}

?>
