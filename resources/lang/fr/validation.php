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

    'accepted'             => ':attribute doit être accèpté.',
    'active_url'           => ':attribute n\'est pas une URL valide.',
    'after'                => ':attribute doit être une date après :date.',
    'after_or_equal'       => ':attribute doit être une date après ou égale à :date.',
    'alpha'                => ':attribute ne peut contenir que des lettres.',
    'alpha_dash'           => ':attribute ne peut contenir que des lettres, chiffres, tirets et underscores.',
    'alpha_num'            => ':attribute ne peut contenir que des lettres ou chiffres.',
    'array'                => ':attribute doit être une liste.',
    'before'               => ':attribute doit être une date avant :date.',
    'before_or_equal'      => ':attribute doit être une date avant ou éegale à :date.',
    'between'              => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file'    => ':attribute doit être entre :min et :max kilobytes.',
        'string'  => ':attribute doit être entre :min et :max charactères.',
        'array'   => ':attribute doit contenir entre :min it :max éléments.',
    ],
    'boolean'              => ':attribute doit être vrai ou faux.',
    'confirmed'            => ':attribute ne correspond pas a la confirmation.',
    'date'                 => ':attribute n\'est pas une date valide.',
    'date_format'          => ':attribute ne correspond pas au format :format.',
    'different'            => ':attribute et :other doivent être differents.',
    'digits'               => ':attribute doit avoir une longueur :digits chiffres.',
    'digits_between'       => ':attribute doit avoir une longueur entre :min et :max chiffres.',
    'dimensions'           => ':attribute n\' pas des dimensions d\'image valide.',
    'distinct'             => ':attribute a une valeur dupliquée.',
    'email'                => ':attribute doit êter une adresse de courriel valide.',
    'exists'               => ':attribute sélectionné est invalide.',
    'file'                 => ':attribute doit être un fichier.',
    'filled'               => ':attribute doit avoir une valeur.',
    'gt'                   => [
        'numeric' => ':attribute doit être plus grand que :value.',
        'file'    => ':attribute doit être plus grand que :value kilobytes.',
        'string'  => ':attribute doit être plus grand que :value charactères.',
        'array'   => ':attribute doit contenir plus de :value éléments.',
    ],
    'gte'                  => [
        'numeric' => ':attribute doit être plus grand que ou égal à :value.',
        'file'    => ':attribute doit être plus grand que ou égal à :value kilobytes.',
        'string'  => ':attribute doit être plus grand que ou égal à :value charactères.',
        'array'   => ':attribute doit contenir plus de ou être égal à :value éléments.',
    ],
    'image'                => ':attribute doit être une image.',
    'in'                   => ':attribute sélectionné est invalide.',
    'in_array'             => ':attribute n\'existe pas dans :other.',
    'integer'              => ':attribute doit être un nombre entier.',
    'ip'                   => ':attribute doit être une adresse IP valide.',
    'ipv4'                 => ':attribute doit être une adresse IPv4 valide.',
    'ipv6'                 => ':attribute doit être une adresse IPv6 valide.',
    'json'                 => ':attribute doit être une string JSON valide.',
    'lt'                   => [
        'numeric' => ':attribute doit être plus petit que :value.',
        'file'    => ':attribute doit être plus petit que :value kilobytes.',
        'string'  => ':attribute doit être plus petit que :value charactères.',
        'array'   => ':attribute doit contenir moins de :value éléments.',
    ],
    'lte'                  => [
        'numeric' => ':attribute doit être plus petit que ou égal à :value.',
        'file'    => ':attribute doit être plus petit que ou égal à :value kilobytes.',
        'string'  => ':attribute doit être plus petit que ou égal à :value charactères.',
        'array'   => ':attribute doit contenir moins de ou être égal à :value éléments.',
    ],
    'max'                  => [
        'numeric' => ':attribute ne doit pas être plus grand que :max.',
        'file'    => ':attribute ne doit pas être plus grand que :max kilobytes.',
        'string'  => ':attribute ne doit pas être plus grand que :max charactères.',
        'array'   => ':attribute ne doit pas contenir plus de :value éléments.',
    ],
    'mimes'                => ':attribute doit être un fichier de type: :values.',
    'mimetypes'            => ':attribute doit être un fichier de type: :values.',
    'min'                  => [
        'numeric' => ':attribute doit être au moins :min.',
        'file'    => ':attribute doit être au moins :min kilobytes.',
        'string'  => ':attribute doit être au moins :min characters.',
        'array'   => ':attribute doit avoir au moins :min éléments.',
    ],
    'not_in'               => ':attribute séléctionné est invalide.',
    'not_regex'            => ':attribute a un format invalide.',
    'numeric'              => ':attribute doit être un nombre.',
    'present'              => ':attribute doit être présent.',
    'regex'                => ':attribute a un format invalide.',
    'required'             => ':attribute est requis.',
    'required_if'          => ':attribute est requis quand :other a la valeur :value.',
    'required_unless'      => ':attribute est requis sauf si :other est dans :values.',
    'required_with'        => ':attribute est requis quand :values est présent.',
    'required_with_all'    => ':attribute est requis quand :values sont présents.',
    'required_without'     => ':attribute est requis quand :values n\'est pas présent.',
    'required_without_all' => ':attribute est requis quand aucun :values n\'est présent.',
    'same'                 => ':attribute et :other doivent correspondre.',
    'size'                 => [
        'numeric' => ':attribute doit avoir une taille de :size.',
        'file'    => ':attribute doit avoir une taille de :size kilobytes.',
        'string'  => ':attribute doit avoir une taille de :size characters.',
        'array'   => ':attribute doit contenir un nombre :size d\'éléments.',
    ],
    'string'               => ':attribute doit être une chaîne de charactères.',
    'timezone'             => ':attribute doit être un fuseau horaire valide.',
    'unique'               => ':attribute est déjà pris.',
    'uploaded'             => ':attribute n\'a pas été téléchargé correctement.',
    'url'                  => ':attribute a un format invalid.',

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
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
