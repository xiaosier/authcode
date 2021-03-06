# wilon/authcode

[![Packagist][badge_package]][link-packagist]
[![Packagist Release][badge_release]][link-packagist]
[![Packagist Downloads][badge_downloads]][link-packagist]

[badge_package]:      https://img.shields.io/badge/package-wilon/authcode-blue.svg?style=flat-square
[badge_release]:      https://img.shields.io/packagist/v/wilon/authcode.svg?style=flat-square
[badge_downloads]:    https://img.shields.io/packagist/dt/wilon/authcode.svg?style=flat-square
[link-packagist]:     https://packagist.org/packages/wilon/authcode

PHP string Encryption and Decryption. From discuz source code.

## Installation & loading

Just add this line to your `composer.json` file:

```json
"wilon/authcode": "^1.1.3"
```

or

```sh
composer require wilon/authcode
```

## A Simple Example

##### Encode & Decode

```php
<?php

use Encryption\Authcode;

$key = 'IoUwe#(#FFFsfoaHFfa';
echo $auth = Authcode::encode('String', $key), '<br>';
echo $result = Authcode::decode($auth, $key), '<br>';
```

```php
<?php

$key = 'IoUwe#(#FFFsfoaHFfa';
echo $auth = Encryption\Authcode::encode('String', $key), '<br>';
echo $result = Encryption\Authcode::decode($auth, $key), '<br>';
```

##### Only correct key can decode

```php
<?php
$key = 'IoUwe#(#FFFsfoaHFfa';
echo $auth = Encryption\Authcode::encode('String', $key), '<br>';
echo $result2 = Encryption\Authcode::decode($auth, 'otherKey'), '<br>';    # Can't get 'String'
```

##### Use expiry

```php
$key = 'IoUwe#(#FFFsfoaHFfa';
echo $auth = Encryption\Authcode::encode('String', $key, 10), '<br>';
sleep(11);
echo $result = Encryption\Authcode::decode($auth, $key), '<br>';    # Can't get 'String'
```

##### Encode remain equal signs
 *python without '=' base64.b64decode() can't decode*

```php
<?php
echo $auth = Encryption\Authcode::encodeRemainEqualsigns('String', $key), '<br>';    # has '='
echo $result = Encryption\Authcode::decode($auth, $key), '<br>';
```
