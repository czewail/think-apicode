<?php
namespace Ppeerit\Apicode;

use Ppeerit\Apicode\Exceptions\DataTypeInvalidException;
use think\Config;
use think\Response;

class Apicode
{
    /**
     * @const string Data type json
     */
    const DATA_TYPE_JSON = 'json';

    /**
     * @const string Data type xml
     */
    const DATA_TYPE_XML = 'xml';

    //默认配置
    protected $_config = [
        'type' => 'json',
    ];

    /**
     * 默认输出类型
     * @var [type]
     */
    protected $data_type = self::DATA_TYPE_JSON;

    /**
     * 可调用的类型列表
     * @var array
     */
    protected $data_types_available = [
        self::DATA_TYPE_JSON,
        self::DATA_TYPE_XML,
    ];

    /**
     * @var object 对象实例
     */
    protected static $instance;

    /**
     * 全局系统信息编码 -- 9900+ 其他地方不得使用9900-10000的编码
     * @var [type]
     * @author 陈泽韦 549226266@qq.com
     */
    private static $msgs = [
        //''    =>    '',
        '10000' => 'OK', //成功
        '9999'  => 'UNKNOWN ERROR', //未知错误
        '9998'  => 'PARAM ERROR', //参数错误
        '9997'  => 'ILLEGAL ACTION', //非法操作
    ];

    /**
     * 构造方法
     * @param array $config [description]
     */
    public function __construct()
    {
        if (Config::has('apicode')) {
            $config        = Config::get('apicode');
            $this->_config = array_merge($this->_config, $config);
        }
        $this->setDataType($this->_config['type']);
    }

    /**
     * 构造错误数据
     * @param  array  $data   [description]
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public static function error(array $data = [])
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        $result = self::$instance->createError($data);
        return self::$instance->setReturnData($result, 404);
    }

    /**
     * 构造成功信息
     * @param  array  $data   [description]
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public static function success(array $data = [])
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        $result = self::$instance->createSuccess($data);
        return self::$instance->setReturnData($result);
    }

    /**
     * 构造错误信息
     * @param  string $code [description]
     * @param  string $msg  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function createError(array $data = [])
    {
        $errer_data = [];

        $errer_data['msg']  = isset($data[0]) ? $data[0] : 'error';
        $errer_data['code'] = isset($data[1]) ? $data[1] : '9999';
        $errer_data['data'] = isset($data[2]) ? $data[2] : null;

        $error_default = [
            'status'    => 0,
            'timestamp' => time(),
            'url'       => 'javascript:history.back(-1);',
        ];
        $result = array_merge($error_default, $errer_data);
        return $result;
    }

    /**
     * 构造成功信息
     * @param  string $msg  [description]
     * @param  string $url  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function createSuccess(array $data = [])
    {
        $success_data = [];

        $success_data['msg']  = isset($data[0]) ? $data[0] : 'ok';
        $success_data['url']  = isset($data[1]) ? $data[1] : '';
        $success_data['data'] = isset($data[2]) ? $data[2] : null;

        $success_default = [
            'status'    => 1,
            'code'      => '10000',
            'timestamp' => time(),
        ];
        $result = array_merge($success_default, $success_data);
        return $result;
    }

    /**
     * json数据构造
     * @param  array   $data    [description]
     * @param  integer $code    [description]
     * @param  array   $header  [description]
     * @param  array   $options [description]
     * @return [type]           [description]
     */
    protected function json($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'json', $code, $header, $options);
    }

    /**
     * 设置返回结果类型
     * @param [type] $data_type [description]
     */
    protected function setDataType($data_type)
    {
        //    检查参数合法性
        if (!in_array($data_type, $this->data_types_available)) {
            //    抛出异常
            throw new DataTypeInvalidException('ApiCode data type ' . $data_type . ' is invalid.');
        }
        $this->data_type = $data_type;
    }

    /**
     * 根据需要格式返回数据
     * @param string  $data    [description]
     * @param integer $code    [description]
     * @param array   $header  [description]
     * @param array   $options [description]
     */
    protected function setReturnData($data = '', $code = 200, $header = [], $options = [])
    {
        return call_user_func_array([$this, $this->data_type], [$data, $code, $header, $options]);
    }

    /**
     * xml数据构造
     * @param  array   $data    [description]
     * @param  integer $code    [description]
     * @param  array   $header  [description]
     * @param  array   $options [description]
     * @return [type]           [description]
     */
    protected function xml($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'xml', $code, $header, $options);
    }
}
