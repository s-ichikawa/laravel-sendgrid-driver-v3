Laravel SendGrid Driver (SendGrid API Version3)
====

if you need version2, [here](https://github.com/s-ichikawa/laravel-sendgrid-driver)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8caeedd3-975e-4313-9b3a-25f677568a56/big.png)](https://insight.sensiolabs.com/projects/8caeedd3-975e-4313-9b3a-25f677568a56)
[![Build Status](https://scrutinizer-ci.com/g/s-ichikawa/laravel-sendgrid-driver-v3/badges/build.png?b=master)](https://scrutinizer-ci.com/g/s-ichikawa/laravel-sendgrid-driver-v3/build-status/master)

A Mail Driver with support for Sendgrid Web API, using the original Laravel API.
This library extends the original Laravel classes, so it uses exactly the same methods.

To use this package required your [Sendgrid Api Key](https://sendgrid.com/docs/User_Guide/Settings/api_keys.html).
Please make it [Here](https://app.sendgrid.com/settings/api_keys).

#Install (Laravel5.1~)

Add the package to your composer.json and run composer update.
```json
"require": {
    "s-ichikawa/laravel-sendgrid-driver": "^v5.1"
},
```

or installed with composer
```
$ composer require s-ichikawa/laravel-sendgrid-driver
```

Remove the default service provider and add the sendgrid service provider in config/app.php:
```php
'providers' => [
//  Illuminate\Mail\MailServiceProvider::class,

    Sichikawa\LaravelSendgridDriver\MailServiceProvider::class,
];
```

Remove the default service provider and add the sendgrid service provider in config/app.php:
```php
'providers' => [
//  'Illuminate\Mail\MailServiceProvider',

    'Sichikawa\LaravelSendgridDriver\MailServiceProvider',
];
```

# Install (Lumen)

Add the package to your composer.json and run composer update.
```json
"require": {
    "s-ichikawa/laravel-sendgrid-driver": "dev-master"
},
```

or installed with composer
```
$ composer require s-ichikawa/laravel-sendgrid-driver:dev-master
```

Add the sendgrid service provider in bootstrap/app.php
```php
$app->configure('mail');
$app->configure('services');
$app->register(Sichikawa\LaravelSendgridDriver\MailServiceProvider::class);

unset($app->availableBindings['mailer']);
```

#Configure

.env
```
MAIL_DRIVER=sendgrid
SENDGRID_API_KEY='YOUR_SENDGRID_API_KEY'
```

config/service.php
```
    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY')
    ]
```

#Use SMTP API

##This function is TODO now.

Sendgrid's [SMTP API](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html) is so cool feature.
This function can use by setting embed data to message.
and, set 'sendgrid/x-smtpapi' to data name or content-type.

```
\Mail::send('view', $data, function (Message $message) {
    $message
        ->to('foo@example.com', 'foo_name')
        ->from('bar@example.com', 'bar_name')
        ->embedData([
            'categories' => ['user_group1']
        ], 'sendgrid/x-smtpapi');
});
```
