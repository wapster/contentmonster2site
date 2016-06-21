<?php
if ( !empty( $_POST ) ) {

           $id = $_POST['exprt'];

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
               $export = $client->export( $apikey, intval($id) );
           } catch (XML_RPC2_FaultException $e) {
               die('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString());
           } catch (Exception $e) {
               die('Exception : ' . $e->getMessage());
           }

        include_once '../wp-load.php';
        global $wpdb;

        // Находим дату опубликованного, либо запланированного поста
        $tbl_posts = $wpdb->prefix . 'posts';
        $date_future_post = $wpdb->get_row( $wpdb->prepare( "SELECT `post_date` FROM $tbl_posts WHERE `post_status` = 'future' ORDER BY `post_date` DESC", null ) );
        if ($date_future_post == '') {
            $date_publish_post = $wpdb->get_row( $wpdb->prepare( "SELECT `post_date` FROM $tbl_posts WHERE `post_status` = 'publish' ORDER BY `post_date` DESC", null ) );
            echo $date_publish_post->post_date;
        }
        //echo $id_posts->post_date;


        // Генерирует случайную дату увеличенную на `$count_day` дней от `$post_date` даты
        function randomDate($post_date, $count_day) {
        	$date = date_create( $post_date );

        	$days = "+$count_day day";
        	$hours   = "+" . strval( rand( 1, 12) )  ." hours";
        	$minutes = "+" . strval( rand( 10, 45) ) ." minutes";
        	$seconds = "+" . strval( rand( 1, 59) ) ." seconds";

        	date_modify( $date, "$hours $minutes $seconds $days" );
        	$future_random_day = date_format( $date, 'Y-m-d H:i:s' );

        	return $future_random_day;
        }
        echo "<pre>".randomDate($date_publish_post->post_date, 1)."</pre>";


        $publish_date = date( 'Y-m-d H:i:s' );
        $post_data = array(
            'post_author' 		=> 1,
            'post_date'	  		=> $publish_date,
            'post_date_gmt' 	=> $publish_date,
            'post_title'  		=> $export['name'],
            'post_status'		=> 'publish',
            'post_content'      => $export['text'],
            'post_category'     => array( 14 ), //ID категорий для публикации
            'tags_input'        => 'peni',      // Метки
            'post_modified' 	=> $publish_date,
            'post_modified_gmt' => $publish_date,
            'post_type'   		=> 'post'
            );

        //$post_id = wp_insert_post( wp_slash( $post_data ) );
        echo "Заказ id " . $id . " опубликован.<br>";
        echo "id поста = " . $post_id;
        echo "<p><a href='index.php'>На главную</a></p>";
} else {
    echo "Ошибка получения id заказа";
}
?>
