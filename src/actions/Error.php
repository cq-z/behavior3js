<?php
/**
 * This action node returns `ERROR` always.
 *
 * @class Error
 * @extends Action
 **/
namespace Behavior3php\Actions;

use  Behavior3php\Core\Action;
use  Behavior3php\B3;

class Error extends Action {
    /**
     * Node name. Default to `Error`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Error';

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} Always return `b3.ERROR`.
     **/
    public function tick ($tick) {
        return B3::$ERROR;
    }
}