<?php
/**
 * Priority ticks its children sequentially until one of them returns
 * `SUCCESS`, `RUNNING` or `ERROR`. If all children return the failure state,
 * the priority also returns `FAILURE`.
 *
 * @class Priority
 * @extends Composite
 **/
namespace Behavior3php\Composites;

use  Behavior3php\Core\Composite;
use  Behavior3php\B3;

class Priority  extends Composite {
    /**
     * Node name. Default to `Priority`.
     *
     * @property name
     * @type String
     * @readonly
     **/
    public $name = 'Priority';

    /**
     * Tick method.
     *
     * @method tick
     * @param {Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     **/
    public function  tick($tick) {
        foreach ($this->children as $status) {
            $status = $status->_execute($tick);

            if ($status !== B3::$FAILURE) {
                return $status;
            }
        }

        return B3::$FAILURE;
    }
}