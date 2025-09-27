# PHP-Toolbox
PHP toolbox library, containing useful everyday utilities (e.g. hashing-, encoding-, date-time-functions)

## Tools
- Base64Tool
- ConversionTool
- DateTimeTool
- HashTool
- ImageTool
- MaskingTool
- MimeTool
- PathTool
- ReflectionTool
- SecurityTool


## Installation
Install the latest version, using composer:
```bash
composer require xcy7e/php-toolbox
```

## Usage
Using the tools is easy:
```php
use Xcy7e\PhpToolbox\Library\ConversionTool;

// ...

function howManyBytesIs8Megabyte()
{
    // static functions dont require instantiation:
    echo "8M equals so many bytes: " . ConversionTool::parseByteShorthand('8M');
}

function createPassword():string
{
    // or: without `use`-directive:
    return \Xcy7e\PhpToolbox\Library\SecurityTool::generateRandomPassword(12);  // 12 char long password
}
```

> #### No instantiation required!
> Alle functions are `static` and can be called directly on the class name.
> You **do not** need to instantiate the class like `$conversionTool = new ConversionTool();`.