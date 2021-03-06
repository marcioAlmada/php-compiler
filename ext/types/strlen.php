<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\ext\types;

use PHPCompiler\Func\Internal\JITInlined;
use PHPCompiler\Frame;
use PHPCompiler\JIT;
use PHPCompiler\JIT\Func as JITFunc;

class strlen extends JITInlined {

    public function execute(Frame $frame): void {
        if (count($frame->calledArgs) !== 1) {
            throw new \LogicException("Expecting exactly a single argument to strlen()");
        }
        $var = $frame->calledArgs[0];
        if (!is_null($frame->returnVar)) {
            $frame->returnVar->int(strlen($var->toString()));
        }
    }

    public function call(\gcc_jit_rvalue_ptr ... $args): \gcc_jit_rvalue_ptr {
        if (count($args) !== 1) {
            throw new \LogicException('Too few args passed to strlen()');
        }
        $type = $this->jit->context->getStringFromType(\gcc_jit_rvalue_get_type($args[0]));
        switch ($type) {
            case '__string__*':
                return $this->jit->context->helper->call('__string__strlen', $args[0]);
            default:
                throw new \LogicException('Non-implemented type handled: ' . $type);
        }
    }

}