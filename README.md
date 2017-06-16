# lottery-engine

> A lottery component engine - fast, modularity and easy to deploy.

[![Latest Stable Version](https://img.shields.io/packagist/v/windomz/lottery-engine.svg?style=flat-square)](https://packagist.org/packages/windomz/lottery-engine)
[![Build Status](https://img.shields.io/travis/WindomZ/lottery-engine/master.svg?style=flat-square)](https://travis-ci.org/WindomZ/lottery-engine)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum MYSQL Version](https://img.shields.io/badge/mysql-%3E%3D%205.6-4479a1.svg?style=flat-square)](https://www.mysql.com/)

## Feature

- [x] Play
- [x] Reward
- [x] Record

## Installation

Open the terminal in the project directory:
```bash
$ composer require windomz/lottery-engine
```

Create a configuration file, like `config.yml`:
```yaml
database_host: 127.0.0.1
database_port: 3306
database_type: mysql
database_name: lotterydb
database_username: root
database_password: root
```

If only for quick testing, 
you can run `./sql/lotterydb.sql` in `MySQL` to quickly create a test database.

Of course, you can also customize the `database name` based on `./sql/lotterydb.sql`, 
but note that the `table name` _CANNOT MODIFY_!

In the project initialization, 
load the specified configuration file through the following implementation:
```php
Lottery::setConfigPath('./config.yml');
```

## Usage

Refer to the [Document](https://windomz.github.io/lottery-engine)(_Currently only Chinese_) for details.

## License

The [MIT License](https://github.com/WindomZ/lottery-engine/blob/master/LICENSE)
