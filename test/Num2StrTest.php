<?php

namespace Unit;

use Amsi\Libs\Num2str;
use PHPUnit\Framework\TestCase;

/**
 * Class Num2StrTest
 */
class Num2StrTest extends TestCase
{
    private const WRONG_VALUES = 'wrongValues';
    private const CORRECT_VALUES = 'correctValues';

    private const RU_LOCALE = 'ru';
    private const UA_LOCALE = 'ua';

    private const TEST_CASES = [
        1 => [
            self::WRONG_VALUES => [
                'Одна гривня 0 копійок',
                'Одна гривна 00 копійка',
                '1 гривня 00 копійка',
            ],
            self::CORRECT_VALUES => 'одна гривня 00 копійок'
        ],
        123456789 => [
            self::WRONG_VALUES => [
                'Сто двадцять три мільйона чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять карбованців 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'сто двадцять три мільйона чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять гривень 00 копійок'
        ],
        111111 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'сто одинадцять тисяч сто одинадцять гривень 00 копійок'
        ],
        222222 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'двісті двадцять дві тисячі двісті двадцять дві гривні 00 копійок'
        ],
        333333 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'триста тридцять три тисячі триста тридцять три гривні 00 копійок'
        ],
        444444 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'чотириста сорок чотири тисячі чотириста сорок чотири гривні 00 копійок'
        ],
        555555 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'п\'ятсот п\'ятдесят п\'ять тисяч п\'ятсот п\'ятдесят п\'ять гривень 00 копійок'
        ],
        666666 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'шістсот шістдесят шість тисяч шістсот шістдесят шість гривень 00 копійок'
        ],
        777777 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'сімсот сімдесят сім тисяч сімсот сімдесят сім гривень 00 копійок'
        ],
        888888 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'вісімсот вісімдесят вісім тисяч вісімсот вісімдесят вісім гривень 00 копійок'
        ],
        999999 => [
            self::WRONG_VALUES => [
                'Пять тисяч двадцять чотири гривнi 00 копійок',
                'Сто двадцять три мільйони чотириста п\'ятдесят шість тисяч сімсот вісімдесят дев\'ять рублів 00 копійок',
            ],
            self::CORRECT_VALUES => 'дев\'ятсот дев\'яносто дев\'ять тисяч дев\'ятсот дев\'яносто дев\'ять гривень 00 копійок'
        ],
    ];

    private const TEST_CASES_WITHOUT_UNITS = [
        2 => [
            self::WRONG_VALUES => [
                'Одна гривня 0 копійок',
                'Одна гривна 00 копійка',
                '1 гривня 00 копійка',
            ],
            self::CORRECT_VALUES => 'два'
        ],
        1 => [
            self::WRONG_VALUES => [
                'Одна гривня 0 копійок',
                'Одна гривна 00 копійка',
                '1 гривня 00 копійка',
            ],
            self::CORRECT_VALUES => 'один'
        ],
        31 => [
            self::WRONG_VALUES => [
                'Одна гривня 0 копійок',
                'Одна гривна 00 копійка',
                '1 гривня 00 копійка',
            ],
            self::CORRECT_VALUES => 'тридцять один'
        ]
    ];

    private const PLURAL_TEST = [
        1 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'дня',
                    'днів',
                ],
                self::CORRECT_VALUES => 'день',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'дней',
                    'дня',
                ],
                self::CORRECT_VALUES => 'день',
            ],
        ],
        15 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'дня',
                    'день',
                ],
                self::CORRECT_VALUES => 'днів',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'дня',
                ],
                self::CORRECT_VALUES => 'дней',
            ],
        ],
        2 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'днів',
                ],
                self::CORRECT_VALUES => 'дня',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'дней',
                ],
                self::CORRECT_VALUES => 'дня',
            ],
        ],
    ];

    private const PLURAL_TEST_NUMBER_STRING = [
        1 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'дня',
                    'днів',
                ],
                self::CORRECT_VALUES => 'один день',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'дней',
                    'дня',
                ],
                self::CORRECT_VALUES => 'один день',
            ],
        ],
        15 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'дня',
                    'день',
                ],
                self::CORRECT_VALUES => 'п\'ятнадцять днів',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'дня',
                ],
                self::CORRECT_VALUES => 'пятнадцать дней',
            ],
        ],
        2 => [
            self::UA_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'днів',
                ],
                self::CORRECT_VALUES => 'два дня',
            ],
            self::RU_LOCALE => [
                self::WRONG_VALUES => [
                    'день',
                    'дней',
                ],
                self::CORRECT_VALUES => 'два дня',
            ],
        ],
    ];

    public function testNum2StrConverting()
    {
        foreach (self::TEST_CASES as $number => $results) {
            $formattedNumber = \CoolCredit\Libs\Utils\Num2Str::convert($number);
            foreach ($results[self::WRONG_VALUES] as $wrongValue) {
                $this->assertNotEquals($formattedNumber, $wrongValue);
            }
            $this->assertEquals($formattedNumber, $results[self::CORRECT_VALUES]);
        }
    }

    public function testNum2StrConvertingWithoutUnits()
    {
        foreach (self::TEST_CASES_WITHOUT_UNITS as $number => $results) {
            $formattedNumber = Num2Str::convertWithoutUnits($number);
            foreach ($results[self::WRONG_VALUES] as $wrongValue) {
                $this->assertNotEquals($formattedNumber, $wrongValue);
            }
            $this->assertEquals($formattedNumber, $results[self::CORRECT_VALUES]);
        }
    }

    public function testPluralForm()
    {
        foreach (self::PLURAL_TEST_NUMBER_STRING as $number => $locales) {
            foreach ($locales as $locale => $testCases) {
                $formattedNumber = Num2Str::pluralFormWithConvertedString($number, $locale);
                foreach ($testCases[self::WRONG_VALUES] as $wrongValue) {
                    $this->assertNotEquals($formattedNumber, $wrongValue);
                }
                $this->assertEquals($formattedNumber, $testCases[self::CORRECT_VALUES]);
            }
        }
        var_dump(Num2Str::pluralFormWithConvertedString(10));
    }
}
