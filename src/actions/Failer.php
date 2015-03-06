<?php
/**
 * This action node returns `FAILURE` always.
 *
 * @class Failer
 * @extends Action
 **/
namespace Behavior3php\Actions;

use  Behavior3php\Core\Action;
use  Behavior3php\B3;

class Failer extends Action {
    /**
     * Node name. Default to `Failer`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Failer';

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} Always return `b3.FAILURE`.
     **/
    public function tick ($tick) {
        return B3::$FAILURE;
    }
}