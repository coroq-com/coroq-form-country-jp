<?php
use Coroq\Form\Country\Jp\FormItem\PrefectureCodeSelect;
use Coroq\Form\Error\EmptyError;
use Coroq\Form\Error\NotInOptionsError;
use PHPUnit\Framework\TestCase;

class PrefectureCodeSelectTest extends TestCase {
  public function testConstructorSetsAllPrefectures() {
    $select = new PrefectureCodeSelect();
    $options = $select->getOptions();

    $this->assertCount(48, $options);
    $this->assertSame('', $options['']);
    $this->assertSame('北海道', $options['01']);
    $this->assertSame('東京都', $options['13']);
    $this->assertSame('沖縄県', $options['47']);
  }

  public function testGetPrefecturesReturnsAll47Prefectures() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertCount(47, $prefectures);
    $this->assertIsArray($prefectures);
  }

  public function testGetPrefecturesHasCorrectKeys() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    // Check first, middle, and last
    $this->assertArrayHasKey('01', $prefectures);
    $this->assertArrayHasKey('13', $prefectures);
    $this->assertArrayHasKey('47', $prefectures);
  }

  public function testGetPrefecturesHasCorrectValues() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertSame('北海道', $prefectures['01']);
    $this->assertSame('東京都', $prefectures['13']);
    $this->assertSame('大阪府', $prefectures['27']);
    $this->assertSame('沖縄県', $prefectures['47']);
  }

  public function testValidateAcceptsValidPrefectureCode() {
    $select = new PrefectureCodeSelect();
    $select->setValue('13'); // Tokyo

    $this->assertTrue($select->validate());
    $this->assertNull($select->getError());
  }

  public function testValidateRejectsInvalidCode() {
    $select = new PrefectureCodeSelect();
    $select->setValue('99');

    $this->assertFalse($select->validate());
    $this->assertInstanceOf(NotInOptionsError::class, $select->getError());
  }

  public function testValidateEmptyRequiredField() {
    $select = (new PrefectureCodeSelect())->setRequired(true);
    $select->setValue('');

    $this->assertFalse($select->validate());
    $this->assertInstanceOf(EmptyError::class, $select->getError());
  }

  public function testValidateEmptyOptionalField() {
    $select = (new PrefectureCodeSelect())->setRequired(false);
    $select->setValue('');

    $this->assertTrue($select->validate());
    $this->assertNull($select->getError());
  }

  public function testGetPrefectureReturnsSelectedName() {
    $select = new PrefectureCodeSelect();
    $select->setValue('13');

    $this->assertSame('東京都', $select->getPrefecture());
  }

  public function testGetPrefectureReturnsNullWhenEmpty() {
    $select = new PrefectureCodeSelect();
    $select->setValue('');

    $this->assertNull($select->getPrefecture());
  }

  public function testGetPrefectureReturnsNullWhenInvalid() {
    $select = new PrefectureCodeSelect();
    $select->setValue('99');

    $this->assertNull($select->getPrefecture());
  }


  public function testGetValueReturnsCode() {
    $select = new PrefectureCodeSelect();
    $select->setValue('13');

    $this->assertSame('13', $select->getValue());
  }

  public function testGetSelectedLabelReturnsName() {
    $select = new PrefectureCodeSelect();
    $select->setValue('27'); // Osaka

    $this->assertSame('大阪府', $select->getSelectedLabel());
  }

  public function testAllPrefectureCodesAreValid() {
    $select = new PrefectureCodeSelect();
    $prefectures = PrefectureCodeSelect::getPrefectures();

    foreach (array_keys($prefectures) as $code) {
      $select->setValue($code);
      $this->assertTrue($select->validate(), "Prefecture code $code should be valid");
    }
  }

  public function testPrefectureCodesAreZeroPadded() {
    $prefectures = PrefectureCodeSelect::getPrefectures();
    $codes = array_keys($prefectures);

    foreach ($codes as $code) {
      $this->assertMatchesRegularExpression('/^\d{2}$/', $code, "Code $code should be 2 digits");
    }
  }

  public function testEmptyOptionIsFirst() {
    $select = new PrefectureCodeSelect();
    $options = $select->getOptions();
    $keys = array_keys($options);

    $this->assertSame('', $keys[0]);
    $this->assertSame('', $options['']);
  }

  public function testHokkaidoIsFirst() {
    $prefectures = PrefectureCodeSelect::getPrefectures();
    $keys = array_keys($prefectures);

    $this->assertSame('01', $keys[0]);
    $this->assertSame('北海道', $prefectures['01']);
  }

  public function testOkinawaIsLast() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertSame(47, array_key_last($prefectures));
    $this->assertSame('沖縄県', $prefectures['47']);
  }

  public function testTokyoOsakaKyotoHaveFuOrTo() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertSame('東京都', $prefectures['13']); // 都 (to)
    $this->assertSame('京都府', $prefectures['26']); // 府 (fu)
    $this->assertSame('大阪府', $prefectures['27']); // 府 (fu)
  }

  public function testHokkaidoDoesNotHaveKenSuffix() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertSame('北海道', $prefectures['01']); // 道 (dō), not 県
  }

  public function testSetEmptyOptionLabel() {
    $select = new PrefectureCodeSelect();
    $select->setEmptyOptionLabel('選択してください');
    $options = $select->getOptions();

    $this->assertSame('選択してください', $options['']);
  }

  public function testGetPrefecturesDoesNotIncludeEmptyOption() {
    $prefectures = PrefectureCodeSelect::getPrefectures();

    $this->assertArrayNotHasKey('', $prefectures);
    $this->assertCount(47, $prefectures);
  }
}
