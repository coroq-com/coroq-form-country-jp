# Coroq Form Country JP

Japan country-specific form inputs for [coroq/form](https://github.com/ozami/coroq-form).

## Features

- **PostalCodeInput** - Japanese postal code (郵便番号) validation and normalization
- **TelInput** - Japanese telephone number (電話番号) validation and normalization
- **PrefectureCodeSelect** - 47 Japanese prefectures selection (2-digit codes: 01-47)
- **PrefectureSelect** - 47 Japanese prefectures selection (prefecture names)
- **ErrorMessages** - Japanese error messages

## Installation

```bash
composer require coroq/form-country-jp
```

Requires PHP ^8.0, coroq/form ^3.0

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

## TelInput

Validates Japanese domestic telephone numbers (10-11 digits starting with 0).

```php
use Coroq\Form\Country\Jp\FormItem\TelInput;

$tel = new TelInput();
$tel->setValue('090-1234-5678');

if ($tel->validate()) {
    echo $tel->getValue();    // "09012345678"
    echo $tel->getTel();      // "09012345678" or null
}
```

### Input Formats

Accepts 10-11 digit numbers starting with 0. Normalizes full-width characters, hyphens, and whitespace.

```php
$tel = new TelInput();

$tel->setValue('090-1234-5678');   // "09012345678"
$tel->setValue('０９０１２３４５６７８'); // "09012345678"
$tel->setValue('  090 1234 5678  '); // "09012345678"
```

### With Hyphen Mode

Use `setWithHyphen(true)` to require hyphens in input (asymmetric validation).

```php
$tel = (new TelInput())->setWithHyphen(true);
$tel->setValue('090-1234-5678');  // Valid, keeps hyphens
$tel->setValue('09012345678');     // Invalid - requires hyphens
```

## PrefectureSelect

Select input for all 47 Japanese prefectures. Values and labels are both prefecture names.

```php
use Coroq\Form\Country\Jp\FormItem\PrefectureSelect;

$prefecture = new PrefectureSelect();
$prefecture->setValue('東京都');

if ($prefecture->validate()) {
    echo $prefecture->getValue();        // "東京都"
    echo $prefecture->getPrefecture();   // "東京都" or null
}
```

### Empty Option Label

By default, an empty option with an empty label is added at the top. You can customize the label:

```php
$prefecture = (new PrefectureSelect())->setEmptyOptionLabel('選択してください');
// First option will be: '' => '選択してください'
```

## PrefectureCodeSelect

Select input for all 47 Japanese prefectures using JIS X 0401 codes (01-47). Values are 2-digit codes, labels are prefecture names.

```php
use Coroq\Form\Country\Jp\FormItem\PrefectureCodeSelect;

$prefecture = new PrefectureCodeSelect();
$prefecture->setValue('13');  // Tokyo

if ($prefecture->validate()) {
    echo $prefecture->getValue();        // "13"
    echo $prefecture->getPrefecture();   // "東京都"
}
```

### Empty Option Label

Same as PrefectureSelect, you can customize the empty option label:

```php
$prefecture = (new PrefectureCodeSelect())->setEmptyOptionLabel('選択してください');
```

## Error Messages

```php
use Coroq\Form\ErrorMessageFormatter;
use Coroq\Form\Country\Jp\ErrorMessages;

$formatter = new ErrorMessageFormatter();
$formatter->setMessages(ErrorMessages::get());

if ($form->postalCode->hasError()) {
    echo $formatter->format($form->postalCode->getError());
    // "正しい郵便番号を入力してください"
}

if ($form->tel->hasError()) {
    echo $formatter->format($form->tel->getError());
    // "正しい電話番号を入力してください"
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
