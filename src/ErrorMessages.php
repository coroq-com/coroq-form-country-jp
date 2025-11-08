<?php
declare(strict_types=1);
namespace Coroq\Form\Country\Jp;

use Coroq\Form\Country\Jp\Error\InvalidPostalCodeError;
use Coroq\Form\Country\Jp\Error\InvalidTelError;

/**
 * Japanese error messages for form validation
 */
class ErrorMessages {
  /**
   * Get Japanese error messages
   *
   * @return array<string, string>
   */
  public static function get(): array {
    return [
      InvalidPostalCodeError::class => '正しい郵便番号を入力してください',
      InvalidTelError::class => '正しい電話番号を入力してください',
    ];
  }
}
