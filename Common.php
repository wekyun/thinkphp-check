<?php

namespace app\common\validate;

/*
 * TP的验证场景类
 * $rule代表了验证的字段,和使用的内置验证规则
 *
 * 原则上可以一个功能模块创建以个验证场景使用
 * */

use think\Validate;

class Common extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id' => 'number',
        'shop_id' => 'number',
        'shop_name' => 'chsDash',
        'doctor_name' => 'chsDash',
        'admin_name' => 'chsDash',
        'pwd' => 'alphaDash',
        'mobile' => 'mobile',
        'sex' => 'integer',
        'age' => 'date',
        'desc' => 'max:255',

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [];

}
