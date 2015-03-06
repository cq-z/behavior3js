<?php
/**
 * Action is the base class for all action nodes. Thus, if you want to
 * create new custom action nodes, you need to inherit from this class.
 *
 * For example, take a look at the Runner action:
 *
 *     var Runner = b3.Class(b3.Action);
 *     var p = Runner.prototype;
 *
 *         p.name = 'Runner';
 *
 *         p.tick = function(tick) {
 *             return b3.RUNNING;
 *         }
 *
 * @class Action
 * @extends BaseNode
 **/
namespace Behavior3php\Core;

use  Behavior3php\B3;

class Action extends  BaseNode{
    /**
     * Node category. Default to `b3.ACTION`.
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
    public function initialize ($params=null) {
        $this->category =B3::$ACTION;

        parent::initialize();
    }
}