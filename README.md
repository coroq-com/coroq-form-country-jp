# Coroq Form Country JP

Japan country-specific form inputs for [coroq/form](https://github.com/ozami/coroq-form).

## Features

- **PostalCodeInput** - Japanese postal code (郵便番号) validation and normalization
- **BasicErrorMessages** - Japanese error messages

## Installation

```bash
composer require coroq/form-country-jp
```

Requires PHP >= 8.0, coroq/form ^3.0

## PostalCodeInput

Validates Japanese postal codes with configurable output format.

```php
use Coroq\Form\Form;
use Coroq\Form\Country\Jp\FormItem\PostalCodeInput;

class AddressForm extends Form {
    public readonly PostalCodeInput $postalCode;

    public function __construct() {
        $this->postalCode = new PostalCodeInput();
    }
}

$form = new AddressForm();
$form->setValue(['postalCode' => '123-4567']);

if ($form->validate()) {
    echo $form->postalCode->getValue();        // "1234567"
    echo $form->postalCode->getPostalCode();   // "1234567" or null
}
```

### Input Formats

Accepts both "1234567" and "123-4567" formats. Normalizes full-width characters and various dash types (－, −, ー).

```php
$postal = new PostalCodeInput();

$postal->setValue('123-4567');     // "1234567"
$postal->setValue('１２３４５６７'); // "1234567"
$postal->setValue('123−4567');     // "1234567"
$postal->setValue('  123 4567  '); // "1234567"
```

### Output Format

Default is without hyphen. Use `setWithHyphen(true)` for hyphenated output:

```php
$postal = (new PostalCodeInput())->setWithHyphen(true);
$postal->setValue('1234567');
echo $postal->getValue();  // "123-4567"
```

## Error Messages

```php
use Coroq\Form\ErrorMessageFormatter;
use Coroq\Form\Country\Jp\BasicErrorMessages;

$formatter = new ErrorMessageFormatter();
$formatter->setMessages(BasicErrorMessages::get());

if ($form->postalCode->hasError()) {
    echo $formatter->format($form->postalCode->getError());
    // "正しい郵便番号を入力してください"
}
```

## Testing

```bash
composer test
composer coverage       # HTML coverage report → coverage/
composer coverage-text  # Text coverage report
```

## License

MIT
