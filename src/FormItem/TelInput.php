<?php
declare(strict_types=1);
namespace Coroq\Form\Country\Jp\FormItem;

use Coroq\Form\FormItem\Input;
use Coroq\Form\FormItem\StringFilterTrait;
use Coroq\Form\Country\Jp\Error\InvalidTelError;
use Coroq\Form\Error\Error;

/**
 * Japanese domestic telephone number input
 *
 * Validates basic format: starts with 0, 10-11 digits.
 * Does not validate specific area codes or number ranges.
 * Supports optional hyphen mode.
 */
class TelInput extends Input {
  use StringFilterTrait;

  private bool $withHyphen = false;

  /**
   * Set whether to require hyphens in telephone number
   *
   * @param bool $withHyphen
   * @return self
   */
  public function setWithHyphen(bool $withHyphen): self {
    $this->withHyphen = $withHyphen;
    $this->setValue($this->getValue());
    return $this;
  }

  /**
   * Filter telephone number input
   *
   * @param mixed $value
   * @return string
   */
  public function filter($value): string {
    $value = "$value";
    $value = $this->scrubUtf8($value);
    $value = $this->toHalfwidthAscii($value);
    $value = $this->removeWhitespace($value);

    if (!$this->withHyphen) {
      $value = preg_replace('/-/u', '', $value);
    }

    return $value;
  }

  /**
   * Validate telephone number
   *
   * @param mixed $value
   * @return Error|null
   */
  public function doValidate($value): ?Error {
    $value = "$value";
    if (!$this->isValidTel($value)) {
      return new InvalidTelError($this);
    }
    return parent::doValidate($value);
  }

  /**
   * Check if value is a valid Japanese telephone number
   *
   * @param string $value
   * @return bool
   */
  private function isValidTel(string $value): bool {
    if ($this->withHyphen) {
      return $this->isValidTelWithHyphen($value);
    }
    return $this->isValidTelWithoutHyphen($value);
  }

  /**
   * Validate telephone number without hyphens
   * Format: starts with 0, 10-11 digits
   *
   * @param string $value
   * @return bool
   */
  private function isValidTelWithoutHyphen(string $value): bool {
    if (!str_starts_with($value, '0')) {
      return false;
    }
    $length = strlen($value);
    if ($length !== 10 && $length !== 11) {
      return false;
    }
    return ctype_digit($value);
  }

  /**
   * Validate telephone number with hyphens
   * Rules: 1) starts with 0, 2) exactly 2 hyphens, 3) 10-11 digits, 4) all digits
   *
   * @param string $value
   * @return bool
   */
  private function isValidTelWithHyphen(string $value): bool {
    // Must start with 0
    if (!str_starts_with($value, '0')) {
      return false;
    }

    // Must contain exactly 2 hyphens
    if (substr_count($value, '-') !== 2) {
      return false;
    }

    // Remove hyphens and check if all digits
    $digitsOnly = str_replace('-', '', $value);
    if (!ctype_digit($digitsOnly)) {
      return false;
    }

    // Must have 10-11 digits total
    $digitCount = strlen($digitsOnly);
    return $digitCount === 10 || $digitCount === 11;
  }

  /**
   * Get the telephone number value
   *
   * @return string|null
   */
  public function getTel(): ?string {
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
   * Get telephone number as parsed value (same as getTel)
   *
   * @return string|null
   */
  public function getParsedValue(): ?string {
    return $this->getTel();
  }
}
