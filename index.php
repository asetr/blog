<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Blog Home - Start Bootstrap Template</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>

</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Some Blog</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#">About</a>
                </li>
                <li>
                    <a href="#">Services</a>
                </li>
                <li>
                    <a href="#">Contact</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <h1 class="page-header">
                BLOG
                <small> PHP homework</small>
            </h1>

            <div id="content"></div>

            <!-- Pager -->
            <ul class="pager">
                <li class="previous">
                    <a id="prev" href="#">&larr; Older</a>
                </li>
                <li class="next">
                    <a id="next" href="#">Newer &rarr;</a>
                </li>
            </ul>
        </div>

        <!-- Blog Sidebar Widgets Column -->
        <div class="col-md-4 col-lg-4 sticky-block">

            <!-- Blog Search Well -->
            <div class="well inner">
                <h4>Blog Search</h4>
                <div class="input-group">
                    <form class="form-inline" method="get">
                        <div class="form-group">
                            <input name="search" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                    <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                    </span>
                        </div>
                    </form>
                </div>
                <!-- /.input-group -->
            </div>

            <!-- Blog Categories Well -->
            <div class="well">
                <h4>Blog Categories</h4>
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="list-unstyled">
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <ul class="list-unstyled">
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                            <li><a href="#">Category Name</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.col-lg-6 -->
                </div>
                <!-- /.row -->
            </div>

            <!-- Side Widget Well -->
            <div class="well">
                <h4>Side Widget Well</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore, perspiciatis adipisci accusamus
                    laudantium odit aliquam repellat tempore quos aspernatur vero.</p>
            </div>

        </div>

    </div>
    <!-- /.row -->

    <hr>

    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12">
                <p>Copyright &copy; <a href="mailto:alexander.trushenko@yahoo.com.ua">Alexander Trushenko</a> 2017</p>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </footer>

</div>
<!-- /.container -->

<?php
//часовой пояс
date_default_timezone_set("Europe/Helsinki");
//количество записей на одной странице
$num_rec_per_page = 4;

// соединяемся с нашей локальной базой данных sqlite
$db_path = "sqlite:myblog.sqlite";
$db = new PDO($db_path);
//режим вывода ошибок
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Query
// Определение количества строк в базе данных и расчет количества страниц.
//запрос с ответом, перебираем колонки
$total_rows = $db->query("SELECT COUNT(*) as count FROM posts")->fetchColumn();
//округление количесива страниц в бОльшую сторону
$total_pages = ceil($total_rows / $num_rec_per_page);

//защита: если в массиве $_GET есть номер страницы
isset($_GET["page"]) ? $page = $_GET["page"] : $page = 1;
//иначе начинаем с нулевой (в массиве) страницы
$start_from = ($page - 1) * $num_rec_per_page;
//определяем номер предыдущей строки
$prev_page = implode(array($_SERVER['PHP_SELF'], "?page=", $page - 1));
//определяем номер следующей строки
$next_page = implode(array($_SERVER['PHP_SELF'], "?page=", $page + 1));

if (empty($_GET['search'])) {
    //вытаскиваем записи по убыванию даты публикации, огранививаясь текущей страницей
    $sql = "SELECT * FROM posts ORDER BY date DESC LIMIT $start_from, $num_rec_per_page";
    $st = $db->prepare($sql);
    $st->execute();
    //??
} else {
    extract($_GET);
    $st = $db->prepare("SELECT * FROM posts WHERE title LIKE :filter ORDER BY date DESC LIMIT $start_from, $num_rec_per_page");
    $st->execute(['filter' => "%$search%"]);
}

// Upload data from respond to page
foreach ($st->fetchAll() as $row) {
    $published_str = date('F d, Y', $row['date']) . " at " . date('g:i:s A', $row['date']);
    ?>
    <script>
        var newContent = document.createElement('div');
        //для нашего div задаем id с именем post + соответствующий id записи
        // в таблице из нашей БД, например, post1
        newContent.setAttribute('id', 'post<?php echo $row['id']; ?>');
        //внутрь созданного div'а помещаем
        newContent.innerHTML = "<?php
            echo "<h2><a href='post.php?id=" . $row['id'] . "'>{$row['title']}</a></h2><p class='lead'> by <a href='mailto:alexander.trushenko@yahoo.com.ua'>Alexander Trushenko</a></p><p><span class='glyphicon glyphicon-time'></span> Posted on {$published_str}</p><hr>";
            //если загружается картинка, т.е. указан путь к ней в БД
            if ($row['picture_path'] != "" && $row['picture_path'] != NULL) {
                //выводим картинку на экран в теге img
                echo "<img class='img-responsive' src='" . $row['picture_path'] . "' alt=''/>";
            }
            //в тексте сообщения заменяем все разрывы строки на перенос строки типа <br>

            $c = substr(str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($row['text'], ENT_QUOTES)), 0, 500);
            echo "<hr><p>" . $c . " <a href='post.php?id=" . $row['id'] . "'>...</a></p>";
            //клик по гиперссылке -> выводим полный текст записи
            echo "<a class='btn btn-primary' href='post.php?id=" . $row['id'] . "'>Read More <span class='glyphicon glyphicon-chevron-right'></span></a><hr>";
            ?>";
            //добавляем полученный html-блок к общему html-содержимому

        document.getElementById('content').appendChild(newContent);

        // Создаем ссылки только на существующие страницы
        <?php
        if ($page <= 1) {
            $prev_page = implode(array($_SERVER['PHP_SELF'], "?page=1"));
        }
        if ($page >= $total_pages) {
            $next_page = implode(array($_SERVER['PHP_SELF'], "?page=$total_pages"));
        }
        ?>

        document.getElementById("prev").setAttribute("href", "<?php echo $next_page; ?>");
        document.getElementById("next").setAttribute("href", "<?php echo $prev_page; ?>");
    </script>
<?php } ?>
</body>
</html>
