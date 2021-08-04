<?php
declare(strict_types=1);

namespace Amsi\Libs;

/**
 * Class Num2Str
 * @package Amsi\Libs
 */
class Num2Str
{
    private const ZERO = 'нуль';
    private const SINGLE_DIGIT_NUMBERS = [
        ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
        ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
    ];
    private const TWO_DIGIT_NUMBERS = [
        'десять',
        'одинадцять',
        'дванадцять',
        'тринадцять',
        'чотирнадцять',
        'п\'ятнадцять',
        'шістнадцять',
        'сімнадцять',
        'вісімнадцять',
        'дев\'ятнадцять ',
    ];
    private const TENS = [
        2 => 'двадцять',
        'тридцять',
        'сорок',
        'п\'ятдесят',
        'шістдесят',
        'сімдесят',
        'вісімдесят',
        'дев\'яносто',
    ];
    private const HUNDRED = [
        '',
        'сто',
        'двісті',
        'триста',
        'чотириста',
        'п\'ятсот',
        'шістсот',
        'сімсот',
        'вісімсот',
        'дев\'ятсот',
    ];
    private const UNITS = [
        ['копійка', 'копійки', 'копійок', 1],
        ['гривня', 'гривні', 'гривень', 0],
        ['тисяча', 'тисячі', 'тисяч', 1],
        ['мільйон', 'мільйона', 'мільйонів', 0],
        ['мільярд', 'мільярда', 'мільярдів', 0],
    ];

    public static function convert(float $num): string
    {
        [$uah, $coins] = explode('.', sprintf("%015.2f", $num)); // Money formating

        $out = [];
        if (intval($uah) > 0) {
            foreach (str_split($uah, 3) as $unitKey => $value) { // by 3 symbols
                if (intval($value)) {
                    $unitKey = sizeof(self::UNITS) - $unitKey - 1; // Get unit key of symbols

                    $gender = self::UNITS[$unitKey][3];
                    [$i1, $i2, $i3] = array_map('intval', str_split($value, 1)); // Separe 3 symbols ony by one

                    $out[] = self::HUNDRED[$i1]; # 1xx-9xx
                    if ($i2 > 1) {
                        $out[] = sprintf('%s %s', self::TENS[$i2], self::SINGLE_DIGIT_NUMBERS[$gender][$i3]);
                    } else { # 20-99
                        $out[] = $i2 > 0 ? self::TWO_DIGIT_NUMBERS[$i3] : self::SINGLE_DIGIT_NUMBERS[$gender][$i3];
                    } # 10-19 | 1-9
                    // units without uah & coins
                    if ($unitKey > 1) {
                        $out[] = self::morph(
                            $value,
                            self::UNITS[$unitKey][0],
                            self::UNITS[$unitKey][1],
                            self::UNITS[$unitKey][2]
                        );
                    }
                }
            } //foreach
        } else {
            $out[] = self::ZERO;
        }
        $out[] = self::morph($uah, self::UNITS[1][0], self::UNITS[1][1], self::UNITS[1][2]); // uah
        $out[] = sprintf('%s %s', $coins, self::morph($coins, self::UNITS[0][0], self::UNITS[0][1], self::UNITS[0][2])); // coins

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    private static function morph(string $n, string $f1, string $f2, string $f5): string
    {
        $n = abs(intval($n)) % 100;

        if ($n > 10 && $n < 20) {
            return $f5;
        }

        $n = $n % 10;
        if ($n > 1 && $n < 5) {
            return $f2;
        }

        if ($n === 1) {
            return $f1;
        }

        return $f5;
    }
}
