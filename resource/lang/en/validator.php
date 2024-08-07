<?php

return [
    'errors' => [
        'required'  => 'is required.',
        'email'     => 'must be a acceptable E-mail adress.',
        'type'      => 'your type is {now-type} but here is only accept {must-type}.',
        'max'       => 'your value is {now-val} but you must max submit {max-val} value.',
        'min'       => 'your value is {now-val} but you must min submit {min-val} value.',
        'same'      => 'value is not match {attribute-name}',
        'unique'    => 'already using.',
        'exists'    => 'that\'s not exists.',
    ],

    'attributes' => [
        'username'    => 'Username',
        'password'    => 'Password',
        're-password' => 'Repeat Password',
        'email'       => 'E-mail',
        'terms'       => 'User Agreement Policy'
    ]
];
