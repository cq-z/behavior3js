<?php
/**
 * This action node returns RUNNING always.
 *
 * @class Runner
 * @extends Action
 **/
namespace Behavior3php\Actions;

use  Behavior3php\Core\Action;
use  Behavior3php\B3;

class Runner extends Action {

    /**
     * Node name. Default to `Runner`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Runner';

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} Always return `b3.RUNNING`.
     **/
    public function tick($tick) {
        return B3::$RUNNING;
    }

}