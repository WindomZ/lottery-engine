# lottery-engine

> 一个抽奖组件引擎 - 模块化且易部署

[![Latest Stable Version](https://img.shields.io/packagist/v/windomz/lottery-engine.svg?style=flat-square)](https://packagist.org/packages/windomz/lottery-engine)
[![Build Status](https://img.shields.io/travis/WindomZ/lottery-engine/master.svg?style=flat-square)](https://travis-ci.org/WindomZ/lottery-engine)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum MYSQL Version](https://img.shields.io/badge/mysql-%3E%3D%205.6-4479a1.svg?style=flat-square)](https://www.mysql.com/)

[English](https://github.com/WindomZ/lottery-engine/blob/master/README.md#readme)

## 特性

- [x] 玩法 - 怎么玩
- [x] 玩法规则 - 玩的规则
- [x] 奖品 - 玩的奖品
- [x] 记录 - 玩的记录

## 安装

在项目目录中打开终端：
```bash
$ composer require windomz/lottery-engine
```

创建一个配置文件，比如`config.yml`：
```yaml
database_host: 127.0.0.1
database_port: 3306
database_type: mysql
database_name: lotterydb
database_username: root
database_password: root
database_charset: utf8
database_logging: false
database_json: true # 是否支持json字段
```

如果仅用于快速测试，
您可以在`MySQL`中运行`./sql/lotterydb.sql`来快速创建一个测试数据库。

当然，您也可以根据`./sql/lotterydb.sql`自定义`database name`，
但是请注意`table name`_不能改动_！

在项目初始化代码中，
通过以下实现加载指定的配置文件：
```php
Lottery::setConfigPath('./config.yml');
```

## 用法

有关详细信息，请参阅[文档](https://windomz.github.io/lottery-engine)。

## 局限

> 因为没有内存缓存系统，如`Memcached`、`Redis`，而是通过`shmop`函数来共享内存操作，
这带来了一些性能瓶颈，如有必要，可以fork并重写`\LotteryEngine\Model\Cache`类来接入。

## 许可

The [MIT License](https://github.com/WindomZ/lottery-engine/blob/master/LICENSE)
