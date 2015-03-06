<?php
/**
 * The Sequence node ticks its children sequentially until one of them returns
 * `FAILURE`, `RUNNING` or `ERROR`. If all children return the success state,
 * the sequence also returns `SUCCESS`.
 *
 * @class Sequence
 * @extends Composite
 **/
namespace Behavior3php\Composites;

use  Behavior3php\Core\Composite;
use  Behavior3php\B3;

class Sequence extends Composite{
    /**
     * Node name. Default to `Sequence`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Sequence';

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     **/
    public function tick ($tick) {
        foreach($this->children as $status) {
            $status = $status->_execute($tick);

            if ($status !== B3::$SUCCESS) {
                return $status;
            }
        }

        return B3::$SUCCESS;
    }
}