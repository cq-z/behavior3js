<?php
/**
 * Decorator is the base class for all decorator nodes. Thus, if you want to
 * create new custom decorator nodes, you need to inherit from this class.
 *
 * When creating decorator nodes, you will need to propagate the tick signal to
 * the child node manually, just like the composite nodes. To do that, override
 * the `tick` method and call the `_execute` method on the child node. For
 * instance, take a look at how the Inverter node inherit this class and how it
 * call its children:
 *
 *
 *     // Inherit from Decorator, using the util function Class.
 *     var Inverter = b3.Class(b3.Decorator);
 *     var p = Inverter.prototype;
 *
 *         // Remember to set the name of the node.
 *         p.name = 'Inverter';
 *
 *         // Override the tick function
 *         p.tick = function(tick) {
 *             if (!this.child) {
 *                 return b3.ERROR;
 *             }
 *
 *             // Propagate the tick
 *             var status = this.child._execute(tick);
 *
 *             if (status == b3.SUCCESS)
 *                 status = b3.FAILURE;
 *             else if (status == b3.FAILURE)
 *                 status = b3.SUCCESS;
 *
 *             return status;
 *         };
 *
 * @class Decorator
 * @extends BaseNode
 **/
namespace Behavior3php\Core;

use  Behavior3php\B3;

class Decorator extends BaseNode{
    /**
     * Node category. Default to b3.DECORATOR.
     *
     * @property category
     * @type {String}
     * @readonly
     **/
    public $category;


    /**
     * Initialization method.
     *
     * @method initialize
     * @constructor
     **/
    public function initialize($settings=array()) {
        $this->category = B3::$DECORATOR;

        parent::initialize();
        $this-> child = $settings['child'] ?: array();
    }
}