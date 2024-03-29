<?php

/**
 * @file
 * Contains \Mozart\Component\Support\Random.
 */

namespace Mozart\Component\Support;

/**
 * Defines a utility class for creating random data.
 *
 * @ingroup utility
 */
class Random
{
  /**
   * The maximum number of times name() and string() can loop.
   *
   * This prevents infinite loops if the length of the random value is very
   * small.
   *
   */
  const MAXIMUM_TRIES = 100;

  /**
   * A list of unique strings generated by string().
   *
   * @var array
   */
  protected $strings = array();

  /**
   * A list of unique names generated by name().
   *
   * @var array
   */
  protected $names = array();

  /**
   * Generates a random string of ASCII characters of codes 32 to 126.
   *
   * The generated string includes alpha-numeric characters and common
   * miscellaneous characters. Use this method when testing general input
   * where the content is not restricted.
   *
   * @param int $length
   *   Length of random string to generate.
   * @param bool $unique
   *   (optional) If TRUE ensures that the random string returned is unique.
   *   Defaults to FALSE.
   * @param callable $validator
   *   (optional) A callable to validate the the string. Defaults to NULL.
   *
   * @return string
   *   Randomly generated string.
   *
   * @see \Mozart\Component\Support\Random::name()
   */
  public function string($length = 8, $unique = FALSE, $validator = NULL)
  {
    $counter = 0;

    // Continue to loop if $unique is TRUE and the generated string is not
    // unique or if $validator is a callable that returns FALSE. To generate a
    // random string this loop must be carried out at least once.
    do {
      if ($counter == static::MAXIMUM_TRIES) {
        throw new \RuntimeException('Unable to generate a unique random name');
      }
      $str = '';
      for ($i = 0; $i < $length; $i++) {
        $str .= chr(mt_rand(32, 126));
      }
      $counter++;

      $continue = FALSE;
      if ($unique) {
        $continue = isset($this->strings[$str]);
      }
      if (!$continue && is_callable($validator)) {
        // If the validator callback returns FALSE generate another random
        // string.
        $continue = !call_user_func($validator, $str);
      }
    } while ($continue);

    if ($unique) {
      $this->strings[$str] = TRUE;
    }

    return $str;
  }

  /**
   * Generates a random string containing letters and numbers.
   *
   * The string will always start with a letter. The letters may be upper or
   * lower case. This method is better for restricted inputs that do not
   * accept certain characters. For example, when testing input fields that
   * require machine readable values (i.e. without spaces and non-standard
   * characters) this method is best.
   *
   * @param int $length
   *   Length of random string to generate.
   * @param bool $unique
   *   (optional) If TRUE ensures that the random string returned is unique.
   *   Defaults to FALSE.
   *
   * @return string
   *   Randomly generated string.
   *
   * @see \Mozart\Component\Support\Random::string()
   */
  public function name($length = 8, $unique = FALSE)
  {
    $values = array_merge(range(65, 90), range(97, 122), range(48, 57));
    $max = count($values) - 1;
    $counter = 0;

    do {
      if ($counter == static::MAXIMUM_TRIES) {
        throw new \RuntimeException('Unable to generate a unique random name');
      }
      $str = chr(mt_rand(97, 122));
      for ($i = 1; $i < $length; $i++) {
        $str .= chr($values[mt_rand(0, $max)]);
      }
      $counter++;
    } while ($unique && isset($this->names[$str]));

    if ($unique) {
      $this->names[$str] = TRUE;
    }

    return $str;
  }

  /**
   * Generates a random PHP object.
   *
   * @param int $size
   *   The number of random keys to add to the object.
   *
   * @return \stdClass
   *   The generated object, with the specified number of random keys. Each key
   *   has a random string value.
   */
  public function object($size = 4)
  {
    $object = new \stdClass();
    for ($i = 0; $i < $size; $i++) {
      $random_key = $this->name();
      $random_value = $this->string();
      $object->{$random_key} = $random_value;
    }

    return $object;
  }

}
