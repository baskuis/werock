<?php

/** @var UserObject $UserObject */
$UserObject = CoreUser::getUser();

if($UserObject) {

    $view = '
<div class="user">
    <i class="fa fa-user"></i>
    <strong>' . $UserObject->getUsername() . '</strong>
    <a href="/do/logout" class="logout"><i class="fa fa-sign-out"></i></a>
</div>';

}