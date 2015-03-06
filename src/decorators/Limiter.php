<?php
/**
 * This decorator limit the number of times its child can be called. After a
 * certain number of times, the Limiter decorator returns `FAILURE` without
 * executing the child.
 *
 * @class Limiter
 * @extends Decorator
 **/
namespace Behavior3php\Decorators;

use Behavior3php\Core\Decorator;
use  Behavior3php\B3;

class Limiter extends Decorator {
    /**
     * Node name. Default to `Limiter`.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = 'Limiter';

    /**
     * Node parameters.
     *
     * @property parameters
     * @type {String}
     * @readonly
     **/
    public $parameters = array('maxLoop'=> null);


    /**
     * Initialization method.
     *
     * Settings parameters:
     *
     * - **maxLoop** (*Integer*) Maximum number of repetitions.
     * - **child** (*BaseNode*) The child node.
     *
     * @method initialize
     * @param {Object} settings Object with parameters.
     * @constructor
     **/
    public function initialize ($settings=array()) {

        parent::initialize($settings);

        if (!$settings['maxLoop']) {
            throw new \Exception( "maxLoop parameter in Limiter decorator is an obligatory " .
                "parameter");
        }

        $this->maxLoop = $settings['maxLoop'];
    }

    /**
     * Open method.
     *
     * @method open
     * @param {Tick} tick A tick instance.
     **/
    public function open ($tick) {
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

        if ($i < $this->maxLoop) {
            $status =  $this->child->_execute($tick);

            if ($status == B3::$SUCCESS || $status == B3::$FAILURE)
                $tick->blackboard->set('i', $i+1, $tick->tree->id, $this->id);

            return $status;
        }

        return B3::$FAILURE;
    }
}