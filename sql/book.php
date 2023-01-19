<?php
/*!
@file sql/book.php
@brief
Цей файл містить всі основні функції для дії над книгою, за допомогою SQL запитів.
*/

/*!
@brief
Функція котра  шукає книги в базі даних і відображає їх в стилі HTML, пошук контролюється за допомогою вхідних параметрів.
@param $nameBook поле яке формує запит для бази даних що шукати.
@param $pole  поле яка формує запит по якому полі ведеться пошук.
@param $table поле яка формує запит в які таблиці шукати.
@param $searchFrame тип Boolean для контролю відображення рамок пошукового доповнення.


*/
function searchBookSQL($nameBook,$pole,$table, $searchFrame){
 $result =  querySQL("select * from ".$table." where ".$pole." like \"%".$nameBook."%\";");

    if ($result->num_rows > 0) {
        if($searchFrame)
        echo "<h1>Найдені книги по заклику  (".$result->num_rows.")</h1>" ;
        echo "<br><table >";
     while($row = $result->fetch_assoc()){
        echo "<form method='post' action='' >
        <tr><td><button name='enterBook' value='".$row['id']."'>".$row['name']."</button>";
         if(isAdmin()){echo"<button name='deleteBook' value='".$row['id']."'>Видалити книгу </button>";}
         echo" <button name='likeBook' value='".$row['id']."'>";
         if(auditLikeBook($row['id'])){Echo "Видалити книгу з улюблених";}else{echo "Додати книгу";}
         echo"</button> <br> Назва ".$row['name']."<br> Опис".$row['describes']."<br>автори:<br>";
         selectQuerySQL("bookAuthor", $row['id'],"idBook","author");
         echo "<br>Жанри:<br>";
         selectQuerySQL("bookGenre", $row['id'],"idBook","genre");
         echo"</td></tr></form>";
     }
     echo "</table>";
     if($searchFrame)echo "Кінець найдених книг " ;


    }else{
        if($searchFrame)echo "<h1>Немає знайдених книг </h1>" ;
    }
}

/*!
@brief 
Функція для добавлення автора до книги, одна книга може мати багато авторів,
які зберігаються в  таблиці bookAuthor, по закінченню показує відповідний запис.
@param $bookID поле яке містить  ідентифікатор книги.
@param $author поле яке містить  автора.
*/
function addAuthorSQL($bookID, $author){
if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null){
echo "Увійдіть в обліковий запис ";
return;}
if($author == null){echo "Порожнє поле автора";
    return;
}
if(querySQL("insert into bookAuthor(idBook, author) value('".$bookID."',\"".$author."\")")){
    echo "Вдало";
}else{
echo "Не вдало";

}
}
/*!
@brief 
Функція для виконання запиту SQL на добавлення жанру книги.
@param $BookID Поле яке містить ідентифікатор книги.
@param $genre Поле яке містить назву жанру для книги.
*/
function addGenreSQL($bookID, $genre){
    if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null){
    echo "Увійдіть в обліковий запис ";
    return;}
    if($genre == null){
        echo "Пусте поле жанр ";
        return;
    }
    if(querySQL("insert into bookGenre(idBook, genre) value('".$bookID."',\"".$genre."\")")){
        echo "Вдало ";
    }else{
    echo "Не вдало ";
    }
    }
/*!
@brief
Функція яка виконує запит SQL і відображає результат без додаткових полів.
@param $table поле містить назву таблиці.
@param $bookID поле яке містить що шукати.
@param $rows поле по якому шукати.
@param $columns поле містить назву колонки яку відображати.
*/

    function selectQuerySQL($table, $bookID, $rows,$columns){
    $result =  querySQL("select * from ".$table." where ".$rows." = '".$bookID."'");
        if ($result->num_rows > 0) {
         while($row = $result->fetch_assoc()){
            echo $row[$columns]."<br>";
         } 
        }else{
            echo "Немає знайдених заповнених книг " ;
        }  
    }
/*!
@brief
Функція яка додає або видаляє  книги в список улюблених, орієнтується на збереженої  інформації 
на боці користувача в COOKIES, а також перевіряє користувача перед виконанням дії добавлення/видалення. 
@warning При помилці перевірки користувача повертає без попередження.
@warning Також відображає інформацію завершення  мовою HTML.
@param $bookID Ідентифікатор книги яку потрібно додати або видалити.
*/
function addLikeBook($bookID){

    if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null){
        echo "Увійдіть в вашу обліковий запис";
        return;}


$sql = " select * from users where login =\"".$_COOKIE['login']."\" and password = \"".$_COOKIE['pwd']."\"";
if(!$idUser = querySQL($sql)->fetch_assoc()['id'])return;

if(auditLikeBook($bookID)){
    $sql = "delete  from likeUserBook where idUser='".$idUser."' and idBook='".$bookID."'";
}else{
    $sql = "insert into likeUserBook(idUser, idBook) value('".$idUser."','".$bookID."')";
}

        if(querySQL($sql)){
            if(auditLikeBook($bookID)){
            echo "<h3 style='color:red;'>добавлено книга в улюблені</h3>";}else {
               echo "<h3 style='color:red;'>Видалена книга</h3>"; 
            }
        }else{
        echo "<h3 style='color:red;'>Не добавленно</h3> ";
        }
}

/*!
@brief
Перевіряє легітимність  користувача, а також книги яки  користувач бажає додати до улюблених.
@warning  При відсутності користувача повертає на стартову сторінку  без пояснень.
@param $bookID Поле містить ідентифікатор книги.
@return повертає Boolean відповідно до наявності книги.
*/
function auditLikeBook($bookID){

    if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null){
        echo "<h3 style='color:red;'>Увійдіть  в вашу обліковий запис </h3>";
        return;}

$sql = " select * from users where login =\"".$_COOKIE['login']."\" and password = \"".$_COOKIE['pwd']."\"";
if(!$idUser = querySQL($sql)->fetch_assoc()['id'])return;

$sql = "select * from likeUserBook where idUser='".$idUser."' and idBook='".$bookID."'";

if(querySQL($sql)->num_rows >0){
    return true;
}
return false;
}

/*!
@brief
Виконує видалення книги із всіх таблиць.
@param $bookID Поле містить ідентифікатор книги.
*/
function deleteBook($bookID){
   
    querySQL("delete  from bookAuthor where  idBook='".$bookID."'");
    
    querySQL("delete  from bookGenre where  idBook='".$bookID."'");
    
    querySQL("delete  from likeUserBook where  idBook='".$bookID."'");
    
    querySQL("delete  from books where id = '".$bookID."'");
    
echo "<h3>Книга видалена<h3>";
}

/*!
@brief
Проводить пошук відповідної книги для відображення авторів і жанрів книги, а також 
cтворює меню  керування книгою на мові HTML.
@param $bookID Поле містить ідентифікатор книги.
@return При відсутності COOKIE користувача оправить на початкову сторінку попередив що потрібно війти,
в інших умовах ігнорується повернення.
*/
function selectAuthorOrGenre($bookID){
      
    if($_COOKIE['login'] == null | $_COOKIE['pwd'] == null){
        echo "Вийдіть в вашу обліковий запис   ";
        return;}

$sql = "select * from bookAuthor where  idBook='".$bookID."'";
$result = querySQL($sql);
echo "<form method='post' action='' ><select name='author' >";
 while($row =  $result->fetch_assoc()){
    echo "<option value='".$row['author']."' >".$row['author']."</option>";
}
echo "</select><button name='deleteAuthor' value='".$bookID."'> Видалити автора  </button>";
echo "<form method='post' action='' ><select name='genre' >";
$sql = "select * from bookGenre where  idBook='".$bookID."'";
$result = querySQL($sql);

while($row = $result->fetch_assoc()){
    echo "<option value='".$row['genre']."' >".$row['genre']."</option>";
}
echo "</select><button name='deleteGenre' value='".$bookID."'> Видалити автора  </button>";
echo"</form>";
}
/*!
@brief
Функція видалення автора із конкретної книги.
@param $bookID Поле містить ідентифікатор книги в котрі необхідно видалити автора.
@param $author Найменування автора котрого потрібно видалити.
*/
function deleteAuthor($bookID, $author){
 querySQL("delete from bookAuthor where idBook='".$bookID."' and author=\"".$author."\"");
}
/*!
@brief
Функція  видалення жанру із конкретної книги.
@param $bookID Поле містить ідентифікатор книги в якій потрібно видалити жанр.
@param $genre Поле яке містить назву жанру.
*/
function deleteGenre($bookID, $genre){
    querySQL("delete from bookGenre where idBook='".$bookID."' and genre=\"".$genre."\"");
}

/*!
@brief
Функція котра створює меню для керування книгою  на мові HTML, а  також вказує 
значення кнопок ідентифікатором конкретної книги, щоб можна було звертатися через POST запити.
@param $bookID поле яке містить ідентифікатор книги.
*/
function addAuthorOrGenre( $bookID ){
echo "<br><form active='' method='post'>
<input type='text' name='authorBook' value='' style='margin:7px; font-size: 24px;' ></input>
<br>
<button type='submit' value='".$bookID."' name='addAuthorBook' style='margin:7px; font-size: 24px;'>Додати автора до книги</button>
<br>
<input type='text' name='genreBook' value=''style='margin:7px; font-size: 24px;'></input>
<br>
<button type='submit' value='".$bookID."' name='addGenreBook' style='margin:7px; font-size: 24px;' >Добавити жанр до книги</button>
</form>";

}

/*!
@brief 
Функція котра відображає всі обрані книги конкретного користувача, який збережений в COOKIE,
використовується подвійний виклик SQL.
@return Якщо книги не знайдені відображає відповідний напис і  виходить.
*/
function showLikeBooks(){

   $sql = "select idBook from likeUserBook where idUser =
   (select id from users where login =\"".$_COOKIE['login']."\"and password = \"".$_COOKIE['pwd']."\")";
    $result = querySQL($sql);
    if($result->num_rows ==0){
        echo "<h3 style='color:red; font-size: 56px;' >У вас немає обраних книг</h3>";
        return;
    }
    while($row = $result->fetch_assoc()){
        searchBookSQL($row['idBook'], 'id', 'books', false);
    }
}
/*!
@brief
Функція для збереження файлу в якому будуть список всіх книг в форматі .csv
@param  $fileName назва файлу під яким зберегти файл.
*/
function csvBook($filename) {

    $sql = "select * from books";
    $result = querySQL($sql);


     $f = fopen('php://temp', 'wrt');
     $first = true;
     while ($row = $result->fetch_assoc()) {
         if ($first) {
             fputcsv($f, array_keys($row));
             $first = false;
         }
         fputcsv($f, $row);
     }
     $size = ftell($f);
     rewind($f);
     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
     header("Content-Length: $size");

     header("Content-type: text/x-csv; charset=WINDOWS-1251");
     header("Content-type: text/csv");
     header("Content-type: application/csv");
     header("Content-Disposition: attachment; filename=$filename");
     fpassthru($f);
     exit;
}
/*!
@brief
Функція для виконання запиту SQL на добавлення нової книги.
@param $name Поле яке містить назву книги.
@param $describes Поле яке містить обяснення книги Ілі примітки.
@return тип Boolean повертає для взаємодії і другими частинами программи.
*/
function addBookSQL($name, $describes){
         $sql = "insert into books(name, describes) values(\"".$name."\",\"".$describes."\")";
        if(querySQL($sql) == true){
         return true;
 }else{
     return false;}
 }
?>