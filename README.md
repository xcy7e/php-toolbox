<h1><img src="./res/icon.png" width="38" style="margin:0 0 -5px;" /> PHP-Toolbox</h1>

PHP toolbox library, containing useful everyday utilities (e.g., hashing-, encoding-, date-time-functions). Utilizing [symfony](https://symfony.com/) components.

## Tools
- ArrayTool
- Base64Tool
- ConversionTool
- CryptoTool
- DateTimeTool
- ImageTool
- MaskingTool
- MimeTool
- PathTool
- ReflectionTool
- SecurityTool


## Installation
Using [composer](https://getcomposer.org/): (recommended)
```bash
composer require xcy7e/php-toolbox
```

Using git:
```bash
git clone https://github.com/xcy7e/php-toolbox.git
```

## Usage
```php
// with use-directive
use Xcy7e\PhpToolbox\Library\ConversionTool;

// all functions can be used statically
echo "8M equals so many bytes: " . ConversionTool::parseShorthandToBytes('8M');
```
```php
// without use-directive
$encrypted = \Xcy7e\PhpToolbox\Library\CryptoTool::encrypt('sensitiveData', 'myPassword');
```


---

```
© 2025 by Jonathan Riedmair
Licensed under GNU General Public License v3
You are free to use this code for personal and commercial purposes, change it to your needs and share it as you like.
Reference to the author is not required.
```

Icon by [muh zakaria](https://jackvisualassets.com/).