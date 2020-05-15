# vangarde/katana

A Laravel Blade extension to enable storing of blade templates in a database.
I tried to keep the core functionality as is. However, some functionalities might currently not available.
Please open an issue in this case.


## Installation

1. Install package:

```composer require vangarde/katana```

2. Publish configuration file

You have to configure the file `config/katana.php` as you want.

## Do I have to change something?
No, you don't have to change anything. It's intended to load primarly from the file system (as known inside your views directory).
If no blade-template is found, it will try to load from the database.

