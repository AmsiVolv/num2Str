<?php
declare(strict_types=1);

namespace Amsi\Libs;

use InvalidArgumentException;

/**
 * Class Num2Str
 * @package Amsi\Libs
 */
class Num2Str
{
    private const UA_LOCALE = 'ua';
    private const RU_LOCALE = 'ru';
    private const ERROR_MESSAGE = 'Locale [%s] is not supported. Try [%s] or [%s]';

    private const ZERO = [
        self::UA_LOCALE => [
            'нуль',
        ],
        self::RU_LOCALE => [
            'ноль',
        ]
    ];
    private const SINGLE_DIGIT_NUMBERS = [
        self::UA_LOCALE => [
            ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять',],
            ['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять',],
        ],
        self::RU_LOCALE => [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',],
        ],
    ];
    private const SINGLE_DIGIT_NUMBERS_FOR_WITHOUT_UNITS = [
        self::UA_LOCALE => [
            ['', 'один', 'два', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять',],
            ['', 'один', 'два', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять',],
        ],
        self::RU_LOCALE => [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',],
        ],
    ];
    private const TWO_DIGIT_NUMBERS = [
        self::UA_LOCALE => [
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
        ],
        self::RU_LOCALE => [
            'десять',
            'одиннадцать',
            'двенадцать',
            'тринадцать',
            'четырнадцать',
            'пятнадцать',
            'шестнадцать',
            'семнадцать',
            'восемнадцать',
            'девятнадцать',
        ],
    ];
    private const TENS = [
        self::UA_LOCALE => [
            2 => 'двадцять',
            'тридцять',
            'сорок',
            'п\'ятдесят',
            'шістдесят',
            'сімдесят',
            'вісімдесят',
            'дев\'яносто',
        ],
        self::RU_LOCALE => [
            2=>'двадцать',
            'тридцать',
            'сорок',
            'пятьдесят',
            'шестьдесят',
            'семьдесят',
            'восемьдесят',
            'девяносто',
        ],
    ];
    private const HUNDRED = [
        self::UA_LOCALE => [
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
        ],
        self::RU_LOCALE => [
            '',
            'сто',
            'двести',
            'триста',
            'четыреста',
            'пятьсот',
            'шестьсот',
            'семьсот',
            'восемьсот',
            'девятьсот',
        ],
    ];
    private const UNITS = [
        self::UA_LOCALE => [
            ['копійка', 'копійки', 'копійок', 1],
            ['гривня', 'гривні', 'гривень', 0],
            ['тисяча', 'тисячі', 'тисяч', 1],
            ['мільйон', 'мільйона', 'мільйонів', 0],
            ['мільярд', 'мільярда', 'мільярдів', 0],
        ],
        self::RU_LOCALE => [
            ['копейка' ,'копейки' ,'копеек', 1],
            ['рубль'   ,'рубля'   ,'рублей', 0],
            ['тысяча'  ,'тысячи'  ,'тысяч', 1],
            ['миллион' ,'миллиона','миллионов', 0],
            ['миллиард','милиарда','миллиардов', 0],
        ],
    ];

    private const DAYS = [
        self::UA_LOCALE => [
            'день', 'дня', 'днів'
        ],
        self::RU_LOCALE => [
            'день', 'дня', 'дней'
        ],
    ];

    private const SUPPORTED_LOCALES = [
        self::UA_LOCALE,
        self::RU_LOCALE,
    ];

    public static function convert(float $num, string $locale = self::UA_LOCALE): string
    {
        if (!self::isLocaleSupported($locale)) {
            throw new InvalidArgumentException(sprintf(self::ERROR_MESSAGE, $locale, self::RU_LOCALE, self::UA_LOCALE));
        }

        [$uah, $coins] = explode('.', sprintf("%015.2f", $num)); // Money formating

        $out = [];
        if (intval($uah) > 0) {
            foreach (str_split($uah, 3) as $unitKey => $value) { // by 3 symbols
                if (intval($value)) {
                    $unitKey = sizeof(self::UNITS[$locale]) - $unitKey - 1; // Get unit key of symbols

                    $gender = self::UNITS[$locale][$unitKey][3];
                    [$i1, $i2, $i3] = array_map('intval', str_split($value, 1)); // Separe 3 symbols ony by one

                    $out[] = self::HUNDRED[$locale][$i1]; # 1xx-9xx
                    if ($i2 > 1) {
                        $out[] = sprintf('%s %s', self::TENS[$locale][$i2], self::SINGLE_DIGIT_NUMBERS[$locale][$gender][$i3]);
                    } else { # 20-99
                        $out[] = $i2 > 0 ? self::TWO_DIGIT_NUMBERS[$locale][$i3] : self::SINGLE_DIGIT_NUMBERS[$locale][$gender][$i3];
                    } # 10-19 | 1-9
                    // units without uah & coins
                    if ($unitKey > 1) {
                        $out[] = self::morph(
                            $value,
                            self::UNITS[$locale][$unitKey][0],
                            self::UNITS[$locale][$unitKey][1],
                            self::UNITS[$locale][$unitKey][2]
                        );
                    }
                }
            } //foreach
        } else {
            $out[] = self::ZERO[$locale];
        }
        $out[] = self::morph($uah, self::UNITS[$locale][1][0], self::UNITS[$locale][1][1], self::UNITS[$locale][1][2]); // uah
        $out[] = sprintf('%s %s', $coins, self::morph($coins, self::UNITS[$locale][0][0], self::UNITS[$locale][0][1], self::UNITS[$locale][0][2])); // coins

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    public static function convertWithoutUnits(float $number, string $locale = self::UA_LOCALE): string
    {
        if (!self::isLocaleSupported($locale)) {
            throw new InvalidArgumentException(sprintf(self::ERROR_MESSAGE, $locale, self::RU_LOCALE, self::UA_LOCALE));
        }

        $out = [];
        [$number] = explode('.', sprintf("%015.2f", $number)); // Money formating

        if (intval($number) >= 0) {
            foreach (str_split($number, 3) as $unitKey => $value) { // by 3 symbols
                if (intval($value)) {
                    $gender = self::UNITS[$locale][$unitKey][3];

                    [$i1, $i2, $i3] = array_map('intval', str_split($value, 1)); // Separe 3 symbols ony by one

                    $out[] = self::HUNDRED[$locale][$i1]; # 1xx-9xx
                    if ($i2 > 1) {
                        $out[] = sprintf('%s %s', self::TENS[$locale][$i2], self::SINGLE_DIGIT_NUMBERS_FOR_WITHOUT_UNITS[$locale][$gender][$i3]);
                    } else { # 20-99
                        $out[] = $i2 > 0 ? self::TWO_DIGIT_NUMBERS[$locale][$i3] : self::SINGLE_DIGIT_NUMBERS_FOR_WITHOUT_UNITS[$locale][$gender][$i3];
                    } # 10-19 | 1-9

                    $out[] = self::morph($value, '', '', '');
                }
            }
        }

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

    public static function pluralForm(int $number, string $locale = self::UA_LOCALE, array $forms = self::DAYS): string
    {
        if (!self::isLocaleSupported($locale)) {
            throw new InvalidArgumentException(sprintf(self::ERROR_MESSAGE, $locale, self::RU_LOCALE, self::UA_LOCALE));
        }

        return $number%10===1&&$number%100!==11?$forms[$locale][0]:($number%10>=2&&$number%10<=4&&($number%100<10||$number%100>=20)?$forms[$locale][1]:$forms[$locale][2]);
    }

    public static function pluralFormWithConvertedString(int $number, string $locale = self::UA_LOCALE, array $forms = self::DAYS): string
    {
        if (!self::isLocaleSupported($locale)) {
            throw new InvalidArgumentException(sprintf(self::ERROR_MESSAGE, $locale, self::RU_LOCALE, self::UA_LOCALE));
        }

        return sprintf('%s %s', self::convertWithoutUnits($number, $locale), self::pluralForm($number, $locale, $forms));
    }

    private static function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }
}
