<?php
/**
 * RepeatUntilSuccess is a decorator that repeats the tick signal until the
 * node child returns `SUCCESS`, `RUNNING` or `ERROR`. Optionally, a maximum
 * number of repetitions can be defined.
 *
 * @class RepeatUntilSuccess
 * @extends Decorator
 **/
namespace Behavior3php\Decorators;

use Behavior3php\Core\Decorator;
use Behavior3php\B3;

class RepeatUntilSuccess extends Decorator {
    /**
     * Node name. Default to `RepeatUntilSuccess`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'RepeatUntilSuccess';

    /**
     * Node title. Default to `Repeat Until Success`.
     *
     * @property title
     * @type {String}
     * @readonly
     **/
    public $title = 'Repeat Until Success';

    /**
     * Node parameters.
     *
     * @property parameters
     * @type {String}
     * @readonly
     **/
    public $parameters = array('maxLoop'=> -1);



    /**
     * Initialization method.
     *
     * Settings parameters:
     *
     * - **maxLoop** (*Integer*) Maximum number of repetitions. Default to -1
     *                           (infinite).
     * - **child** (*BaseNode*) The child node.
     *
     * @method initialize
     * @param {Object} settings Object with parameters.
     * @constructor
     **/
    public function initialize ($settings=array()) {

        parent::initialize($settings);

        $this->maxLoop = $settings['maxLoop'] ?: -1;
    }

    /**
     * Open method.
     *
     * @method open
     * @param {Tick} tick A tick instance.
     **/
    public function open($tick) {
        $tick->blackboard->set('i', 0, $tick->tree->id, $this->id);
    }

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

        $i = $tick->blackboard->get('i', $tick->tree->id, $this->id);
        while ($this->maxLoop < 0 || $i < $this->maxLoop) {
            $status = $this->child->_execute($tick);
            if ($status == B3::$FAILURE)
                $i++;
            else
                break;
        }

        $i = $tick->blackboard->set('i', $i, $tick->tree->id, $this->id);
        return $status;
    }
}