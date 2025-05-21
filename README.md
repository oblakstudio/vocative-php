<div align="center">

<h1 align="center" style="border-bottom: none; margin-bottom: 0px">Vocative 🗣️</h1>
<h3 align="center" style="margin-top: 0px">Vocative inflector for Serbian names</h3>

[![Packagist Version](https://img.shields.io/packagist/v/oblak/vocative?label=Release&style=flat-square&logo=packagist&logoColor=white)](https://packagist.org/packages/oblak/vocative)
![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/oblak/vocative/php?label=PHP&logo=php&logoColor=white&logoSize=auto&style=flat-square)

</div>

This library allows you to effortlessly convert Serbian personal names to their correct vocative form.

## Key Features

1. **Peer-reviewed** - The library has been tested with almost all Serbian names, and reviewed by a team of linguists.
2. Easy to use - The library is designed to be simple and intuitive, making it easy for developers of all skill levels to integrate into their projects.
3. Lightweight - The library is small and efficient, ensuring that it won't slow down your application.

## Installation

You can install the library via Composer:

```bash
composer require oblak/vocative
```

## Usage

Below is a simple example of how to use the library:


### Basic Usage

```php
<?php

use Oblak\Vocative\Vocative;

$firstName = 'Avram';
$vocative = new Vocative();

echo $vocative->make($firstName); // Outputs: Avrame

```

### With custom dictionary

`Vocative` class depends on an **exception dictionary** to handle special cases. By default it uses the built-in `BaseDictionary` class, but you can use your own by implementing the `Dictionary` interface.

> [!TIP]
> We provide a `NullDictionary` class that does not use any exceptions. This is useful for testing purposes.

```php
<?php

use Oblak\Vocative\Vocative;
use Oblak\Vocative\NullDictionary;

$firstName = 'Aleksandar';
$vocative = new Vocative();

echo $vocative->make($firstName);                                       // Outputs: Aleksandre
echo $vocative->withDictionary(new NullDictionary())->make($firstName); // Outputs: Aleksandare

```

## Special thanks

As someone without deep expertise in Serbian linguistics, I am deeply grateful to the following individuals—without their guidance and support, this project could never have been completed:

* **[Svetlana Slijepčević Bjelivuk, PhD](https://jezikofil.rs)** - For providing invaluable insights into intricacies of declination and conjugation of Serbian nouns and names and reviewing the data set.
* **[Nemanja Avramović](https://github.com/avramovic)** - For his initial implementation of the `Vokativ` Library
* **Milana Bašić** - For taking the time to gather and provide a list of Serbian names, both male and female.
