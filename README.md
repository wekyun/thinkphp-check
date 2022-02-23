# thinkphp-check使用

参数接收验证插件,依赖TP框架的场景验证.

## 安装插件:

```composer
composer require wekyun/check
```

**插件只能是TP5.1及TP6可以使用**



## 使用说明:

1:使用之前需要先创建一个验证场景

```php
php think make:validate Common
```



2:在config文件目录下,创建配置文件:check.php

```
<?php
return [
	//默认错误的处理方法
    'err_func' => function ($msg, $err_code) {
        $json = json_encode([
            'msg' => $msg,
            'code' => $err_code,
        ], JSON_UNESCAPED_UNICODE);
        die($json);
    },
    'err_code' => 203,//默认错误的错误码
    //此配置为必须,配置需要使用的验证场景类
    'mapping' => [
        'com' => \app\common\validate\Common::class,
    ],
];
```



3:使用方法

```php
<?php
//1:使用com的场景验证类, 接收id参数,如果没有则默认值为33                 接收参数,并设置默认值
Check::check('com', 'id:33');

//2:使用com的场景验证类, 接收id参数,id参数为必传,不传就报错               接收必传参数,不传报错
Check::check('com', 'id.');


//3:接收多个参数,使用com的场景验证类, 多个参数之间使用英文的 , 分割;
//  .代表参数必传
//  :后面的值为设置的默认值
Check::check('com', 'id.,name.,age:18');
```


# Composer手册

使用手册地址: https://www.kancloud.cn/liqingbo27/composer/574854
