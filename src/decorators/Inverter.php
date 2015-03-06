<?php
/**
 * The Inverter decorator inverts the result of the child, returning `SUCCESS`
 * for `FAILURE` and `FAILURE` for `SUCCESS`.
 *
 * @class Inverter
 * @extends Decorator
 **/
namespace Behavior3php\Decorators;

use Behavior3php\Core\Decorator;
use  Behavior3php\B3;

class Inverter extends Decorator {
    /**
     * Node name. Default to `Inverter`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Inverter';

    /**
     * Tick method.
     *
     * @method tick
     * @param {Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     **/
    public function tick($tick) {
        if (!$this->child) {
            return B3::$ERROR;
        }

        $status = $this->child->_execute($tick);
        if ($status == B3::$SUCCESS)
            $status = B3::$FAILURE;
        else if ($status == B3::$FAILURE)
            $status = B3::$SUCCESS;

        return $status;
    }
}