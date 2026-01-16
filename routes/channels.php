<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('vendeur.{id_vendeur}', function ($user, $id_vendeur) {
    return $user->vendeur && (int) $user->vendeur->id_vendeur === (int) $id_vendeur;
});
