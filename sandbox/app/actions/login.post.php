<?php

if (Atomik_Auth::login($_POST['username'], $_POST['password'])) {
	Atomik::redirect(A('request/from'));
}
Atomik::flash('wrong user', 'error');