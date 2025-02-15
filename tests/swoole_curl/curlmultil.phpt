--TEST--
swoole_curl: curlmulti
--SKIPIF--
<?php
require __DIR__ . '/../include/skipif.inc';
?>
--FILE--
<?php declare(strict_types = 1);

Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

Co\run(function () {
    $nodes = array('https://www.google.com', 'https://www.php.net', 'https://openswoole.com');
    $node_count = count($nodes);

    $curl_arr = array();
    $master = curl_multi_init();

    for($i = 0; $i < $node_count; $i++)
    {
        $url =$nodes[$i];
        $curl_arr[$i] = curl_init($url);
        curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($master, $curl_arr[$i]);
    }

    do {
        curl_multi_exec($master,$running);
    } while($running > 0);

    for($i = 0; $i < $node_count; $i++)
    {
        $result = curl_multi_getcontent  ( $curl_arr[$i]  );
        echo( $i . ":" . (strpos($result, '<title>') > -1) . "\n");
    }
});

?>
--EXPECT--
0:1
1:1
2:1
