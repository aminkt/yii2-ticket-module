How to install this module:
----------
Add flowing line to require part of `composer.json` :
```
"aminkt/yii2-ticket-module": "*",
```

And after that run bellow command in your composer :
```
Composer update aminkt/yii2-payment-module
```

Step2: Add flowing lines in your application backend config:

```php
'ticket' => [
    'class' => 'aminkt\ticket\Ticket',
    'controllerNamespace' => 'aminkt\ticket\controllers\admin',
],
```

Step4: Add flowing lines in your application frontend config:

```php
'ticket' => [
    'class' => 'aminkt\ticket\Ticket',
    'controllerNamespace' => 'aminkt\ticket\controllers\front',
],
```

---
**Database Migrations**

Before usage this extension, we'll also need to prepare the database.

```
php yii migrate --migrationPath=@vendor/aminkt/yii2-ticket-module/migrations
```


Done :)