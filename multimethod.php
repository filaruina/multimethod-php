<?php

/**
* multimethod.php 0.1
* Copyright (c) 2012 Filipe La Ruina
* multimethod.php inspired by multimethod.js by Kris Jordan
* multimethod.php is freely distributed under the MIT License
*/

/**
 * Returns an instance of multiMethod class for OO use
 */
function multimethod() {
    return new MultiMethod();
}

/**
 * The actual multimethod code
 */
class MultiMethod {
    /**
     * Dispatch function used or the string containing the 
     * property name to be used as a dispatch criteria
     */
    protected $dispatch;
    protected $methods;
    protected $default;

    function __construct($dispatch = null) {
        $this->dispatch = $dispatch;
    }
}