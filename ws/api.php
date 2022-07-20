<?php

use App\Models\User;

switch ($ws->data) {
    case 'test':
        $ws->response = ['status' => 1, 'message' =>  'Bağlanıldı!'];
        break;

    case 'user':
        $user = new User;
        $ws->response = $user->find($ws->args['id']);
        break;

    default:
        $ws->response = ['status' => 0, 'message' =>  'Unable command!'];
}
