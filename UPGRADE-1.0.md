This document details the changes that you need to make to your code when upgrading from one version to another.

Upgrading from 0.x to 1.x
=========================

- The `PdfField` properties listed below were transformed into read-only public properties, so their getters and setters
  were removed.
    - name
    - flag
    - defaultValue
    - options
    - description
    - justification

```php
// Before
$name = $field->getName();
$flag = $field->getFlag();

// After
$name = $field->name;
$flag = $field->flag;
```

- The `PdfField::addOption` was removed.
- The `PdfField::checkBitValue` is now final.
- The static property `PdfField::$flags` was removed.
