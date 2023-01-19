\<?php
/*!
@file sql/config.php
@brief 
Цей файл призначен для добавлення прав адміністратора, 
а також відповідальних функцій за перевірку цілісності бази даних,
при відсутності таблиці створює нову із полями, якщо пошкоджена, то 
тільки повідомляє яка таблиця пошкоджена.<br>
Примір використання файлу<br>
php config.php (addAdmin, deleteAdmin) login (Примір 1)
<br>
@param addAdmin    - Мітка для виконання надавання доповнених прав.
<br>
@param deleteAdmin - Мітка для виконання пониження доповнених прав.
<br>
@param login - Логін користувача якому надати або позбавити доповнених прав.
<br>
php testTable - (примір 2)
<br>
@param testTable Перевірити цілісність таблиць або створити таблицю.
@warning 
Потенційно небезпечно зберігати цей файл в публічних папках сервера.
*/

/*!
@brief 
Функція призначена для підключення до бази даних, а також виконання SQL закликів.
@return Повертає результат заклику функції MySQLi->query()
*/
function workInSQL($query){
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
Функція для добавлення прав адміністратора сайту.
@param $login логін користувача якому змінити права доступу.
@param $TrueOrFalse поле типу boolian для добавлення або лишити права адміністратора сайту.
@return Тип Boolean для відгуку виконання заклику.
*/
function addRootUser($login,$TrueOrFalse){
    $sql = "update users set admin = '$TrueOrFalse' where login =\"".$login."\"";
   if(workInSQL($sql)){
    return true;
}else{
return false;}
}

/*!
@brief
Перевірка по почергово всі таблиці яки має цей сайт, а також поля таблиць
перевіряються  тільки за кількістю колонок в таблиці. 
@return
*/
function testTable(){
    //перевірка таблиці books
    $sql = "create table if not exists books(id int not null auto_increment primary key,
    name varchar(100) not null, describes varchar(500))";
   if(workInSQL($sql)){
    echo "\n Створена таблиця books \n";
   }else{
    echo "\n Таблиця books присутня \n";
   }
  //перевірка полів таблиці books 
   $sql = " select * from books";
  if(workInSQL($sql)->field_count == 3){
   echo "\n Всі поля присутні \n";
  }else{
   echo "\n Відсутні поля в таблиці books \n";
  }

    //перевірка таблиці bookAuthor
    $sql = "create table if not exists bookAuthor(idBook int not null ,
    author varchar(200) not null,
     foreign key (idBook) references books(id) )";
     
   if(workInSQL($sql)){
    echo "\n Вдале перевірення/створення таблиці bookAuthor \n";
   }else{
    echo "\n 'Не Вдале перевірення/створення таблиці bookAuthor' \n";
   }
  //перевірка полів таблиці bookAuthor 
   $sql = " select * from bookAuthor";
  if(workInSQL($sql)->field_count == 2){
   echo "\n Всі поля присутні \n";
  }else{
   echo "\n Відсутні поля в таблиці bookAuthor \n";
  }

    //перевірка таблиці bookGenre
    $sql = "create table if not exists bookGenre(idBook int not null ,
    genre varchar(200) not null,
     foreign key (idBook) references books(id) )";
     
   if(workInSQL($sql)){
    echo "\n Вдале перевірення/створення таблиці bookGenre \n";
   }else{
    echo "\n Не Вдале перевірення/створення таблиці bookGenre \n";
   }
  //перевірка полів таблиці bookGenre 
   $sql = " select * from bookGenre";
  if(workInSQL($sql)->field_count == 2){
   echo "\n Всі поля присутні \n";
  }else{
   echo "\n Відсутні поля в таблиці bookGenre \n";
  }


    //перевірка таблиці likeUserBook
    $sql = "create table if not exists likeUserBook(
        id int not null auto_increment primary key,
        idUser int not null,
        idBook int not null,
        foreign key (idUser) references users(id),
        foreign key (idBook) references books(id))";
     
   if(workInSQL($sql)){
    echo "\n Вдале перевірення/створення таблиці likeUserBook \n";
   }else{
    echo "\n Не Вдале перевірення/створення таблиці likeUserBook \n";
   }
  //перевірка полів таблиці likeUserBook 
   $sql = " select * from likeUserBook";
  if(workInSQL($sql)->field_count == 3){
   echo "\n Всі поля присутні \n";
  }else{
   echo "\n Відсутні поля в таблиці likeUserBook \n";
  }


 //перевірка таблиці users
 $sql = "create table if not exists users(
    id int not null auto_increment primary key,
    mail    varchar(350) not null,
    password varchar(100) not null,
    login   varchar(100) not null,
    admin   varchar(1)) ";
 
if(workInSQL($sql)){
echo "\n Вдале перевірення/створення таблиці users \n";
}else{
echo "\n Не Вдале перевірення/створення таблиці users \n";
}
//перевірка полів таблиці users 
$sql = " select * from users";
if(workInSQL($sql)->field_count == 5){
echo "\n Всі поля присутні \n";
}else{
echo "\n Відсутні поля в таблиці users \n";
}
}

// виконання дій по вхідних глобальних параметрів
switch($argv[1]){
    case 'deleteAdmin':{
        if(addRootUser($argv[2], 0)){
            echo "\n Вдало виконаний запит для користувача ".$argv[2];
        }else{
            echo "Помилка виконання запиту :". $qrgv[2]." , ".$arg[3];
        }
        break;
    }
    case 'addAdmin':{
        if(addRootUser($argv[2], 1)){
            echo "\n Вдало виконаний запит для користувача ".$argv[2];
        }else{
            echo "Помилка виконання запиту :". $qrgv[2]." , ".$arg[3];
        }
        break;
    }
    case 'testTable':{
        testTable();
        break;
    }
    default:{
        echo "\n Не розпізнанна команда!";
    }
}


?>
