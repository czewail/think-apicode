# think-apicode
think-apicode 是基于thinkphp5的简单的接口数据生成工具
## 安装
```base
composer require ppeerit\apicode
```
## 配置
在扩展配置目录新建配置文件apicode.php
```php
return [
	'type' => 'json',//输出格式
];
```
## 加载
```php
use Ppeerit\Apicode\Apicode;
```
## 使用
输出错误信息
```php
// 参数为[错误信息，错误代码，数组形式的数据];
Apicode::error(['error message','9999', $data]);
```
输出成功信息
```php
// 参数为[成功信息，跳转地址，数组形式的数据];
Apicode::success(['success message',$url, $data]);
```
