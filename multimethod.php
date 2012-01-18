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
function multimethod($dispatch = null) {
    return new MultiMethod($dispatch);
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
    /**
     * Registered methods
     * [matchValue, implementation]
     * This format is necessary for multiple paramenters
     */
    protected $methods = array();
    /**
     * Fallback when no method is registered
     */
    protected $defaultFunction;

    public function __construct($dispatch) {
        if ($dispatch) {
            $this->dispatch = $dispatch;
        } else {
            $this->dispatch(function($a) { return $a; });
        }

        $this->defaultFunction = function() { return null; };
    }

    /**
     * Our array of method is like this:
     * array(0 => array(matchValue, function));
     * This function looks for the index of the correct matchValue
     */
    protected function indexOf($value, $methods) {
        foreach ($methods as $key => $method) {
            $matches = $method[0];
            if ($matches === $value) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Just a helper that returns the value or the function return
     * Used mainly for the checks used on _invoke to call the _default or method
     */
    protected function evaluate($subject, $args) {
        if ($subject instanceof Closure) {
            $args[] = $this;
            return call_user_func_array($subject, $args);
        }

        return $subject;
    }

    public function dispatch($function) {
        $this->dispatch = $function;
        return $this;
    }

    /**
     * Defines a new method, or overrides it
     */
    public function when($matchValue, $func) {
        $index = $this->indexOf($matchValue, $this->methods);
        if ($index !== false) {
            $this->methods[$index] = array($matchValue, $func);
        } else {
            $this->methods[] = array($matchValue, $func);
        }
        return $this;
    }

    public function remove($matchValue) {
        $index = $this->indexOf($matchValue, $this->methods);
        if ($index !== false) {
            unset($this->methods[$index]);
        }
        return $this;
    }

    public function _default($default) {
        $this->defaultFunction = $default;
        return $this;
    }

    public function __invoke() {
        $arguments = func_get_args();

        if ($this->dispatch instanceof Closure) {
            $dispatch = $this->dispatch;
        } elseif (is_string($this->dispatch)) {
            $key = $this->dispatch;
            $dispatch = function($subject) use ($key) {
                if (is_array($subject)) {
                    return $subject[$key];
                } else {
                    return $subject->$key;
                }
            };
        }
        $matchValue = call_user_func_array($dispatch, $arguments);

        $method = $this->defaultFunction;
        $index = $this->indexOf($matchValue, $this->methods);
        if ($index !== false) {
            $method = $this->methods[$index][1];
        }

        return $this->evaluate($method, $arguments);
    }
}