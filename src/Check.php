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
//        dd($check_name);
        try {
            if (!self::$mapping) {
                $check = config('check.');
                if (!$check) return self::err_json('验证配置为空');
                if (!isset($check['mapping'])) return self::err_json('验证配置:mapping 未定义');
                if (count($check['mapping']) == 0) return self::err_json('验证配置:mapping 为空');
                self::$mapping = $check['mapping'];
            }
            $param = \think\facade\Request::param('');
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
                        //判断必须
                        if (strpos($value, '.')) {
                            $value_more = explode('.', $value);
                            $check_key = $value_more[0];
                            if (!self::check_isset_value($param, $check_key)) {
                                return self::err_json($value_more[0] . ':必须');
                            }
                        }
                        if ($check_key === null) $check_key = $value;

                        if (strpos($check_key, ':')) {
                            $check_key_more = explode(':', $check_key);
                            $check_key = $check_key_more[0];
                            if (!self::check_isset_value($param, $check_key)) {
                                if ($check_key_more[1]) {
                                    $default_data[$check_key] = $check_key_more[1];
                                } else {
                                    $default_data[$check_key] = NULL;
                                }
                            }
                            //直接验证接受的所有参数是否合法，
                            if (!$obj->check($param)) {
                                return self::err_json($obj->getError());
                            }
                        } else {
                            if (!self::check_isset_value($param, $check_key)) {
                                $default_data[$check_key] = NULL;
                            }
                            if (!$obj->check($param)) {
                                return self::err_json($obj->getError());
                            }
                        }
                    }

                }


//            dump($default_data);
                $res_data = array_merge($param, $default_data);
                return $res_data;
            }
        } catch (Exception $exception) {
            return self::err_json($exception->getMessage());
        }
    }

    //判断是否有值
    private static function check_isset_value($param, $check_key)
    {
        if (!isset($param[$check_key])) {
            return false;
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


}

