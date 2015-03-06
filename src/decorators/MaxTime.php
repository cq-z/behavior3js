<?php
/**
 * The MaxTime decorator limits the maximum time the node child can execute.
 * Notice that it does not interrupt the execution itself (i.e., the child must
 * be non-preemptive), it only interrupts the node after a `RUNNING` status.
 *
 * @class MaxTime
 * @extends Decorator
 **/
namespace Behavior3php\Decorators;

use Behavior3php\Core\Decorator;
use Behavior3php\B3;

class MaxTime extends Decorator{
    /**
     * Node name. Default to `MaxTime`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'MaxTime';

    /**
     * Node title. Default to `Max Time`.
     *
     * @property title
     * @type {String}
     * @readonly
     **/
    public $title = 'Max Time';

    /**
     * Node parameters.
     *
     * @property parameters
     * @type {String}
     * @readonly
     **/
    public $parameters = array('maxTime'=> null);


    /**
     * Initialization method.
     *
     * Settings parameters:
     *
     * - **maxTime** (*Integer*) Maximum time a child can execute.
     * - **child** (*BaseNode*) The child node.
     *
     * @method initialize
     * @param {Object} settings Object with parameters.
     * @constructor
     **/
    public function initialize ($settings=array()) {

        parent::initialize($settings);
        if (!$settings['maxTime']) {
            throw new \Exception( "maxTime parameter in MaxTime decorator is an obligatory " .
                "parameter");
        }

        $this->maxTime = $settings['maxTime'];
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
    public function tick ($tick) {
        if (!$this->child) {
            return B3::$ERROR;
        }

        $currTime = time();
        $startTime = $tick->blackboard->get('startTime', $tick->tree->id, $this->id);

        $status = $this->child->_execute($tick);
        if ($currTime - $startTime > $this->maxTime) {
            return B3::$FAILURE;
        }

        return $status;
    }
}