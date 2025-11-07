<?php
declare(strict_types=1);
namespace Coroq\Form\Country\Jp\FormItem;

use Coroq\Form\FormItem\Select;

/**
 * Japanese prefecture selection (47 prefectures)
 *
 * Provides options for all 47 Japanese prefectures in standard order.
 * Both values and labels are prefecture names (e.g., "東京都").
 */
class PrefectureSelect extends Select {
  private string $emptyOptionLabel = '';

  public function __construct() {
    parent::__construct();
    $this->setOptions($this->getPrefectureOptions());
  }

  /**
   * Set the label for the empty option
   *
   * @param string $label Label for empty option (e.g., "選択してください")
   * @return self
   */
  public function setEmptyOptionLabel(string $label): self {
    $this->emptyOptionLabel = $label;
    $this->setOptions($this->getPrefectureOptions());
    return $this;
  }

  /**
   * Get prefecture options with empty option at the top
   *
   * @return array<string, string> Prefecture name => Prefecture name
   */
  private function getPrefectureOptions(): array {
    $names = self::getPrefectures();
    $options = array_combine($names, $names);
    return ['' => $this->emptyOptionLabel] + $options;
  }

  /**
   * Get all 47 Japanese prefectures
   *
   * @return array<int, string> Prefecture names
   */
  public static function getPrefectures(): array {
    return [
      '北海道',
      '青森県',
      '岩手県',
      '宮城県',
      '秋田県',
      '山形県',
      '福島県',
      '茨城県',
      '栃木県',
      '群馬県',
      '埼玉県',
      '千葉県',
      '東京都',
      '神奈川県',
      '新潟県',
      '富山県',
      '石川県',
      '福井県',
      '山梨県',
      '長野県',
      '岐阜県',
      '静岡県',
      '愛知県',
      '三重県',
      '滋賀県',
      '京都府',
      '大阪府',
      '兵庫県',
      '奈良県',
      '和歌山県',
      '鳥取県',
      '島根県',
      '岡山県',
      '広島県',
      '山口県',
      '徳島県',
      '香川県',
      '愛媛県',
      '高知県',
      '福岡県',
      '佐賀県',
      '長崎県',
      '熊本県',
      '大分県',
      '宮崎県',
      '鹿児島県',
      '沖縄県',
    ];
  }

  /**
   * Get the selected prefecture name
   *
   * @return string|null
   */
  public function getPrefecture(): ?string {
    if ($this->isEmpty()) {
      return null;
    }
    $value = $this->getValue();
    if ($this->doValidate($value) !== null) {
      return null;
    }
    return $value;
  }

  /**
   * Get prefecture name as parsed value (same as getPrefecture)
   *
   * @return string|null
   */
  public function getParsedValue(): ?string {
    return $this->getPrefecture();
  }
}
