<?php
use Coroq\Form\Country\Jp\FormItem\PostalCodeInput;
use Coroq\Form\Country\Jp\Error\InvalidPostalCodeError;
use Coroq\Form\Error\EmptyError;
use PHPUnit\Framework\TestCase;

class PostalCodeInputTest extends TestCase {
  public static function setUpBeforeClass(): void {
    mb_substitute_character(0xFFFD);
  }

  // Constructor and setup
  public function testConstructorSetsDefaultWithHyphenToFalse() {
    $input = new PostalCodeInput();
    $this->assertFalse($input->getWithHyphen());
  }

  public function testConvertsFullWidthNumbersToHalfWidth() {
    $input = new PostalCodeInput();
    $input->setValue('１２３４５６７');
    $this->assertSame('1234567', $input->getValue());
  }

  // setWithHyphen configuration
  public function testSetWithHyphenTrue() {
    $input = new PostalCodeInput();
    $result = $input->setWithHyphen(true);
    $this->assertSame($input, $result); // Method chaining
    $this->assertTrue($input->getWithHyphen());
  }

  public function testSetWithHyphenFalse() {
    $input = new PostalCodeInput();
    $result = $input->setWithHyphen(false);
    $this->assertFalse($input->getWithHyphen());
  }

  // Filter tests - without hyphen (default)
  public function testFilterPreservesSevenDigitsWithoutHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $this->assertSame('1234567', $input->getValue());
  }

  public function testFilterRemovesHyphenWhenWithHyphenIsFalse() {
    $input = new PostalCodeInput();
    $input->setValue('123-4567');
    $this->assertSame('1234567', $input->getValue());
  }

  public function testFilterConvertsFullWidthNumbersWithoutHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('１２３４５６７');
    $this->assertSame('1234567', $input->getValue());
  }

  public function testFilterConvertsFullWidthWithHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('１２３−４５６７');
    $this->assertSame('1234567', $input->getValue());
  }

  public function testFilterConvertsProlongedSoundMark() {
    $input = new PostalCodeInput();
    $input->setValue('123ー4567');
    $this->assertSame('1234567', $input->getValue());
  }

  // Filter tests - with hyphen
  public function testFilterAddsHyphenWhenWithHyphenIsTrue() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('1234567');
    $this->assertSame('123-4567', $input->getValue());
  }

  public function testFilterPreservesHyphenWhenWithHyphenIsTrue() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('123-4567');
    $this->assertSame('123-4567', $input->getValue());
  }

  public function testFilterConvertsFullWidthToHyphenFormat() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('１２３４５６７');
    $this->assertSame('123-4567', $input->getValue());
  }

  public function testFilterConvertsProlongedSoundMarkWithHyphenFormat() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('123ー4567');
    $this->assertSame('123-4567', $input->getValue());
  }

  // Filter tests - preserving invalid input
  public function testFilterPreservesTooShortInput() {
    $input = new PostalCodeInput();
    $input->setValue('12345');
    $this->assertSame('12345', $input->getValue());
  }

  public function testFilterPreservesTooLongInput() {
    $input = new PostalCodeInput();
    $input->setValue('12345678');
    $this->assertSame('12345678', $input->getValue());
  }

  public function testFilterPreservesInvalidFormat() {
    $input = new PostalCodeInput();
    $input->setValue('123-456'); // Wrong hyphen position
    $this->assertSame('123-456', $input->getValue());
  }

  public function testFilterPreservesNonNumericInput() {
    $input = new PostalCodeInput();
    $input->setValue('abc-defg');
    $this->assertSame('abc-defg', $input->getValue());
  }

  public function testFilterPreservesMixedInput() {
    $input = new PostalCodeInput();
    $input->setValue('123a567');
    $this->assertSame('123a567', $input->getValue());
  }

  // Validation - acceptance (without hyphen)
  public function testValidateAcceptsSevenDigitsWithoutHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $this->assertTrue($input->validate());
    $this->assertNull($input->getError());
  }

  public function testValidateAcceptsHyphenatedInputConvertedToNoHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('123-4567'); // Gets converted to 1234567
    $this->assertTrue($input->validate());
    $this->assertSame('1234567', $input->getValue());
  }

  // Validation - acceptance (with hyphen)
  public function testValidateAcceptsHyphenatedFormat() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('123-4567');
    $this->assertTrue($input->validate());
    $this->assertNull($input->getError());
  }

  public function testValidateAcceptsNoHyphenInputConvertedToHyphenated() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('1234567'); // Gets converted to 123-4567
    $this->assertTrue($input->validate());
    $this->assertSame('123-4567', $input->getValue());
  }

  // Validation - rejection
  public function testValidateRejectsTooShortInput() {
    $input = new PostalCodeInput();
    $input->setValue('12345');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  public function testValidateRejectsTooLongInput() {
    $input = new PostalCodeInput();
    $input->setValue('12345678');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  public function testValidateRejectsNonNumericInput() {
    $input = new PostalCodeInput();
    $input->setValue('abc-defg');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  public function testValidateRejectsWrongHyphenPosition() {
    $input = new PostalCodeInput();
    $input->setValue('123-456');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  public function testValidateRejectsHyphenWhenConfiguredWithoutHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('12-34567');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  public function testValidateRejectsMixedCharacters() {
    $input = new PostalCodeInput();
    $input->setValue('123a567');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());
  }

  // Empty handling
  public function testValidateEmptyRequiredField() {
    $input = (new PostalCodeInput())->setRequired(true);
    $input->setValue('');
    $this->assertFalse($input->validate());
    $this->assertInstanceOf(EmptyError::class, $input->getError());
  }

  public function testValidateEmptyOptionalField() {
    $input = (new PostalCodeInput())->setRequired(false);
    $input->setValue('');
    $this->assertTrue($input->validate());
    $this->assertNull($input->getError());
  }

  // getPostalCode tests
  public function testGetPostalCodeReturnsValueWhenValid() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $this->assertSame('1234567', $input->getPostalCode());
  }

  public function testGetPostalCodeReturnsHyphenatedWhenConfigured() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('1234567');
    $this->assertSame('123-4567', $input->getPostalCode());
  }

  public function testGetPostalCodeReturnsNullWhenEmpty() {
    $input = new PostalCodeInput();
    $input->setValue('');
    $this->assertNull($input->getPostalCode());
  }

  public function testGetPostalCodeReturnsNullWhenInvalid() {
    $input = new PostalCodeInput();
    $input->setValue('Invalid');
    $this->assertNull($input->getPostalCode());
  }

  public function testGetPostalCodeReturnsNullForTooShort() {
    $input = new PostalCodeInput();
    $input->setValue('12345');
    $this->assertNull($input->getPostalCode());
  }

  // getParsedValue tests
  public function testGetParsedValueReturnsSameAsGetPostalCode() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $this->assertSame($input->getPostalCode(), $input->getParsedValue());
  }

  public function testGetParsedValueReturnsNullWhenInvalid() {
    $input = new PostalCodeInput();
    $input->setValue('Invalid');
    $this->assertNull($input->getParsedValue());
  }

  // Whitespace handling
  public function testRemovesWhitespace() {
    $input = new PostalCodeInput();
    $input->setValue('  123 45 67  ');
    $this->assertSame('1234567', $input->getValue());
  }

  public function testRemovesFullWidthSpaces() {
    $input = new PostalCodeInput();
    $input->setValue('123　4567');
    $this->assertSame('1234567', $input->getValue());
  }

  // Format switching
  public function testCanSwitchFromNoHyphenToHyphen() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $this->assertSame('1234567', $input->getValue());

    // Switch format and re-set value
    $input->setWithHyphen(true);
    $input->setValue('1234567');
    $this->assertSame('123-4567', $input->getValue());
  }

  public function testCanSwitchFromHyphenToNoHyphen() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $input->setValue('123-4567');
    $this->assertSame('123-4567', $input->getValue());

    // Switch format and re-set value
    $input->setWithHyphen(false);
    $input->setValue('123-4567');
    $this->assertSame('1234567', $input->getValue());
  }

  // State management
  public function testClearResetsValue() {
    $input = new PostalCodeInput();
    $input->setValue('1234567');
    $input->clear();
    $this->assertTrue($input->isEmpty());
  }

  public function testClearResetsError() {
    $input = new PostalCodeInput();
    $input->setValue('Invalid');
    $input->validate();
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());

    $input->clear();
    $this->assertNull($input->getError());
  }

  public function testSetValueClearsError() {
    $input = new PostalCodeInput();
    $input->setValue('Invalid');
    $input->validate();
    $this->assertInstanceOf(InvalidPostalCodeError::class, $input->getError());

    $input->setValue('1234567');
    $this->assertNull($input->getError());
  }

  // Real-world postal codes
  public function testAcceptsRealPostalCodes() {
    $input = new PostalCodeInput();
    $realCodes = [
      '100-0001', // Tokyo
      '060-0001', // Sapporo
      '810-0001', // Fukuoka
      '900-0001', // Naha
    ];

    foreach ($realCodes as $code) {
      $input->setValue($code);
      $this->assertTrue($input->validate(), "Failed for: $code");
      $this->assertSame(str_replace('-', '', $code), $input->getValue());
    }
  }

  public function testAcceptsRealPostalCodesWithHyphenFormat() {
    $input = (new PostalCodeInput())->setWithHyphen(true);
    $realCodes = ['1000001', '0600001', '8100001', '9000001'];

    foreach ($realCodes as $code) {
      $input->setValue($code);
      $this->assertTrue($input->validate(), "Failed for: $code");
      $formatted = substr($code, 0, 3) . '-' . substr($code, 3);
      $this->assertSame($formatted, $input->getValue());
    }
  }
}
