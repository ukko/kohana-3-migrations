<?php
Route::set('migrations', 'migrations(/<action>(/<id>))',
    array(
        'action'    => '(index|status|up|down)',
        'id'        => '(\d+|all)',
    ))->defaults(array(
        'controller'=> 'migrations',
        'action'    => 'index',
    ));
