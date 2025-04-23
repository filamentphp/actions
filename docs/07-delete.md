---
title: Delete action
---
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Filament includes an action that is able to delete Eloquent records. When the trigger button is clicked, a modal asks the user for confirmation. You may use it like so:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
```

Or if you want to add it as a table bulk action, so that the user can choose which rows to delete, use `Filament\Actions\DeleteBulkAction`:

```php
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
}
```

## Redirecting after deleting

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->successRedirectUrl(route('posts.list'))
```

<UtilityInjection set="actions" version="4.x">As well as `$record`, the `successRedirectUrl()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the delete notification

When the record is successfully deleted, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->successNotificationTitle('User deleted')
```

<UtilityInjection set="actions" version="4.x">As well as allowing a static value, the `successNotificationTitle()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

DeleteAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User deleted')
            ->body('The user has been deleted successfully.'),
    )
```

<UtilityInjection set="actions" version="4.x" extras="Notification;;Filament\Notifications\Notification;;$notification;;The default notification object, which could be a useful starting point for customization.">As well as allowing a static value, the `successNotification()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

To disable the notification altogether, use the `successNotification(null)` method:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

You can use the `before()` and `after()` methods to execute code before and after a record is deleted:

```php
use Filament\Actions\DeleteAction;

DeleteAction::make()
    ->before(function () {
        // ...
    })
    ->after(function () {
        // ...
    })
```

<UtilityInjection set="actions" version="4.x">These hook functions can inject various utilities as parameters.</UtilityInjection>
