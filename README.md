# Laravel Eloquent Model Auditing

## Introduction

This package help audit model data changes, during create, update, and deleting (audit trail)

## Installation

1. Installing the package
```console
$ composer require frengky/laravel-auditable
```
2. Add the service provider to your `config/app.php`
```
Frengky\Auditable\ServiceProvider::class
```

## Usage

Use the `Auditable` trait to your model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use Frengky\Auditable\Auditable;
}

?>
```

Now you can call the trait method, everytime you make changes with your model:

```php
// After creating new record
$yourModel = YourModel::create(['title' => 'Foo']);
$yourModel->auditCreating();

// Before saving updated record
$yourModel->auditUpdating();
$yourModel->save();

// After deleting a record
$yourModel->destroy($id);
$yourModel->auditDeleting();

```
