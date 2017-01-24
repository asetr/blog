<html>
<head>
    <title>BLOG ADMIN</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-3.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-ui.js"></script>
</head>
<body>
<div class="container-fluid">
    <button class="btn btn-info btn-lg" type="button" data-toggle="modal" data-target="#add_post">Add New Post</button>
    <div id="add_post" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h3 class="modal-title" style="text-align: center;">Add post</h3>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <label>
                            Title: <br/>
                            <input name="title" type="text"/>
                        </label>
                        <p>
                            <label>
                                Upload image: <br/>
                                <input name="img" type="file"/>
                            </label>
                        </p>
                        <label>Content: <br/>
                            <textarea id="content" name="content" rows="5" cols="60"></textarea>
                        </label>
                        <br/>
                        <button type="submit">Save</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <p>Previous posts:</p>

    <!--    <div class="container-fluid">-->
    <ul id="navigation" class="nav nav-pills nav-stacked col-lg-2"></ul>
    <div id="navigation_content" class="tab-content col-lg-10"></div>
    <!--    </div>-->

    <?php
    date_default_timezone_set("Europe/Helsinki");
    //базаданных типа sqlite
    $db_path = "sqlite:myblog.sqlite";
    $db = new PDO($db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_FILES)) {
        if ($_FILES['img']['error'] > 0) {
            switch ($_FILES['img']['error']) {
                case 1:
                    echo 'Размер файла слишком большой';
                    break;
                case 2:
                    echo 'Размер файла слишком большой';
                    break;
                case 3:
                    echo 'Загружена только часть файла';
                    break;
                case 4:
//                    echo 'Файл не загружен';
                    break;
            }
        } else {
            //если файл загружен и ошибок нет
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
//                //содержимое массива $_FILES
//                echo "<pre>";
//                //var_dump($_FILES);
//                echo "</pre>";
                //$fname = $_FILES['img']['name'];

                //источник для картинки из файла
                // находим временное имя загруженного файла, вычленяем, начиная с php~
                $source = $_FILES['img']['tmp_name'];
                preg_match('/php\w*/', $source, $matches); //оставляем временные имена файлов (для случая, когда имя загружаемого файла совпадает с уже имеющимся)


                //перемещаем найденный файл
                $dest = "img" . "/$matches[0]";
                move_uploaded_file($source, $dest);
            }
        }
    }

    try {
        $db->beginTransaction();
        if (!empty($_POST)) {
            if ($_POST['title'] != "") {
                extract($_POST);
                //меняем кавычки на html-сущности ->защита от sql-инъекции
                $title = htmlentities($title, ENT_QUOTES);
                $pubdate = time();
                if (isset($source)) {
                    $sql = "INSERT INTO posts(title, text, date, picture_path) values ('$title', '$content', '$pubdate', '$dest')";
                } else {
                    $sql = "INSERT INTO posts(title, text, date) values ('$title', '$content', '$pubdate')";
                }
                $db->exec($sql);
            }
        }
        $db->commit();

        //готовим запрос к БД для вывода на экран
        $st = $db->prepare("SELECT * FROM posts ORDER BY date DESC");
        $st->execute();

        foreach ($st->fetchAll() as $row) {
            ?>
            <script>
                var newLi = document.createElement('li');
                <?php $published_str = date('F d, Y', $row['date']) . " at " . date('g:i:s A', $row['date']); ?>
                newLi.innerHTML = "<a data-toggle='tab' href='#post<?php echo $row['id']; ?>'><?php echo $published_str; ?><br><?php echo $row['title']; ?></a>";
                document.getElementById("navigation").appendChild(newLi);

                var newContent = document.createElement('div');
                newContent.setAttribute('id', 'post<?php echo $row['id']; ?>');
                newContent.setAttribute('class', "tab-pane fade");
                newContent.innerHTML = "<?php
                    echo "<article><header><h3>{$row['title']}</h3></header>";
                    if ($row['picture_path'] != "" && $row['picture_path'] != NULL) {
                        echo "<img src='" . $row['picture_path'] . "' width=200px/>";
                    }
                    //замена разрывов строк на перевод каретки, "\r\n" - для винды
                    $c = str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($row['text'], ENT_QUOTES));
                    //вывод готовой записи на экран
                    echo "<p>" . $c . "</p>";
                    echo "<footer><span style='font-size: 12px'>Published at: {$published_str}</span></footer></article>";
                    ?>";
                    //можно так: document.getElementById("navigation_content").appendChild(newContent)
                document.getElementById('navigation_content').appendChild(newContent);
            </script>
            <?php
        }
    } catch (PDOException $ex) {
        $db->rollBack();
        echo "<p style='color:red'>";
        echo $ex->getMessage();
        echo "</p>";
    }
    ?>
</div>
</body>
</html>
