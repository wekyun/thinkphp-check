<?php
/**
 * Created by PhpStorm.
 * UserValidate: hg
 */

namespace Check;

use think\Exception;
use think\Request;

/**
 * @title 验证类
 *
 */
class Check
{
    private static $class = [];//容器对象池

    //预定义验证场景类
    private static $mapping = [];


    /*
     * 配置参考
     * <?php
        return [
                'mapping' => [
                    'com' => \app\common\validate\Common::class,
                ],
            ];
     *
     * */


    /** 调用指定验证场景类的场景
     * @autho hugang
     * @param string $name 场景类名
     * @param string $check_name 场景名
     * @return 调用指定验证场景类的场景
     * */
    public static function check($name = '', $check_name = '')
    {
        $check_key = false;
        $tips_name = false;
//        age|年龄  表示接收字段age  提示的时候提示 年龄
//        . 表示是必传
//        > 表示使用自定义的提示信息   注意 . 和 >同时使用, . 在 > 前面


//        Check::check('com', ['age|年龄.>请输入年龄']);
//        Check::input('com',['mobile.>请输入手机号码']);//提示:请输入手机号码
//        Check::input('com',['mobile.手机号码']);//提示:手机号码为必传字段
//        try {
        if (!self::$mapping) {
            $c = config('');
            $check = $c['check'];
            unset($c);
            if (!$check) return self::err_json('验证配置为空');
            if (!isset($check['mapping'])) return self::err_json('验证配置:mapping 未定义');
            if (count($check['mapping']) == 0) return self::err_json('验证配置:mapping 为空');
            self::$mapping = $check['mapping'];
        }
        $param = input('');
        if (!$name || !$check_name) {
            return $param;
        }
        $obj = null;
        if (isset(self::$class[$name])) {
            $obj = self::$class[$name];
        } elseif (isset(self::$mapping[$name])) {
            $class = self::$mapping[$name];
            $obj = new $class();
            self::$class[$name] = $obj;
        }
        $default_data = [];
        if ($obj) {
            if (is_string($check_name)) {
                $check_name = explode(',', $check_name);
            }
            if (is_array($check_name)) {
                foreach ($check_name as $key => $value) {
                    $check_key = null;
                    if (strpos($value, '>.')) {
                        return self::err_json('验证规则书写错误了 . 必须在 > 前面');
                    }
                    //判断必须
                    if (strpos($value, '.')) {
                        $value_more = explode('.', $value);
                        $check_key_data = explode('|', $value_more[0]);

                        $check_key = $check_key_data[0];
                        if (isset($check_key_data[1]) && self::is_mast($check_key_data[1])) {
                            $tips_name = $check_key_data[1];
                        } else {
                            $tips_name = $check_key;
                        }
                        if (self::check_isset_value($param, $check_key) == false) {
                            if (self::is_mast($value_more[1])) {
                                $tis_data = explode('>', $value_more[1]);
                                if (count($tis_data) > 1) {
                                    return self::err_json($tis_data[1]);
                                } else {
                                    if ($tips_name == $check_key) {
                                        return self::err_json($tips_name . '是必须');
                                    } else {
                                        return self::err_json($tips_name . '是必须:' . $check_key);
                                    }
                                }
                            }
                            return self::err_json($tips_name . '必须:' . $check_key);
                        }
                    }
                    if ($check_key === null) $check_key = $value;
                    if (strpos($check_key, ':')) {
                        $check_key_more = explode(':', $check_key);
                        $check_key = $check_key_more[0];
                        if (!self::check_isset_value($param, $check_key)) {
                            if (strlen($check_key_more[1]) > 0) {
                                $default_data[$check_key] = $check_key_more[1];
                            } else {
                                $default_data[$check_key] = '';
                            }
                        }
                        //直接验证接受的所有参数是否合法，
                        if (!$obj->check($param)) {
                            $err_msg = str_replace($check_key, $tips_name, $obj->getError());
                            return self::err_json($err_msg);
//                                return self::err_json($obj->getError());
                        }
                    } else {
                        if (!self::check_isset_value($param, $check_key)) {
                            $default_data[$check_key] = '';
                        }
                        if (!$obj->check($param)) {
                            $err_msg = str_replace($check_key, $tips_name, $obj->getError());
                            return self::err_json($err_msg);
//                                return self::err_json($obj->getError());
                        }
                    }
                }
            }
            $res_data = array_merge($param, $default_data);
            return $res_data;
        }
//        } catch (Exception $exception) {
//            $err_msg = $exception->getMessage();
//            $err_msg = str_replace($check_key, $tips_name, $err_msg);
//            return self::err_json($err_msg);
//        }
    }

    //判断是否有值
    private static function check_isset_value($param, $check_key)
    {
        if (!isset($param[$check_key])) {
            return false;
        }
        if (is_string($param[$check_key]) && strlen($param[$check_key]) > 0) {
            return true;
        }

        if (is_array($param[$check_key]) && count($param[$check_key]) > 0) {
            return true;
        }

        if (!$param[$check_key]) {
            if ($param[$check_key] === 0 || $param[$check_key] === '0') {
                return true;
            }
            return false;
        }
        return true;
    }

    //错误提示
    private static function err_json($msg)
    {
        $err_func = config('check.err_func');
        if ($err_func) {
            return $err_func($msg, config('check.err_code', 203));
        }
        return exception($msg, config('check.err_code', 203));
    }

    /** 验证变量是否存在有值
     * @autho hugang
     * @param string $val 场景类名
     * @return 验证变量是否存在有值
     * */
    private static function is_mast($val)
    {
        if (isset($val) && strlen($val) > 0) {
            return true;
        }
        return false;
    }

}

