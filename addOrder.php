<link href="style.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

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
       $subjectList = $client -> subjectList( $apikey );
       $getSites    = $client -> getSites( $apikey );

       } catch (XML_RPC2_FaultException $e) {
           die('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString());
       } catch (Exception $e) {
           die('Exception : ' . $e->getMessage());
       }

    //    echo "<pre>";
    //    print_r( $getSites );
    //    echo "</pre>";
?>

<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
<body>

<form class="" action="" method="post">
    <label for="subjectList">Тематика</label>
    <select class="form-control" name="subjectList">
        <?php foreach ($subjectList as $key=>$value ) : ?>
                <option name="<?php echo $value['name']; ?>" value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <label for="getSites">Проекты</label>
    <select class="form-control" name="getSites">
        <?php foreach ($getSites as $id=>$name ) : ?>
                <option name="<?php echo $name['name']; ?>" value="<?php echo $name['id']; ?>"><?php echo $name['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <input type="submit" name="create_order" value="Создать заказ">

</form>

<?php
if ( !empty($_POST['create_order']) ) {

    $string = file( 'keys.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

    // Формируем итоговый массив
    $full_array = array();

    foreach ($string as $fa) {
        $second_array = explode( '+', $fa );
        $full_array[] = $second_array;
    }

    foreach ($full_array as $key => $value) {

        $title = $value[0];
        $desc = 'description';
        $keywords = '';

        $x = count( $value );
        if ( $x > 1) {

            // Убираем 1-й элемент массива,
            //т.к. это основной ключ = название статьи
            array_shift( $value );

            // и формируем список ключевых слов для статьи
            $keywords = implode(", " , $value);

            for ($i=1; $i < $x; $i++) {

                $params = array(
                            'site_id' => 10262,
                            'task' => 1,
                            'subject_id' => 29,
                            'write_time_limit' => 36,
                            'len_min' => 300,
                            'len_max' => 600,
                            'write_pay' => 50,
                            'bezprob' => 1,
                            'wmtype' => 1,
                            'payall' => 1,
                            'uniq_status' => 1,
                            'uniq_min' => 85,
                            'name' => $title,
                            'description' => $desc,
                            'keywords' => $keywords,
                            'mdesc' => 1,
                            'min_desc' => 30,
                            'max_desc' => 70,
                            'autoselect' => 1,
                            'autoselectlevel' => 3,
                            'nocoment' => 1,
                            'tender_type' => 1,
                            'tender_time_limit' => 36
                          );


            }
        } else {

            // Если в задании не указаны ключевые слова
            $params = array(
                        'site_id' => 10262,
                        'task' => 1,
                        'subject_id' => 29,
                        'write_time_limit' => 36,
                        'len_min' => 300,
                        'len_max' => 600,
                        'write_pay' => 50,
                        'bezprob' => 1,
                        'wmtype' => 1,
                        'payall' => 1,
                        'uniq_status' => 1,
                        'uniq_min' => 85,
                        'name' => $title,
                        'description' => $desc,
                        'keywords' => $keywords,
                        'mdesc' => 1,
                        'min_desc' => 30,
                        'max_desc' => 70,
                        'autoselect' => 1,
                        'autoselectlevel' => 3,
                        'nocoment' => 1,
                        'tender_type' => 1,
                        'tender_time_limit' => 36
                      );
        }


        try {
           $createOrder = $client -> createOrder( $apikey, $params );

           } catch (XML_RPC2_FaultException $e) {
               die('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString());
           } catch (Exception $e) {
               die('Exception : ' . $e->getMessage());
           }

        if ( $createOrder ) {
            echo "Заказ размещен<br>";
        } else {
            echo "ошибка :(<br>";
        }

    }



}
?>


</body>
</html>
