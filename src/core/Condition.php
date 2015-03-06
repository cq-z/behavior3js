<?php
/**
 * Condition is the base class for all condition nodes. Thus, if you want to
 * create new custom condition nodes, you need to inherit from this class.
 *
 * @class Condition
 * @extends BaseNode
 **/
namespace Behavior3php\Core;

use  Behavior3php\B3;

class Condition extends BaseNode{
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
        $this->category = B3::$CONDITION;
        parent::initialize();
    }
}