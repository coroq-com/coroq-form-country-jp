<?php
use Coroq\Form\Country\Jp\FormItem\PrefectureSelect;
use Coroq\Form\Error\EmptyError;
use Coroq\Form\Error\NotInOptionsError;
use PHPUnit\Framework\TestCase;

class PrefectureSelectTest extends TestCase {
  public function testConstructorSetsAllPrefectures() {
    $select = new PrefectureSelect();
    $options = $select->getOptions();

    $this->assertCount(48, $options);
    $this->assertSame('', $options['']);
    $this->assertSame('北海道', $options['北海道']);
    $this->assertSame('東京都', $options['東京都']);
    $this->assertSame('沖縄県', $options['沖縄県']);
  }

  public function testGetPrefecturesReturnsAll47Prefectures() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertCount(47, $prefectures);
    $this->assertIsArray($prefectures);
  }

  public function testGetPrefecturesHasCorrectValues() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertSame('北海道', $prefectures[0]);
    $this->assertSame('東京都', $prefectures[12]);
    $this->assertSame('沖縄県', $prefectures[46]);
  }

  public function testValidateAcceptsValidPrefecture() {
    $select = new PrefectureSelect();
    $select->setValue('東京都');

    $this->assertTrue($select->validate());
    $this->assertNull($select->getError());
  }

  public function testValidateRejectsInvalidPrefecture() {
    $select = new PrefectureSelect();
    $select->setValue('存在しない県');

    $this->assertFalse($select->validate());
    $this->assertInstanceOf(NotInOptionsError::class, $select->getError());
  }

  public function testValidateEmptyRequiredField() {
    $select = (new PrefectureSelect())->setRequired(true);
    $select->setValue('');

    $this->assertFalse($select->validate());
    $this->assertInstanceOf(EmptyError::class, $select->getError());
  }

  public function testValidateEmptyOptionalField() {
    $select = (new PrefectureSelect())->setRequired(false);
    $select->setValue('');

    $this->assertTrue($select->validate());
    $this->assertNull($select->getError());
  }

  public function testGetPrefectureReturnsSelectedName() {
    $select = new PrefectureSelect();
    $select->setValue('東京都');

    $this->assertSame('東京都', $select->getPrefecture());
  }

  public function testGetPrefectureReturnsNullWhenEmpty() {
    $select = new PrefectureSelect();
    $select->setValue('');

    $this->assertNull($select->getPrefecture());
  }

  public function testGetPrefectureReturnsNullWhenInvalid() {
    $select = new PrefectureSelect();
    $select->setValue('存在しない県');

    $this->assertNull($select->getPrefecture());
  }


  public function testGetValueReturnsPrefectureName() {
    $select = new PrefectureSelect();
    $select->setValue('東京都');

    $this->assertSame('東京都', $select->getValue());
  }

  public function testGetSelectedLabelReturnsSameValue() {
    $select = new PrefectureSelect();
    $select->setValue('大阪府');

    $this->assertSame('大阪府', $select->getSelectedLabel());
  }

  public function testAllPrefecturesAreValid() {
    $select = new PrefectureSelect();
    $prefectures = PrefectureSelect::getPrefectures();

    foreach ($prefectures as $name) {
      $select->setValue($name);
      $this->assertTrue($select->validate(), "Prefecture $name should be valid");
    }
  }

  public function testEmptyOptionIsFirst() {
    $select = new PrefectureSelect();
    $options = $select->getOptions();
    $keys = array_keys($options);

    $this->assertSame('', $keys[0]);
    $this->assertSame('', $options['']);
  }

  public function testHokkaidoIsFirst() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertSame('北海道', $prefectures[0]);
  }

  public function testOkinawaIsLast() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertSame('沖縄県', $prefectures[46]);
  }

  public function testTokyoOsakaKyotoHaveFuOrTo() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertContains('東京都', $prefectures); // 都 (to)
    $this->assertContains('京都府', $prefectures); // 府 (fu)
    $this->assertContains('大阪府', $prefectures); // 府 (fu)
  }

  public function testHokkaidoDoesNotHaveKenSuffix() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertContains('北海道', $prefectures); // 道 (dō), not 県
  }

  public function testSetEmptyOptionLabel() {
    $select = new PrefectureSelect();
    $select->setEmptyOptionLabel('選択してください');
    $options = $select->getOptions();

    $this->assertSame('選択してください', $options['']);
  }

  public function testGetPrefecturesDoesNotIncludeEmptyOption() {
    $prefectures = PrefectureSelect::getPrefectures();

    $this->assertNotContains('', $prefectures);
    $this->assertCount(47, $prefectures);
  }
}
