<?php
/**
 * MemSequence is similar to Sequence node, but when a child returns a
 * `RUNNING` state, its index is recorded and in the next tick the MemPriority
 * call the child recorded directly, without calling previous children again.
 *
 * @class MemPriority
 * @extends Composite
 **/
namespace Behavior3php\Composites;

use  Behavior3php\Core\Composite;
use  Behavior3php\B3;

class MemSequence extends Composite{
    /**
     * Node name. Default to `MemSequence`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'MemSequence';

    /**
     * Open method.
     *
     * @method open
     * @param {b3.Tick} tick A tick instance.
     **/
    public function open ($tick) {
        $tick->blackboard->set('runningChild', 0, $tick->tree->id, $this->id);
    }

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     **/
    public function tick($tick) {
        $child = $tick->blackboard->get('runningChild', $tick->tree->id, $this->id);
        foreach(array_slice($this->children,$child+1) as $i=>$status) {
            $status = $status->_execute($tick);

            if ($status !== B3::$SUCCESS) {
                if ($status === B3::$RUNNING) {
                    $tick->blackboard->set('runningChild', $i, $tick->tree->id, $this->id);
                }
                return $status;
            }
        }

        return B3::$SUCCESS;
    }
}