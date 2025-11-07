<?php
declare(strict_types=1);
namespace Coroq\Form\Country\Jp\FormItem;

use Coroq\Form\Error\Error;
use Coroq\Form\FormItem\Input;
use Coroq\Form\FormItem\StringFilterTrait;
use Coroq\Form\Country\Jp\Error\InvalidPostalCodeError;

/**
 * Japanese postal code input with automatic format normalization
 *
 * Accepts both "1234567" and "123-4567" formats during input.
 * Normalizes to configured format (with or without hyphen).
 */
class PostalCodeInput extends Input {
  use StringFilterTrait;

  private bool $withHyphen = false;

  /**
   * Set output format style
   *
   * @param bool $withHyphen true for "123-4567", false for "1234567"
   * @return self
   */
  public function setWithHyphen(bool $withHyphen): self {
    $this->withHyphen = $withHyphen;
    return $this;
  }

  /**
   * Get the configured format style
   *
   * @return bool
   */
  public function getWithHyphen(): bool {
    return $this->withHyphen;
  }

  /**
   * Filter input to normalize postal code format
   *
   * @param mixed $value
   * @return mixed
   */
  public function filter($value): string {
    $value = "$value";
    $value = $this->scrubUtf8($value);
    $value = $this->toHalfwidthAscii($value);
    $value = $this->removeWhitespace($value);
    // Convert prolonged sound mark to hyphen (not handled by toHalfwidthAscii)
    $value = preg_replace('/ー/u', '-', $value);

    if ($this->withHyphen) {
      if ($this->isValidPostalCodeWithoutHyphen($value)) {
        $value = substr($value, 0, 3) . '-' . substr($value, 3);
      }
    }
    else {
      if ($this->isValidPostalCodeWithHyphen($value)) {
        $value = preg_replace('/-/u', '', $value);
      }
    }

    return $value;
  }

  /**
   * Check if value is a valid postal code with hyphen (123-4567)
   *
   * @param string $value
   * @return bool
   */
  private function isValidPostalCodeWithHyphen(string $value): bool {
    return preg_match('/^[0-9]{3}-[0-9]{4}$/', $value) === 1;
  }

  /**
   * Check if value is a valid postal code without hyphen (1234567)
   *
   * @param string $value
   * @return bool
   */
  private function isValidPostalCodeWithoutHyphen(string $value): bool {
    return preg_match('/^[0-9]{7}$/', $value) === 1;
  }

  /**
   * Check if value is a valid postal code
   *
   * @param string $value
   * @return bool
   */
  private function isValidPostalCode(string $value): bool {
    return $this->withHyphen
      ? $this->isValidPostalCodeWithHyphen($value)
      : $this->isValidPostalCodeWithoutHyphen($value);
  }

  /**
   * Validate postal code format
   *
   * @param mixed $value
   * @return Error|null
   */
  public function doValidate($value): ?Error {
    if (!$this->isValidPostalCode($value)) {
      return new InvalidPostalCodeError($this);
    }

    return parent::doValidate($value);
  }

  /**
   * Get validated postal code
   *
   * @return string|null Returns postal code in configured format, or null if invalid/empty
   */
  public function getPostalCode(): ?string {
    if ($this->isEmpty()) {
      return null;
    }
    $value = $this->getValue();

    if (!$this->isValidPostalCode($value)) {
      return null;
    }
    return $value;
  }

  /**
   * Get postal code as parsed value (same as getPostalCode)
   *
   * @return string|null
   */
  public function getParsedValue(): ?string {
    return $this->getPostalCode();
  }
}
