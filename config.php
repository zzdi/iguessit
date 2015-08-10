<?php
/*
因为无法使用session或cookie，又不想使用数据库
用户历史猜测数据直接存储为：用户id.txt
请保证目录可写。
*/

header('Content-type:text/html; charset=utf-8');

define("DEBUG", true);
define("TOKEN", "xxx"); //填写微信对接的token
