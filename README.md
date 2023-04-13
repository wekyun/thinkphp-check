# thinkphp-check使用

参数接收验证插件,依赖TP框架的场景验证.



**插件只能是TP5.0以及TP5.1可以使用，TP6还未尝试测试使用**





## 1：安装插件:

```composer
composer require wekyun/check
```



## 2：创建基础验证类

<!--使用之前需要先创建一个验证场景：-->

**TP5.0**请在框架的 `application/common/` 目录下创建创建一个文件夹 `validate` ，然后创建文件 `Common.php` 即可。

**TP5.1**创建验证类文件的命令行，直接在项目根目录打开终端运行即可

```php
php think make:validate Common
```



如果创建的位置是 `application\common\validate\Common.php ` ,文件内容请可以直接复制粘贴成如下:

~~~
<?php

namespace app\common\validate;

use think\Validate;

class Common extends Validate
{
    /*
     *  验证的字段，插件接受的字段如果与$rule的键相同的，就会验证其定义的规则，
     *
     *  例如：$param = Check::check('com', ['cs_email.']);
     *  就会先验证 cs_email 字段是否传参，如果没有传就会调用配置文件check.php 的 err_func的这个方法提示
     *  如果字段传递了，就会验证框架的内置规则，假设cs_email = 123 ，就会提示： {"msg":"cs_email格式不符","code":203}  提示的格式和字段，可以修改err_func的这个方法
     *
     *  更多的验真规则，请查阅框架内置规则： 验证也支持正则哦
     *  TP5.1: https://static.kancloud.cn/manual/thinkphp5_1/354107
     *  TP5.0: https://static.kancloud.cn/manual/thinkphp5/129356
    * */
    protected $rule = [
        'cs_name' => 'max:25',
        'cs_age' => 'number|between:1,120',
        'cs_email' => 'email',
    ];

    protected $message = [

    ];

    protected $scene = [
    ];

}
~~~



## 3：创建配置文件

**TP5.0**请在`application/extra/` 目录下创建配置文件 `check.php` 。

**TP5.1**请在根目录的 `config` 目录下,创建配置文件:check.php

文件内容如下：err_func 是验证参数错误的处理方法。可以自行修改

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



## 4：代码中使用

1：**使用插件接受接收参数，要使用的字段，就必须要接收定义，如果不接收就直接使用，会提示不存在，务必谨记！**

2：**接受的参数会返回一个数组，$param 就是所有接受的参数**

~~~
//参数的接收
$param = Check::check('com', 'id,name,sex,mobile');				//字符串形式
$param = Check::check('com', ['id','name','sex','mobile']);		//数组形式，推荐此形式

$param = Check::check('com', 'id.');//字段id为必传   .表示必传
$param = Check::check('com', 'sex:0');//接收sex字段，如果不传默认值为0

$param = Check::check('com', 'mobile.');//mobile为必传   .表示必传
$param = Check::check('com', 'mobile|手机号.');//手机号必传   .表示必传 |表示自定义的提示名称

$param = Check::check('com', 'mobile.>请输入手机号码');//字段mobile为必传   .表示必传 >表示不传递参数的提示信息

//字段mobile为必传  .表示必传 >表示不传递参数的提示信息 |表示自定义的提示名称，并且如果手机号码格式不正确会提示手机号码格式不正确，如果不定义则提示mobile格式不正确
$param = Check::check('com', 'mobile|手机号码.>请输入手机号码');//不传会提示请输入手机号码，传错了会提示 手机号码格式错误


~~~



```php
<?php
1:使用com的场景验证类, 接收id参数,如果没有则默认值为33                 接收参数,并设置默认值
    //  :后面的值为设置的默认值
	Check::check('com', 'id:33');

2:使用com的场景验证类, 接收id参数,id参数为必传,不传就报错               接收必传参数,不传报错
    //  .代表参数必传
	Check::check('com', 'id.');


3:接收多个参数,使用com的场景验证类, 多个参数之间使用英文的 , 分割;
//  .代表参数必传
//  :后面的值为设置的默认值
	Check::check('com', 'id.,name.,age:18');

4:数组使用方式
    //规则都是一样的
    Check::check('com', ['id.','name.','age:18']);
```

## 5指定参数不验证规则

~~~
//mobile字段前面增加了英文的感叹号：! ，所以手机号这个字段不会验证是否正确，有些时候手机号不是必填的，可能会给0，就不会验证非法
$param = Check::check('com', ['name|名称.','sex.>请选择性别','!mobile|手机号']);

~~~



## 6：手册

*  TP5.1: https://static.kancloud.cn/manual/thinkphp5_1/354107
 *  TP5.0: https://static.kancloud.cn/manual/thinkphp5/129356
 *  Composer使用手册地址: https://www.kancloud.cn/liqingbo27/composer/574854
