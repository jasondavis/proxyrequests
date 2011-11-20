<?php

/**
 * @author Luka Pušić <luka@pusic.si>
 */
$content = file_get_contents('http://checkerproxy.net/'.date('d-m-Y'));
preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,5}/', $content, $match);


$fh = fopen("proxy.txt", "w");
$count = count(array_unique($match[0]));
for ($i = 0; $i < $count; $i++) {
    fwrite($fh, $match[0][$i]."\n");
}
fclose($fh);
?>