<?php

$dir = __DIR__ . '/upload_files/';

for($i=1;$i<=10;$i++)
{
    file_put_contents
    (
        $dir . hash('crc32', random_bytes(128)) . '.jpg',
        file_get_contents('https://picsum.photos/200/200?random')
    );
}