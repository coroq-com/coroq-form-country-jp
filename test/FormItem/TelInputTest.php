<?php
use Coroq\Form\Country\Jp\FormItem\TelInput;
use Coroq\Form\Country\Jp\Error\InvalidTelError;
use Coroq\Form\Error\EmptyError;
use PHPUnit\Framework\TestCase;

class TelInputTest extends TestCase {
  // Without hyphen mode tests (default)
  public function testValid11DigitNumber() {
    $tel = new TelInput();
    $tel->setValue('09012345678');

    $this->assertTrue($tel->validate());
    $this->assertNull($tel->getError());
    $this->assertSame('09012345678', $tel->getValue());
  }

  public function testValid10DigitNumber() {
    $tel = new TelInput();
    $tel->setValue('0312345678');

    $this->assertTrue($tel->validate());
    $this->assertNull($tel->getError());
  }

  public function testInvalidNumberTooShort() {
    $tel = new TelInput();
    $tel->setValue('090123456'); // 9 digits

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testInvalidNumberTooLong() {
    $tel = new TelInput();
    $tel->setValue('090123456789'); // 12 digits

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testInvalidNumberNotStartingWith0() {
    $tel = new TelInput();
    $tel->setValue('19012345678');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testEmptyRequiredField() {
    $tel = (new TelInput())->setRequired(true);
    $tel->setValue('');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(EmptyError::class, $tel->getError());
  }

  public function testEmptyOptionalField() {
    $tel = (new TelInput())->setRequired(false);
    $tel->setValue('');

    $this->assertTrue($tel->validate());
    $this->assertNull($tel->getError());
  }

  // Filter/normalization tests (without hyphen mode)
  public function testFilterRemovesHyphens() {
    $tel = new TelInput();
    $tel->setValue('090-1234-5678');

    $this->assertSame('09012345678', $tel->getValue());
  }

  public function testFilterConvertsFullWidthNumbers() {
    $tel = new TelInput();
    $tel->setValue('０９０１２３４５６７８');

    $this->assertSame('09012345678', $tel->getValue());
  }

  public function testFilterRemovesWhitespace() {
    $tel = new TelInput();
    $tel->setValue('  090 1234 5678  ');

    $this->assertSame('09012345678', $tel->getValue());
  }

  public function testFilterCombinedNormalization() {
    $tel = new TelInput();
    $tel->setValue('０９０－１２３４－５６７８');

    $this->assertSame('09012345678', $tel->getValue());
  }

  public function testFilterPreservesInvalidInput() {
    $tel = new TelInput();
    $tel->setValue('123abc');

    $this->assertSame('123abc', $tel->getValue());
    $this->assertFalse($tel->validate());
  }

  // With hyphen mode tests
  public function testWithHyphenModeValid11Digit() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('090-1234-5678');

    $this->assertTrue($tel->validate());
    $this->assertSame('090-1234-5678', $tel->getValue());
  }

  public function testWithHyphenModeValid10Digit() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('03-1234-5678');

    $this->assertTrue($tel->validate());
    $this->assertSame('03-1234-5678', $tel->getValue());
  }

  public function testWithHyphenModeRelaxedPositions() {
    $tel = (new TelInput())->setWithHyphen(true);

    // Various hyphen positions - all valid as long as 2 hyphens, 10-11 digits
    $tel->setValue('0123-45-6789');
    $this->assertTrue($tel->validate());

    $tel->setValue('012-345-6789');
    $this->assertTrue($tel->validate());

    $tel->setValue('01-2345-6789');
    $this->assertTrue($tel->validate());
  }

  public function testWithHyphenModeInvalidNoHyphen() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('09012345678');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testWithHyphenModeInvalidOneHyphen() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('090-12345678');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testWithHyphenModeInvalidThreeHyphens() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('09-01-234-5678');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testWithHyphenModeInvalidNotStartingWith0() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('190-1234-5678');

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testWithHyphenModeInvalidWrongDigitCount() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('090-123-456'); // 9 digits

    $this->assertFalse($tel->validate());
    $this->assertInstanceOf(InvalidTelError::class, $tel->getError());
  }

  public function testWithHyphenModeNormalizesFullWidth() {
    $tel = (new TelInput())->setWithHyphen(true);
    $tel->setValue('０９０－１２３４－５６７８');

    $this->assertSame('090-1234-5678', $tel->getValue());
    $this->assertTrue($tel->validate());
  }

  public function testSwitchBetweenModes() {
    $tel = new TelInput();
    $tel->setValue('090-1234-5678');
    $this->assertSame('09012345678', $tel->getValue()); // Without hyphen: removes hyphens

    // Switching to withHyphen mode doesn't restore hyphens - value is already normalized
    $tel->setWithHyphen(true);
    $tel->setValue('090-1234-5678'); // Need to set value again with hyphens
    $this->assertSame('090-1234-5678', $tel->getValue()); // With hyphen: keeps hyphens

    $tel->setWithHyphen(false);
    $this->assertSame('09012345678', $tel->getValue()); // Back to without hyphen: removes hyphens
  }

  // getTel() tests
  public function testGetTelReturnsValidNumber() {
    $tel = new TelInput();
    $tel->setValue('09012345678');

    $this->assertSame('09012345678', $tel->getTel());
  }

  public function testGetTelReturnsNullWhenEmpty() {
    $tel = new TelInput();
    $tel->setValue('');

    $this->assertNull($tel->getTel());
  }

  public function testGetTelReturnsNullWhenInvalid() {
    $tel = new TelInput();
    $tel->setValue('123abc');

    $this->assertNull($tel->getTel());
  }

  public function testGetParsedValueReturnsSameAsTel() {
    $tel = new TelInput();
    $tel->setValue('09012345678');

    $this->assertSame($tel->getTel(), $tel->getParsedValue());
  }

  // Various valid numbers
  public function testVariousValidNumbers() {
    $tel = new TelInput();

    $validNumbers = [
      '09012345678', // Mobile
      '08012345678', // Mobile
      '07012345678', // Mobile
      '0312345678',  // Fixed (Tokyo)
      '0612345678',  // Fixed (Osaka)
      '0120123456',  // Toll-free
      '08001234567', // Toll-free
      '0570123456',  // Navi dial
      '0123456789',  // Any 10-digit starting with 0
      '01234567890', // Any 11-digit starting with 0
    ];

    foreach ($validNumbers as $number) {
      $tel->setValue($number);
      $this->assertTrue($tel->validate(), "Number $number should be valid");
    }
  }
}
