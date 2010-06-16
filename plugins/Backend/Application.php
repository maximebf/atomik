<?php

	Atomik_Backend::bootstrap();
	Atomik_Backend::dispatch();
	
    // to avoid dispatching the current application
	return false;
