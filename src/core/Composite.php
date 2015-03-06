<?php
/**
 * Composite is the base class for all composite nodes. Thus, if you want to
 * create new custom composite nodes, you need to inherit from this class.
 *
 * When creating composite nodes, you will need to propagate the tick signal to
 * the children nodes manually. To do that, override the `tick` method and call
 * the `_execute` method on all nodes. For instance, take a look at how the
 * Sequence node inherit this class and how it call its children:
 *
 *
 *     // Inherit from Composite, using the util function Class.
 *     var Sequence = b3.Class(b3.Composite);
 *     var p = Sequence.prototype;
 *
 *         // Remember to set the name of the node.
 *         p.name = 'Sequence';
 *
 *         // Override the tick function
 *         p.tick = function(tick) {
 *
 *             // Iterates over the children
 *             for (var i=0; i<this.children.length; i++) {
 *
 *                 // Propagate the tick
 *                 var status = this.children[i]._execute(tick);
 *
 *                 if (status !== b3.SUCCESS) {
 *                     return status;
 *                 }
 *             }
 *
 *             return b3.SUCCESS;
 *         }
 *
 * @class Composite
 * @extends BaseNode
 **/
namespace Behavior3php\Core;

use  Behavior3php\B3;

class Composite extends BaseNode{
    /**
     * Node category. Default to `b3.COMPOSITE`.
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
        $this->category = B3::$COMPOSITE;

        parent::initialize();

        $this-> children = array_slice (($settings['children'] ?: array()),0);
    }
}