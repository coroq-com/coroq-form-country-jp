<?php
declare(strict_types=1);
namespace Coroq\Form\Country\Jp\FormItem;

use Coroq\Form\FormItem\Select;

/**
 * Japanese prefecture code selection (47 prefectures)
 *
 * Provides options for all 47 Japanese prefectures in standard order.
 * Values are 2-digit codes (01-47), labels are prefecture names.
 */
class PrefectureCodeSelect extends Select {
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
   * @return array<string, string> Prefecture code => Prefecture name
   */
  private function getPrefectureOptions(): array {
    return ['' => $this->emptyOptionLabel] + self::getPrefectures();
  }

  /**
   * Get all 47 Japanese prefectures
   *
   * @return array<string, string> Prefecture code => Prefecture name
   */
  public static function getPrefectures(): array {
    return [
      '01' => '北海道',
      '02' => '青森県',
      '03' => '岩手県',
      '04' => '宮城県',
      '05' => '秋田県',
      '06' => '山形県',
      '07' => '福島県',
      '08' => '茨城県',
      '09' => '栃木県',
      '10' => '群馬県',
      '11' => '埼玉県',
      '12' => '千葉県',
      '13' => '東京都',
      '14' => '神奈川県',
      '15' => '新潟県',
      '16' => '富山県',
      '17' => '石川県',
      '18' => '福井県',
      '19' => '山梨県',
      '20' => '長野県',
      '21' => '岐阜県',
      '22' => '静岡県',
      '23' => '愛知県',
      '24' => '三重県',
      '25' => '滋賀県',
      '26' => '京都府',
      '27' => '大阪府',
      '28' => '兵庫県',
      '29' => '奈良県',
      '30' => '和歌山県',
      '31' => '鳥取県',
      '32' => '島根県',
      '33' => '岡山県',
      '34' => '広島県',
      '35' => '山口県',
      '36' => '徳島県',
      '37' => '香川県',
      '38' => '愛媛県',
      '39' => '高知県',
      '40' => '福岡県',
      '41' => '佐賀県',
      '42' => '長崎県',
      '43' => '熊本県',
      '44' => '大分県',
      '45' => '宮崎県',
      '46' => '鹿児島県',
      '47' => '沖縄県',
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
    $prefectures = self::getPrefectures();
    return $prefectures[$value];
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
