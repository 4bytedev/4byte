<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => ':attribute alanı kabul edilmelidir.',
    'accepted_if'     => ':other, :value olduğunda :attribute alanı kabul edilmelidir.',
    'active_url'      => ':attribute alanı geçerli bir URL olmalıdır.',
    'after'           => ':attribute alanı :date tarihinden sonra olmalıdır.',
    'after_or_equal'  => ':attribute alanı :date tarihinden sonra veya eşit olmalıdır.',
    'alpha'           => ':attribute alanı yalnızca harflerden oluşmalıdır.',
    'alpha_dash'      => ':attribute alanı yalnızca harf, rakam, tire ve alt çizgi içerebilir.',
    'alpha_num'       => ':attribute alanı yalnızca harf ve rakamlardan oluşmalıdır.',
    'any_of'          => ':attribute alanı geçersiz.',
    'array'           => ':attribute alanı bir dizi (array) olmalıdır.',
    'ascii'           => ':attribute alanı yalnızca tek baytlık alfanümerik karakterler ve semboller içermelidir.',
    'before'          => ':attribute alanı :date tarihinden önce olmalıdır.',
    'before_or_equal' => ':attribute alanı :date tarihinden önce veya eşit olmalıdır.',
    'between'         => [
        'array'   => ':attribute alanı :min ile :max arasında öğe içermelidir.',
        'file'    => ':attribute alanı :min ile :max kilobayt arasında olmalıdır.',
        'numeric' => ':attribute alanı :min ile :max arasında olmalıdır.',
        'string'  => ':attribute alanı :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean'           => ':attribute alanı doğru ya da yanlış olmalıdır.',
    'can'               => ':attribute alanı yetkisiz bir değer içeriyor.',
    'confirmed'         => ':attribute doğrulaması eşleşmiyor.',
    'contains'          => ':attribute alanında gerekli bir değer eksik.',
    'current_password'  => 'Şifre hatalı.',
    'date'              => ':attribute alanı geçerli bir tarih olmalıdır.',
    'date_equals'       => ':attribute alanı :date ile aynı tarih olmalıdır.',
    'date_format'       => ':attribute alanı :format formatı ile eşleşmelidir.',
    'decimal'           => ':attribute alanı :decimal ondalık basamak içermelidir.',
    'declined'          => ':attribute alanı reddedilmelidir.',
    'declined_if'       => ':other, :value olduğunda :attribute alanı reddedilmelidir.',
    'different'         => ':attribute ve :other farklı olmalıdır.',
    'digits'            => ':attribute alanı :digits basamaklı olmalıdır.',
    'digits_between'    => ':attribute alanı :min ile :max basamak arasında olmalıdır.',
    'dimensions'        => ':attribute alanı geçersiz resim boyutlarına sahip.',
    'distinct'          => ':attribute alanı yinelenen bir değer içeriyor.',
    'doesnt_contain'    => ':attribute alanı şu değerlerden herhangi birini içermemelidir: :values.',
    'doesnt_end_with'   => ':attribute alanı şu değerlerden biriyle bitmemelidir: :values.',
    'doesnt_start_with' => ':attribute alanı şu değerlerden biriyle başlamamalıdır: :values.',
    'email'             => ':attribute alanı geçerli bir e-posta adresi olmalıdır.',
    'ends_with'         => ':attribute alanı şu değerlerden biriyle bitmelidir: :values.',
    'enum'              => 'Seçilen :attribute geçersiz.',
    'exists'            => 'Seçilen :attribute geçersiz.',
    'extensions'        => ':attribute alanı şu uzantılardan birine sahip olmalıdır: :values.',
    'file'              => ':attribute alanı bir dosya olmalıdır.',
    'filled'            => ':attribute alanı doldurulmalıdır.',
    'gt'                => [
        'array'   => ':attribute alanı :value öğeden fazla olmalıdır.',
        'file'    => ':attribute alanı :value kilobayttan büyük olmalıdır.',
        'numeric' => ':attribute alanı :value değerinden büyük olmalıdır.',
        'string'  => ':attribute alanı :value karakterden uzun olmalıdır.',
    ],
    'gte' => [
        'array'   => ':attribute alanı en az :value öğe içermelidir.',
        'file'    => ':attribute alanı :value kilobayt veya daha fazla olmalıdır.',
        'numeric' => ':attribute alanı :value veya daha büyük olmalıdır.',
        'string'  => ':attribute alanı :value karakter veya daha fazla olmalıdır.',
    ],
    'hex_color'     => ':attribute alanı geçerli bir onaltılık renk kodu olmalıdır.',
    'image'         => ':attribute alanı bir resim olmalıdır.',
    'in'            => 'Seçilen :attribute geçersiz.',
    'in_array'      => ':attribute alanı :other içinde bulunmalıdır.',
    'in_array_keys' => ':attribute alanı şu anahtarlardan en az birini içermelidir: :values.',
    'integer'       => ':attribute alanı bir tam sayı olmalıdır.',
    'ip'            => ':attribute alanı geçerli bir IP adresi olmalıdır.',
    'ipv4'          => ':attribute alanı geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'          => ':attribute alanı geçerli bir IPv6 adresi olmalıdır.',
    'json'          => ':attribute alanı geçerli bir JSON dizesi olmalıdır.',
    'list'          => ':attribute alanı bir liste olmalıdır.',
    'lowercase'     => ':attribute alanı küçük harf olmalıdır.',
    'lt'            => [
        'array'   => ':attribute alanı :value öğeden az olmalıdır.',
        'file'    => ':attribute alanı :value kilobayttan küçük olmalıdır.',
        'numeric' => ':attribute alanı :value değerinden küçük olmalıdır.',
        'string'  => ':attribute alanı :value karakterden kısa olmalıdır.',
    ],
    'lte' => [
        'array'   => ':attribute alanı :value öğeden fazla olmamalıdır.',
        'file'    => ':attribute alanı :value kilobayt veya daha az olmalıdır.',
        'numeric' => ':attribute alanı :value veya daha küçük olmalıdır.',
        'string'  => ':attribute alanı :value karakter veya daha az olmalıdır.',
    ],
    'mac_address' => ':attribute alanı geçerli bir MAC adresi olmalıdır.',
    'max'         => [
        'array'   => ':attribute alanı en fazla :max öğe içerebilir.',
        'file'    => ':attribute alanı :max kilobayttan büyük olmamalıdır.',
        'numeric' => ':attribute alanı :max değerinden büyük olmamalıdır.',
        'string'  => ':attribute alanı en fazla :max karakter olmalıdır.',
    ],
    'max_digits' => ':attribute alanı en fazla :max basamak içerebilir.',
    'mimes'      => ':attribute alanı şu dosya türlerinden biri olmalıdır: :values.',
    'mimetypes'  => ':attribute alanı şu dosya türlerinden biri olmalıdır: :values.',
    'min'        => [
        'array'   => ':attribute alanı en az :min öğe içermelidir.',
        'file'    => ':attribute alanı en az :min kilobayt olmalıdır.',
        'numeric' => ':attribute alanı en az :min olmalıdır.',
        'string'  => ':attribute alanı en az :min karakter olmalıdır.',
    ],
    'min_digits'       => ':attribute alanı en az :min basamak içermelidir.',
    'missing'          => ':attribute alanı eksik olmalıdır.',
    'missing_if'       => ':other, :value olduğunda :attribute alanı eksik olmalıdır.',
    'missing_unless'   => ':other, :value değilse :attribute alanı eksik olmalıdır.',
    'missing_with'     => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
    'missing_with_all' => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
    'multiple_of'      => ':attribute alanı :value katı olmalıdır.',
    'not_in'           => 'Seçilen :attribute geçersiz.',
    'not_regex'        => ':attribute formatı geçersiz.',
    'numeric'          => ':attribute alanı bir sayı olmalıdır.',
    'password'         => [
        'letters'       => ':attribute alanı en az bir harf içermelidir.',
        'mixed'         => ':attribute alanı en az bir büyük ve bir küçük harf içermelidir.',
        'numbers'       => ':attribute alanı en az bir rakam içermelidir.',
        'symbols'       => ':attribute alanı en az bir sembol içermelidir.',
        'uncompromised' => 'Verilen :attribute bir veri sızıntısında bulundu. Lütfen farklı bir :attribute seçin.',
    ],
    'present'                => ':attribute alanı mevcut olmalıdır.',
    'present_if'             => ':other, :value olduğunda :attribute alanı mevcut olmalıdır.',
    'present_unless'         => ':other, :value değilse :attribute alanı mevcut olmalıdır.',
    'present_with'           => ':values mevcut olduğunda :attribute alanı mevcut olmalıdır.',
    'present_with_all'       => ':values mevcut olduğunda :attribute alanı mevcut olmalıdır.',
    'prohibited'             => ':attribute alanı yasaktır.',
    'prohibited_if'          => ':other, :value olduğunda :attribute alanı yasaktır.',
    'prohibited_if_accepted' => ':other kabul edildiğinde :attribute alanı yasaktır.',
    'prohibited_if_declined' => ':other reddedildiğinde :attribute alanı yasaktır.',
    'prohibited_unless'      => ':other, :values içinde değilse :attribute alanı yasaktır.',
    'prohibits'              => ':attribute alanı :other alanının var olmasını engelliyor.',
    'regex'                  => ':attribute formatı geçersiz.',
    'required'               => ':attribute alanı zorunludur.',
    'required_array_keys'    => ':attribute alanı şu anahtarları içermelidir: :values.',
    'required_if'            => ':other, :value olduğunda :attribute alanı zorunludur.',
    'required_if_accepted'   => ':other kabul edildiğinde :attribute alanı zorunludur.',
    'required_if_declined'   => ':other reddedildiğinde :attribute alanı zorunludur.',
    'required_unless'        => ':other, :values içinde değilse :attribute alanı zorunludur.',
    'required_with'          => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_with_all'      => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_without'       => ':values mevcut değilken :attribute alanı zorunludur.',
    'required_without_all'   => ':values hiçbiri mevcut değilken :attribute alanı zorunludur.',
    'same'                   => ':attribute alanı :other ile eşleşmelidir.',
    'size'                   => [
        'array'   => ':attribute alanı :size öğe içermelidir.',
        'file'    => ':attribute alanı :size kilobayt olmalıdır.',
        'numeric' => ':attribute alanı :size olmalıdır.',
        'string'  => ':attribute alanı :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute alanı şu değerlerden biriyle başlamalıdır: :values.',
    'string'      => ':attribute alanı bir metin olmalıdır.',
    'timezone'    => ':attribute alanı geçerli bir zaman dilimi olmalıdır.',
    'unique'      => ':attribute zaten alınmış.',
    'uploaded'    => ':attribute yüklenemedi.',
    'uppercase'   => ':attribute alanı büyük harf olmalıdır.',
    'url'         => ':attribute alanı geçerli bir URL olmalıdır.',
    'ulid'        => ':attribute alanı geçerli bir ULID olmalıdır.',
    'uuid'        => ':attribute alanı geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'özel-mesaj',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
