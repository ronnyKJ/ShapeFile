<?php

$array = array
    (
        'title'=>iconv('gb2312','utf-8','���������ı���'),
        'body'=>'abcd...'
    );
echo json_encode($array);
?>
