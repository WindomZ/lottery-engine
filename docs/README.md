# lottery-engine

## 当前版本

[![Latest Stable Version](https://img.shields.io/packagist/v/windomz/lottery-engine.svg?style=flat-square)](https://packagist.org/packages/windomz/lottery-engine)
[![Build Status](https://img.shields.io/travis/WindomZ/lottery-engine/master.svg?style=flat-square)](https://travis-ci.org/WindomZ/lottery-engine)

## 运行环境

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum MYSQL Version](https://img.shields.io/badge/mysql-%3E%3D%205.6-4479a1.svg?style=flat-square)](https://www.mysql.com/)

## 安装与更新

```bash
$ composer require windomz/lottery-engine
```

## 使用用法

### 配置文件

创建并编写`config.yml`，里面参数根据您的环境情况修改：
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

如果只是作为测试，可以在`MySQL`运行`./sql/lotterydb.sql`来快速创建测试数据库。

在项目初始化阶段，**加载**指定配置文件：
```php
Lottery::setConfigPath('./config.yml');
```

### 模块定义

- 奖品(`Reward`): 管理各种抽奖奖品
- 玩法(`Play`): 管理各种抽奖方法
- 记录(`Record`): 记录各种抽奖情况

### 业务流程

奖品(`Reward`) -> 玩法(`Play`) -> 记录(`Record`)

### 属性字段

#### 奖品(`Reward`)

|类型|字段|必填|修改|描述|
|---|---|:---:|:---:|---|
|string|id|N|N|UUID|
|string|post_time|N|N|创建时间|
|string|put_time|N|N|修改时间|
|string|name|Y|Y|名称|
|bool|active|N|Y|是否生效|
|string|level|N|Y|级别|
|string|desc|N|Y|描述|
|string|award_id|N|Y|指定奖品UUID|
|string|award_class|N|Y|指定奖品类别(第一级分类，单选，可选)|
|string|award_kind|N|Y|指定奖品类型(第二级分类，多选，可选)|
|int|size|Y|Y|奖品派发总数|
|int|count|N|N|奖品派发数量|

#### 玩法(`Play`)

|类型|字段|必填|修改|描述|
|---|---|:---:|:---:|---|
|string|id|N|N|UUID|
|string|post_time|N|N|创建时间|
|string|put_time|N|N|修改时间|
|string|name|Y|Y|名称|
|bool|active|N|Y|是否生效|
|string|level|N|Y|级别|
|string|desc|N|Y|描述|
|bool|daily|Y|Y|每日活动|
|int|limit|Y|Y|用户次数限制(每日活动或总次数)|
|int|size|Y|Y|参与活动总数|
|int|count|N|N|参与活动次数|
|json|weights|Y|Y|奖品权重(若不支持json则开启'rule')|
|bool|rule|N|N|是否开启玩法规则(`Rule`)|

#### 玩法规则(`Rule`)

|类型|字段|必填|修改|描述|
|---|---|:---:|:---:|---|
|string|id|N|N|UUID|
|string|post_time|N|N|创建时间|
|string|put_time|N|N|修改时间|
|string|name|Y|Y|名称|
|bool|active|N|Y|是否生效|
|string|play_id|Y|N|玩法UUID|
|string|reward_id|Y|N|奖品UUID|
|int|weight|Y|Y|奖品权重|

#### 记录(`Record`)

|类型|字段|必填|修改|描述|
|---|---|:---:|:---:|---|
|string|id|N|N|UUID|
|string|post_time|N|N|创建时间|
|string|put_time|N|N|修改时间|
|string|user_id|Y|N|用户UUID|
|string|play_id|Y|N|玩法UUID|
|string|reward_id|Y|N|奖品UUID|
|string|related_id|N|Y|关联外部UUID(可选)|
|bool|winning|N|N|是否中奖|
|bool|passing|N|N|是否生效|

### 接口方法

#### 奖品(`Reward`)

- Reward::object($id = null)
  - @description 查询或构建奖品(`Reward`)
  - @param
    - string $id 奖品UUID，或者留空来构建新的奖品(`Reward`)
  - @return object

- Reward::list($where, $limit, $page, $order)
  - @description 获取一组奖品(`Reward`)
  - @param
    - array $where 筛选范围，选用`Reward::COL_`开头的字段
    - int $limit 筛选数量
    - int $page 筛选页数
    - array $order 筛选排序
  - @return array

- Reward->post()
  - @description 创建奖品(`Reward`)
  - @return bool

- Reward->put($columns)
  - @description 修改奖品(`Reward`)
  - @param
    - array $columns 标明修改的字段，选用`Reward::COL_`开头的字段组成数组
  - @return bool

- Reward->setAward($award_id, $award_class, $award_kind)
  - @description 配置奖品(`Reward`)的外部指向
  - @param
    - string $award_id 指定奖品外部UUID
    - int $award_class 指定奖品的类别
    - int $award_kind 指定奖品的类型
  - @note 最后记得调用`post`或`put`来提交修改

- Reward::ID_NULL
  - @description 默认的奖品UUID - 未获奖

- Reward::ID_AGAIN
  - @description 默认的奖品UUID - 再来一次

#### 玩法(`Play`)

- Play::object($id = null)
  - @description 查询或构建玩法(`Play`)
  - @param
    - string $id 玩法UUID，或者留空来构建新的玩法(`Play`)
  - @return object

- Play::list($where, $limit, $page, $order)
  - @description 获取一组玩法(`Play`)
  - @param
    - array $where 筛选范围，选用`Play::COL_`开头的字段
    - int $limit 筛选数量
    - int $page 筛选页数
    - array $order 筛选排序
  - @return array

- Play->post()
  - @description 创建玩法(`Play`)
  - @return bool

- Play->put($columns)
  - @description 修改玩法(`Play`)
  - @param
    - array $columns 标明修改的字段，选用`Play::COL_`开头的字段组成数组
  - @return bool

- Play->setReward($reward_id, $weight)
  - @description 配置抽奖玩法(`Play`)的奖品(`Reward`)概率
  - @param
    - string $reward_id 奖品UUID
    - int $weight 权重，数值越大概率越大
  - @note 最后记得调用`post`或`put`来提交修改

- Play->hasCount($user_id)
  - @description 剩余抽奖玩法(`Play`)次数
  - @param
    - string $user_id 用户UUID
  - @return int

- Play->play($user_id, $callback = null)
  - @description 进行抽奖玩法(`Play`)
  - @param
    - string $user_id 用户UUID
    - callable $callback($err, Record $record) 回调系统确认的记录(`Record`)
  - @return string 记录UUID，系统未确认，便于后续的追踪

#### 记录(`Record`)

- Record::object($id)
  - @description 查询记录(`Record`)
  - @param
    - string $id 记录UUID
  - @return object

- Record::list($where, $limit, $page, $order)
  - @description 获取一组记录(`Record`)
  - @param
    - array $where 筛选范围，选用`Record::COL_`开头的字段
    - int $limit 筛选数量
    - int $page 筛选页数
    - array $order 筛选排序
  - @return array

- Record->isWinning()
  - @description 是否中奖
  - @return bool

- Record->putRelated($related_id)
  - @description 设置关联外部UUID
  - @param
    - string $related_id 关联外部UUID
  - @return bool

- Record::ID_FINISH
  - @description 默认的记录UUID - 当天活动结束(多种原因)

- Record::ID_NULL
  - @description 默认的记录UUID - 空记录(预留)

- Record::ID_AGAIN
  - @description 默认的记录UUID - 空记录(再来一次)

#### 公共方法

- *->toJSON()
  - @description 转为JSON格式对象
  - @demo `$obj->toJSON()`
  - @return object

- *::obj2JSON($obj)
  - @description 转为JSON格式对象
  - @demo `Play::toJSON(Play::get('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'))`
  - @param
    - object $obj 对象
  - @return object

- *::where($type, $key)
  - @description 使用`*::list($where, $limit, $page, $order)`时，构造`$where`的高级用法。
  - @demo `[Play::where(Play::WHERE_GTE, Play::COL_LIMIT) => 10]`，等同于`[Play::COL_LIMIT>=10]`。
  - @param
    - int $type 对象
    - string $key 对象
  - @return object
