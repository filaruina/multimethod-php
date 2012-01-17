# multimethod.php

Multimethods are a functional programming control structure that allow you
to dynamically build-up and manipulate the dispatching behavior of a 
polymorphic function. Inspired by [multimethod.js](https://github.com/KrisJordan/multimethod-js) (description taken from him).

# Main differences between multimethod.js and multimethod.php

- PHP has 'default' as a reserved word. So, to define a default method you have to use _default
- Objects and Arrays are really different in PHP. I though it would be better to let the string plucking work with objects and arrays, instead of just objects. (this doesn't make sense for JS)

# about the port

Basically the process of making the port consisted of copying the unit tests and implementing the code based on Kris Jordan multimethod-js.
Most of the features are currently replicated but more tests are needed.

# TODO

- undefined property (Test exists)