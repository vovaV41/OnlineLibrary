<?php
/*!
@file page/addBook.html
@brief 
Візуальна частина для добавлення книги в базу даних.<br>
Цей файл призначено для відображення полів і стилю добавлення книги.
В основному файлі index.php використовується як поле
@param $addBook яке відображається через оператор echo
Винесено в файл, щоб не нагромадити код і додане розширення файлу HTML,
 щоб IDE допомогло писати й не помилялась в визначеному форматі  мови сторінки.
*/
include 'page/addBook.html';
/*!
@file page/usloviaPage.html
@brief
Візуальна частина  умов користувача.
Використовується як поле строкове 
@param $usloviaPage для відображення під час проходження процесу реєстрації.
*/
include 'page/usloviaPage.html';
/*!
@file page/startPage.html
@brief
Сторінка для початкового відображення містить стилі, а також поля для реєстрації
@param $startPage поле для відображення вмісту файлу.
*/
include 'page/startPage.html';
/*!
@file page/akaPage.html
@brief
Файл для відображення сторінки користувача, а також стиль.
@param $akaPage поле для відображення сторінки користувача за допомогою оператора echo.
*/
include 'page/akaPage.html';
/*!
@file page/adminPage.html
@drief
Файл зберігає одне поле в якому зберігаються параметри візуальної частини  сторінки адміністратора
у форматі html.
@param $adminPage поле для відображення змісту файлу через оператора echo. 
*/
include 'page/adminPage.html';

// Інформація зберігається в відповідному файлі.
include 'sql/user.php';
include 'sql/book.php';

/*!
@author MS_puziKO
@mainpage Ця  документація презначенна для обясненя бібліотеки онлайн 
@brief Опис ідеї  програми

Перший мій проєкт на мові PHP, до цього я не коли не знав про структуру і синтаксис 
але знаю основи ООП та синтаксис, діалект С, який допомагає орієнтуватися в інших мовах програмування.<br>
У мене це зайняло близько двох тижнів весь процес, через цю причину що багато не знав не вистачило часу
 на отладку коду сайту, а також формувати його більше стійким і погано його спроєктував.
<br>

Також не знав деталі та ідею GitHub, зараз вважаю дуже потрібний цей інструмент.<br>
Також не знав про Doxygen, за допомогою якого я  сформував документацію в форматі HTML, 
вибрав іменно цей формат, тому що мені показалось це дуже іронічним "Создати сайт який розповідає про сайт".<br>
Також я уяви не мав про PlanUML, на котрий я потратив час для написання, хоча примітивну діаграму.

<br>
Цей веб застосунок розрахован на  POST запроси він орієнтований на те, щоб позбавити 
початківця-вломника позбавити спокуси використати GET запроси для ушкодження інтернет
 застосунку  для опитних це не поміха, ідея дуже погано продумана, бажав застосувати тільки один
файл для керування всього процесу відповіді користувачеві, початкова ідея 
була в тому щоб  розподілити  файли HTML, SQL, а також PHP в різних папках з 
доцільними  назвами, але через те що погано спроєктував  цей сайт  вийшло трішки 
 не так структуровано.<br>
 <br>
Розроблялось це все на програмному забеспеченью:<br>
Kali GNU Linux Rolling;<br>
Apache/2.4.54 (Debian):2022-10-12T07:20:52<br>
MySQL  Ver 15.1 Distrib 10.6.11-MariaDB, for debian-linux-gnu (x86_64)<br>
Документація створена за допомогою Doxygen.
*/
// $timeCookie  зміна для  задання часу зберігання інформації на боці користувача 2000 секунд.
$timeCookie = 2000;
//@startuml
if($_POST['exit']){
    setcookie("mail",'',0);
    setcookie("pwd",'',0);
    setcookie("login",'',0);
    setcookie("uslovie","1",0);
    header("Location:");
}

if(isAdmin()){
    if($_POST['csvAllBook']){
    csvBook("books.csv");
exit;
}
    goto adminStart;
}
//@enduml
if($_COOKIE['access'] != null)
{
    echo "<h3 style='background-color: red;>Заповніть  всі поля для реєстрації<h3>";
    setcookie("access","",0);
}

if($_COOKIE['uslovie'] == "0")
if(!accessUser($_COOKIE["login"],$_COOKIE["pwd"], true))
{
    setcookie("uslovie","1",time()+$timeCookie);
    echo $usloviaPage;
    heaser("Location:");
}


if(accessUser($_COOKIE["login"], $_COOKIE["pwd"], true)){
    if(accessUser($_COOKIE["login"], $_COOKIE["pwd"], false)){
        /* функції не адміна */
        echo $akaPage;
        switch(true){
            case $_POST['likeBook'] :{
                addLikeBook($_POST['likeBook']);
                 auditLikeBook($_POST['likeBook']);
                  searchBookSQL("","id","books",false);
                  break;
            }
            case $_POST['startEnterBook']:{
                searchBookSQL("","id","books",false);
                break;
            }

            case $_POST['showLikeBook']:{
                showLikeBooks();
                break;
            }

        case $_POST['searchBook']:{
        searchBookSQL($_POST['searchBooks'],"name","books",true);
        break;
    }
    case $_POST['enterBook']:{
    searchBookSQL($_POST['enterBook'],"id","books",false);
    break;
    }
            default:{
                searchBookSQL("","id","books",false);
                break;
            }
        }
    }else{
        echo "<center><h1 style='background-color: red;'>Не вірний  пароль<h1></center> <br>".$startPage ;
        setcookie("mail",'',0);
        setcookie("pwd",'',0);
        setcookie("login",'',0);
        setcookie("uslovie","1",0);
    }

if( $_POST['exit']){
       setcookie("mail",'',0);
       setcookie("pwd",'',0);
       setcookie("login",'',0);
       setcookie("uslovie","1",0);
       header("Location:");
       }
}else{ 
    // не зареєстровані можливості користувача 
        switch(true){
        case $_POST['yes']:{
             //додать акаутн в базу
             if($_COOKIE['mail'] == null){
                $proBro = true;
             }
             insertUserSQL();
             header("Location:");
            break;
        }
        case $_POST['enter']:{
            setcookie("pwd", md5($_POST['password']), time()+$timeCookie);
            setcookie("mail", $_POST['mail'], time()+$timeCookie);
            setcookie("login", $_POST['login'], time()+$timeCookie);
            setcookie("uslovie","0",time()+$timeCookie);
            header("Location:");
           break;
       }
       case $_POST['enterBook']:{
        echo $startPage;
        searchBookSQL($_POST['enterBook'],"id","books",false);
       break;
   }
       default :{
        echo $startPage;
        searchBookSQL("","name","books",false);
       }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

return;
// розділ для адміністрування
adminStart:

echo $adminPage;
switch(true){
    case $_POST['addBook']:{
        echo $addBook;
        break;
    }
    case $_POST['addNewBook']:{
         addBookSQL($_POST['nameBook'],$_POST['describesBokk']);
        break;
    }
    case $_POST['searchBook']:{
        searchBookSQL($_POST['searchBooks'],"name","books",true);
        break;
    }
    case $_POST['addAuthorBook']:{
        addAuthorSQL($_POST['addAuthorBook'],$_POST['authorBook']);
        searchBookSQL($_POST['addAuthorBook'],"id","books",true);
        addAuthorOrGenre($_POST['addAuthorBook']);
        break;
    }
    case $_POST['addGenreBook']:{
        addGenreSQL($_POST['addGenreBook'],$_POST['genreBook']);
        searchBookSQL($_POST['addGenreBook'],"id","books",true);
        addAuthorOrGenre($_POST['addGenreBook']);
        break;
    }
    case $_POST['enterBook']  :{
        searchBookSQL($_POST['enterBook'],"id","books",true);
        addAuthorOrGenre($_POST['enterBook']);
        selectAuthorOrGenre($_POST['enterBook']);
        break;
    }
    case $_POST['deleteBook']  :{
       deleteBook($_POST['deleteBook']);
        searchBookSQL($_POST['enterBook'],"id","books",true);
        break;
    }
    case $_POST['deleteAuthor']  :{
       deleteAuthor($_POST['deleteAuthor'],$_POST['author'] );
         searchBookSQL($_POST['deleteAuthor'],"id","books",true);
         break;
     }
     case $_POST['deleteGenre']  :{
        deleteGenre($_POST['deleteGenre'],$_POST['genre'] );
          searchBookSQL($_POST['deleteGenre'],"id","books",true);
          break;
      }
    case $_POST['likeBook']:{
        addLikeBook($_POST['likeBook']);
      echo  auditLikeBook($_POST['likeBook']);
        searchBookSQL("","id","books",true);
        break;
    }
    case $_POST['showLikeBook']:{
        showLikeBooks();
        break;
    }
    default:{
        searchBookSQL("","name","books",false);
        break;
    }
}
?>
