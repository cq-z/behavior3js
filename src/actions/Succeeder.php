<?php
/**
 * This action node returns `SUCCESS` always.
 *
 * @class Succeeder
 * @extends Action
 **/
namespace Behavior3php\Actions;

use  Behavior3php\Core\Action;
use  Behavior3php\B3;


class Succeeder extends Action {


    /**
     * Node name. Default to `Succeeder`.
     *
     * @property name
     * @type String
     * @readonly
     **/
    public $name = 'Succeeder';

    /**
     * Tick method.
     *
     * @method tick
     * @param {b3.Tick} tick A tick instance.
     * @returns {Constant} Always return `b3.SUCCESS`.
     **/
    public function tick ($tick) {
        return B3::$SUCCESS;
    }


}