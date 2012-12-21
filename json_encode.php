<?php

$array = array
    (
        'title'=>iconv('gb2312','utf-8','这里是中文标题'),
        'body'=>'abcd...'
    );
echo json_encode($array);
?>
