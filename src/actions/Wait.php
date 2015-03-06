<?php
/**
 * Wait a few seconds.
 *
 * @class Wait
 * @extends Action
 **/
namespace Behavior3php\Actions;

use  Behavior3php\Core\Action;
use  Behavior3php\B3;


class Wait extends Action {


    /**
     * Node name. Default to `Wait`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Wait';

    /**
     * Node endTime.
     *
     * @property endTime
     * @type {String}
     * @readonly
     **/
    public $endTime = 0;

    /**
     * Node parameters.
     *
     * @property parameters
     * @type {String}
     * @readonly
     **/
    public $parameters = array('milliseconds'=>0);


    /**
     * Initialization method.
     *
     * Settings parameters:
     *
     * - **milliseconds** (*Integer*) Maximum time, in milliseconds, a child
     *                                can execute.
     *
     * @method initialize
     * @param {Object} settings Object with parameters.
     * @constructor
     **/
    public function initialize ($settings=array()) {

        parent::initialize();

        $this->endTime = $settings['milliseconds'] ?: 0;
    }

    /**
     * Open method.
     *
     * @method open
     * @param {Tick} tick A tick instance.
     **/
    public function open($tick) {
        $startTime = time();
        $tick->blackboard->set('startTime', $startTime, $tick->tree->id, $this->id);
    }

    /**
     * Tick method.
     *
     * @method tick
     * @param {Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     **/
    public function tick($tick) {
        $currTime = time();
        $startTime = $tick->blackboard->get('startTime', $tick->tree->id, $this->id);

        if ($currTime - $startTime > $this->endTime) {
            return B3::$SUCCESS;
        }

        return B3::$RUNNING;
    }


}