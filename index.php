<link href="style.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<?php
include_once '../wp-load.php';

global $wpdb;

$wp_posts = $wpdb->prefix . 'posts';
$sql = "SELECT `post_title` FROM $wp_posts WHERE `post_status` = 'draft' AND `post_content` = '' ";
$posts = $wpdb->get_results( $wpdb->prepare($sql, null), ARRAY_A );
?>

<!--
<form action="" method="POST">
    <select name="post_title">
        <option selected value=""></option>
        <?php foreach ($posts as $post_title) : $title = $post_title['post_title']; ?>
            <option name="" value="<?php echo $title; ?>"><?php echo $title; ?></option>
        <?php endforeach; ?>
    </select>

    <br>

    <textarea placeholder="Сюда вставляйте текст в формате HTML">
    </textarea>

    <br>

    <p><input type="submit" name="add" value="Отправить"></p>

</form>
-->
<?php

 require_once 'XML/RPC2/CachedClient.php';
        $apikey = 'f844d5442d32cc3415b79352da2182ad';
        $options = array(
            'prefix' => 'contentmonster.',
            'cacheOptions' => array(
                'cacheDir' => '/tmp/',
                'lifetime' => 3600
            )
        );

        $client = XML_RPC2_CachedClient::create('https://contentmonster.ru/api/xmlrpc/', $options);
        try {
            $balance = $client->balance( $apikey );
            $getSites = $client->getSites( $apikey );
            $zakonchen = $client->getOrdersId( $apikey, 12 );

            //$orderStatus = $client->getOrderStatus( $apikey, 416504 );
            //print_r( $orderStatus );



        } catch (XML_RPC2_FaultException $e) {
            die('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString());
        } catch (Exception $e) {
            die('Exception : ' . $e->getMessage());
        }



/*
$db = dbase_open( 'rclose.dbf', 0 );
if ( $db ) {
    echo "файk открыт для чтения";
} else {
    echo "ошибка";
}
*/
//phpinfo();


?>

<html>
<body>
<div class="main">

<div class="links">
    <a href="#export">На экспорт</a> (<?php echo count($zakonchen); ?>)
    <span style="float: right;"><a href="addOrder.php">Cоздать заказ</a></span>
</div>

<span>
    Баланс: <?php print_r( $balance['wmr'] ); ?>, руб.
    Резерв: <?php print_r( $balance['wmr_reserve'] ); ?>, руб.
</span>


        <?php
            foreach ($getSites as $name) {
                $nnm[] = $name['name'];
                //echo $nnm[0] . "<br>";
            }

            foreach ($getSites as $id_project) {
                $id[] = $id_project['id'];
            }
        ?>

        <table border="1">
            <tr>
                <td align='center'>
                    <h4>проект</h4>
                </td>
                <td align='center'>
                    <h4>id</h4>
                </td>
            </tr>
            <tr>
                <td>
                <?php
                foreach ($nnm as $imya) {
                    echo $imya."<br>";
                }
                ?>
                </td>

                <td>
                    <?php
                    foreach ($id as $ids) {
                        echo $ids."<br>";
                    }
                    ?>
                </td>

            </tr>


        </table>

        <div class="getOrders" width="100%">
            <h3 id="export">Законченные заказы (на экспорт) <?php echo count($zakonchen); ?>шт.</h3>
            <form action="export.php" method="post">
            <?php
            foreach ($zakonchen as $id_zakaza ) : $export = $client->export( $apikey, $id_zakaza );
            ?>
            <table class="zakonchen_zakaz">
                <hr size=5 color="black">
                <tr class="header_title" align="center" valign='top'>
                    <td>id</td>
                    <td>Название</td>
                    <td>Тайтл</td>
                    <td>Описание</td>
                    <td>Текст</td>
                    <td>Примечание автора</td>
                    <td>Размер статьи</td>
                    <td>Стоимость</td>
                    <td>Экспорт</td>
                </tr>

                <tr valign='top'>
                    <td><?php echo $export['id']; ?></td>
                    <td><?php echo $export['name']; ?></td>
                    <td><?php echo $export['title']; ?></td>
                    <td><?php echo $export['desc']; ?></td>
                    <td><?php echo $export['text']; ?></td>
                    <td><?php echo $export['write_note']; ?></td>
                    <td><?php echo $export['length']; ?></td>
                    <td><?php echo $export['write_payall']; ?>, руб.</td>
                    <td>
                        <input class="btn btn-primary" type="submit" name="exprt" value="<?php echo $export['id']; ?>"></td>
                    </td>
                </tr>
            </table>
            <?php endforeach; ?>
            </form>
        </div>

</div> <!--//main -->

</body>
</html>
