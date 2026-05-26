<?php

namespace App\Support;

class PhoneNumber
{
    public static function flagForCode(string $dialingCode): string
    {
        $iso = self::countryIsoMap()[$dialingCode] ?? null;
        if (!$iso) {
            return '🌐';
        }

        return mb_chr(127397 + ord($iso[0]), 'UTF-8') . mb_chr(127397 + ord($iso[1]), 'UTF-8');
    }

    public static function countrySelectLabel(string $dialingCode, string $country): string
    {
        return self::flagForCode($dialingCode) . ' ' . $country . ' ' . $dialingCode;
    }

    public static function countries(): array
    {
        return [
            '+93' => 'Afghanistan',
            '+355' => 'Albania',
            '+213' => 'Algeria',
            '+1684' => 'American Samoa',
            '+376' => 'Andorra',
            '+244' => 'Angola',
            '+1264' => 'Anguilla',
            '+672' => 'Antarctica / Norfolk Island',
            '+1268' => 'Antigua and Barbuda',
            '+54' => 'Argentina',
            '+374' => 'Armenia',
            '+297' => 'Aruba',
            '+61' => 'Australia / Christmas Island / Cocos Islands',
            '+43' => 'Austria',
            '+994' => 'Azerbaijan',
            '+1242' => 'Bahamas',
            '+973' => 'Bahrain',
            '+880' => 'Bangladesh',
            '+1246' => 'Barbados',
            '+375' => 'Belarus',
            '+32' => 'Belgium',
            '+501' => 'Belize',
            '+229' => 'Benin',
            '+1441' => 'Bermuda',
            '+975' => 'Bhutan',
            '+591' => 'Bolivia',
            '+387' => 'Bosnia and Herzegovina',
            '+267' => 'Botswana',
            '+55' => 'Brazil',
            '+246' => 'British Indian Ocean Territory',
            '+1284' => 'British Virgin Islands',
            '+673' => 'Brunei',
            '+359' => 'Bulgaria',
            '+226' => 'Burkina Faso',
            '+257' => 'Burundi',
            '+855' => 'Cambodia',
            '+237' => 'Cameroon',
            '+1' => 'United States / Canada / NANP Countries',
            '+238' => 'Cape Verde',
            '+1345' => 'Cayman Islands',
            '+236' => 'Central African Republic',
            '+235' => 'Chad',
            '+56' => 'Chile',
            '+86' => 'China',
            '+57' => 'Colombia',
            '+269' => 'Comoros / Mayotte',
            '+242' => 'Congo',
            '+243' => 'Congo DR',
            '+682' => 'Cook Islands',
            '+506' => 'Costa Rica',
            '+225' => 'Cote dIvoire',
            '+385' => 'Croatia',
            '+53' => 'Cuba',
            '+599' => 'Curacao / Caribbean Netherlands',
            '+357' => 'Cyprus',
            '+420' => 'Czech Republic',
            '+45' => 'Denmark',
            '+253' => 'Djibouti',
            '+1767' => 'Dominica',
            '+1809' => 'Dominican Republic',
            '+1829' => 'Dominican Republic',
            '+1849' => 'Dominican Republic',
            '+593' => 'Ecuador',
            '+20' => 'Egypt',
            '+503' => 'El Salvador',
            '+240' => 'Equatorial Guinea',
            '+291' => 'Eritrea',
            '+372' => 'Estonia',
            '+268' => 'Eswatini',
            '+251' => 'Ethiopia',
            '+500' => 'Falkland Islands',
            '+298' => 'Faroe Islands',
            '+679' => 'Fiji',
            '+358' => 'Finland / Aland Islands',
            '+33' => 'France',
            '+594' => 'French Guiana',
            '+689' => 'French Polynesia',
            '+241' => 'Gabon',
            '+220' => 'Gambia',
            '+995' => 'Georgia',
            '+49' => 'Germany',
            '+233' => 'Ghana',
            '+350' => 'Gibraltar',
            '+30' => 'Greece',
            '+299' => 'Greenland',
            '+1473' => 'Grenada',
            '+590' => 'Guadeloupe / Saint Barthelemy / Saint Martin',
            '+1671' => 'Guam',
            '+502' => 'Guatemala',
            '+44' => 'United Kingdom / Guernsey / Isle of Man / Jersey',
            '+224' => 'Guinea',
            '+245' => 'Guinea-Bissau',
            '+592' => 'Guyana',
            '+509' => 'Haiti',
            '+504' => 'Honduras',
            '+852' => 'Hong Kong',
            '+36' => 'Hungary',
            '+354' => 'Iceland',
            '+91' => 'India',
            '+62' => 'Indonesia',
            '+98' => 'Iran',
            '+964' => 'Iraq',
            '+353' => 'Ireland',
            '+972' => 'Israel',
            '+39' => 'Italy / Vatican City',
            '+1876' => 'Jamaica',
            '+81' => 'Japan',
            '+962' => 'Jordan',
            '+7' => 'Kazakhstan / Russia',
            '+254' => 'Kenya',
            '+686' => 'Kiribati',
            '+383' => 'Kosovo',
            '+965' => 'Kuwait',
            '+996' => 'Kyrgyzstan',
            '+856' => 'Laos',
            '+371' => 'Latvia',
            '+961' => 'Lebanon',
            '+266' => 'Lesotho',
            '+231' => 'Liberia',
            '+218' => 'Libya',
            '+423' => 'Liechtenstein',
            '+370' => 'Lithuania',
            '+352' => 'Luxembourg',
            '+853' => 'Macau',
            '+261' => 'Madagascar',
            '+265' => 'Malawi',
            '+60' => 'Malaysia',
            '+960' => 'Maldives',
            '+223' => 'Mali',
            '+356' => 'Malta',
            '+692' => 'Marshall Islands',
            '+596' => 'Martinique',
            '+222' => 'Mauritania',
            '+230' => 'Mauritius',
            '+52' => 'Mexico',
            '+691' => 'Micronesia',
            '+373' => 'Moldova',
            '+377' => 'Monaco',
            '+976' => 'Mongolia',
            '+382' => 'Montenegro',
            '+1664' => 'Montserrat',
            '+212' => 'Morocco / Western Sahara',
            '+258' => 'Mozambique',
            '+95' => 'Myanmar',
            '+264' => 'Namibia',
            '+674' => 'Nauru',
            '+977' => 'Nepal',
            '+31' => 'Netherlands',
            '+687' => 'New Caledonia',
            '+64' => 'New Zealand / Pitcairn Islands',
            '+505' => 'Nicaragua',
            '+227' => 'Niger',
            '+234' => 'Nigeria',
            '+683' => 'Niue',
            '+850' => 'North Korea',
            '+389' => 'North Macedonia',
            '+1670' => 'Northern Mariana Islands',
            '+47' => 'Norway / Svalbard and Jan Mayen',
            '+968' => 'Oman',
            '+92' => 'Pakistan',
            '+680' => 'Palau',
            '+970' => 'Palestine',
            '+507' => 'Panama',
            '+675' => 'Papua New Guinea',
            '+595' => 'Paraguay',
            '+51' => 'Peru',
            '+63' => 'Philippines',
            '+48' => 'Poland',
            '+351' => 'Portugal',
            '+1787' => 'Puerto Rico',
            '+1939' => 'Puerto Rico',
            '+974' => 'Qatar',
            '+262' => 'Reunion / French Southern Territories',
            '+40' => 'Romania',
            '+250' => 'Rwanda',
            '+290' => 'Saint Helena / Tristan da Cunha',
            '+1869' => 'Saint Kitts and Nevis',
            '+1758' => 'Saint Lucia',
            '+508' => 'Saint Pierre and Miquelon',
            '+1784' => 'Saint Vincent and the Grenadines',
            '+685' => 'Samoa',
            '+378' => 'San Marino',
            '+239' => 'Sao Tome and Principe',
            '+966' => 'Saudi Arabia',
            '+221' => 'Senegal',
            '+381' => 'Serbia',
            '+248' => 'Seychelles',
            '+232' => 'Sierra Leone',
            '+65' => 'Singapore',
            '+1721' => 'Sint Maarten',
            '+421' => 'Slovakia',
            '+386' => 'Slovenia',
            '+677' => 'Solomon Islands',
            '+252' => 'Somalia',
            '+27' => 'South Africa',
            '+82' => 'South Korea',
            '+211' => 'South Sudan',
            '+34' => 'Spain',
            '+94' => 'Sri Lanka',
            '+249' => 'Sudan',
            '+597' => 'Suriname',
            '+46' => 'Sweden',
            '+41' => 'Switzerland',
            '+963' => 'Syria',
            '+886' => 'Taiwan',
            '+992' => 'Tajikistan',
            '+255' => 'Tanzania',
            '+66' => 'Thailand',
            '+670' => 'Timor-Leste',
            '+228' => 'Togo',
            '+690' => 'Tokelau',
            '+676' => 'Tonga',
            '+1868' => 'Trinidad and Tobago',
            '+216' => 'Tunisia',
            '+90' => 'Turkey',
            '+993' => 'Turkmenistan',
            '+1649' => 'Turks and Caicos Islands',
            '+688' => 'Tuvalu',
            '+1340' => 'US Virgin Islands',
            '+256' => 'Uganda',
            '+380' => 'Ukraine',
            '+971' => 'United Arab Emirates',
            '+598' => 'Uruguay',
            '+998' => 'Uzbekistan',
            '+678' => 'Vanuatu',
            '+58' => 'Venezuela',
            '+84' => 'Vietnam',
            '+681' => 'Wallis and Futuna',
            '+967' => 'Yemen',
            '+260' => 'Zambia',
            '+263' => 'Zimbabwe',
        ];
    }

    public static function normalize(?string $countryCode, ?string $number): ?string
    {
        $number = trim((string) $number);
        if ($number === '') {
            return null;
        }

        $countryCode = self::normalizeCountryCode($countryCode);
        $digits = preg_replace('/\D+/', '', $number) ?: '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($number, '+')) {
            return '+' . ltrim($digits, '0');
        }

        $countryDigits = ltrim($countryCode, '+');
        if ($countryDigits !== '' && str_starts_with($digits, $countryDigits) && strlen($digits) > strlen($countryDigits) + 5) {
            return '+' . $digits;
        }

        return $countryCode . ltrim($digits, '0');
    }

    public static function split(?string $phone, string $fallbackCountryCode = '+234'): array
    {
        $phone = trim((string) $phone);
        $fallbackCountryCode = self::normalizeCountryCode($fallbackCountryCode);

        if ($phone === '') {
            return [$fallbackCountryCode, ''];
        }

        $digits = preg_replace('/\D+/', '', $phone) ?: '';
        foreach (array_keys(self::countries()) as $code) {
            $countryDigits = ltrim($code, '+');
            if (str_starts_with($digits, $countryDigits)) {
                return [$code, substr($digits, strlen($countryDigits))];
            }
        }

        return [$fallbackCountryCode, ltrim($digits, '0')];
    }

    public static function normalizeCountryCode(?string $countryCode): string
    {
        $digits = preg_replace('/\D+/', '', (string) $countryCode) ?: '234';

        return '+' . $digits;
    }

    private static function countryIsoMap(): array
    {
        return [
            '+93'=>'AF','+355'=>'AL','+213'=>'DZ','+1684'=>'AS','+376'=>'AD','+244'=>'AO','+1264'=>'AI','+672'=>'AQ','+1268'=>'AG','+54'=>'AR','+374'=>'AM','+297'=>'AW','+61'=>'AU','+43'=>'AT','+994'=>'AZ',
            '+1242'=>'BS','+973'=>'BH','+880'=>'BD','+1246'=>'BB','+375'=>'BY','+32'=>'BE','+501'=>'BZ','+229'=>'BJ','+1441'=>'BM','+975'=>'BT','+591'=>'BO','+387'=>'BA','+267'=>'BW','+55'=>'BR','+246'=>'IO','+1284'=>'VG','+673'=>'BN','+359'=>'BG','+226'=>'BF','+257'=>'BI',
            '+855'=>'KH','+237'=>'CM','+1'=>'US','+238'=>'CV','+1345'=>'KY','+236'=>'CF','+235'=>'TD','+56'=>'CL','+86'=>'CN','+57'=>'CO','+269'=>'KM','+242'=>'CG','+243'=>'CD','+682'=>'CK','+506'=>'CR','+225'=>'CI','+385'=>'HR','+53'=>'CU','+599'=>'CW','+357'=>'CY','+420'=>'CZ',
            '+45'=>'DK','+253'=>'DJ','+1767'=>'DM','+1809'=>'DO','+1829'=>'DO','+1849'=>'DO','+593'=>'EC','+20'=>'EG','+503'=>'SV','+240'=>'GQ','+291'=>'ER','+372'=>'EE','+268'=>'SZ','+251'=>'ET',
            '+500'=>'FK','+298'=>'FO','+679'=>'FJ','+358'=>'FI','+33'=>'FR','+594'=>'GF','+689'=>'PF','+241'=>'GA','+220'=>'GM','+995'=>'GE','+49'=>'DE','+233'=>'GH','+350'=>'GI','+30'=>'GR','+299'=>'GL','+1473'=>'GD','+590'=>'GP','+1671'=>'GU','+502'=>'GT','+44'=>'GB','+224'=>'GN','+245'=>'GW','+592'=>'GY',
            '+509'=>'HT','+504'=>'HN','+852'=>'HK','+36'=>'HU','+354'=>'IS','+91'=>'IN','+62'=>'ID','+98'=>'IR','+964'=>'IQ','+353'=>'IE','+972'=>'IL','+39'=>'IT','+1876'=>'JM','+81'=>'JP','+962'=>'JO',
            '+7'=>'RU','+254'=>'KE','+686'=>'KI','+383'=>'XK','+965'=>'KW','+996'=>'KG','+856'=>'LA','+371'=>'LV','+961'=>'LB','+266'=>'LS','+231'=>'LR','+218'=>'LY','+423'=>'LI','+370'=>'LT','+352'=>'LU',
            '+853'=>'MO','+261'=>'MG','+265'=>'MW','+60'=>'MY','+960'=>'MV','+223'=>'ML','+356'=>'MT','+692'=>'MH','+596'=>'MQ','+222'=>'MR','+230'=>'MU','+52'=>'MX','+691'=>'FM','+373'=>'MD','+377'=>'MC','+976'=>'MN','+382'=>'ME','+1664'=>'MS','+212'=>'MA','+258'=>'MZ','+95'=>'MM',
            '+264'=>'NA','+674'=>'NR','+977'=>'NP','+31'=>'NL','+687'=>'NC','+64'=>'NZ','+505'=>'NI','+227'=>'NE','+234'=>'NG','+683'=>'NU','+850'=>'KP','+389'=>'MK','+1670'=>'MP','+47'=>'NO','+968'=>'OM',
            '+92'=>'PK','+680'=>'PW','+970'=>'PS','+507'=>'PA','+675'=>'PG','+595'=>'PY','+51'=>'PE','+63'=>'PH','+48'=>'PL','+351'=>'PT','+1787'=>'PR','+1939'=>'PR','+974'=>'QA','+262'=>'RE','+40'=>'RO','+250'=>'RW',
            '+290'=>'SH','+1869'=>'KN','+1758'=>'LC','+508'=>'PM','+1784'=>'VC','+685'=>'WS','+378'=>'SM','+239'=>'ST','+966'=>'SA','+221'=>'SN','+381'=>'RS','+248'=>'SC','+232'=>'SL','+65'=>'SG','+1721'=>'SX','+421'=>'SK','+386'=>'SI','+677'=>'SB','+252'=>'SO','+27'=>'ZA','+82'=>'KR','+211'=>'SS','+34'=>'ES','+94'=>'LK','+249'=>'SD','+597'=>'SR','+46'=>'SE','+41'=>'CH','+963'=>'SY',
            '+886'=>'TW','+992'=>'TJ','+255'=>'TZ','+66'=>'TH','+670'=>'TL','+228'=>'TG','+690'=>'TK','+676'=>'TO','+1868'=>'TT','+216'=>'TN','+90'=>'TR','+993'=>'TM','+1649'=>'TC','+688'=>'TV','+1340'=>'VI','+256'=>'UG','+380'=>'UA','+971'=>'AE','+598'=>'UY','+998'=>'UZ',
            '+678'=>'VU','+58'=>'VE','+84'=>'VN','+681'=>'WF','+967'=>'YE','+260'=>'ZM','+263'=>'ZW',
        ];
    }
}
