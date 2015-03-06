<?php
/**
 * A new Tick object is instantiated every tick by BehaviorTree. It is passed
 * as parameter to the nodes through the tree during the traversal.
 *
 * The role of the Tick class is to store the instances of tree, debug, target
 * and blackboard. So, all nodes can access these informations.
 *
 * For internal uses, the Tick also is useful to store the open node after the
 * tick signal, in order to let `BehaviorTree` to keep track and close them
 * when necessary.
 *
 * This class also makes a bridge between nodes and the debug, passing the node
 * state to the debug if the last is provided.
 *
 * @class Tick
 **/
namespace Behavior3php\Core;

use  Behavior3php\Behavior3;

class Tick extends Behavior3 {
    /**
     * The tree reference.
     *
     * @property tree
     * @type {b3.BehaviorTree}
     * @readOnly
     */
    public $tree;
    /**
     * The debug reference.
     *
     * @property debug
     * @type {Object}
     * @readOnly
     */
    public $debug;
    /**
     * The target object reference.
     *
     * @property target
     * @type {Object}
     * @readOnly
     */
    public $target;
    /**
     * The blackboard reference.
     *
     * @property blackboard
     * @type {Blackboard}
     * @readOnly
     */
    public $blackboard;
    /**
     * The list of open nodes. Update during the tree traversal.
     *
     * @property _openNodes
     * @type {Array}
     * @protected
     * @readOnly
     */
    public $_openNodes=array();
    /**
     * The number of nodes entered during the tick. Update during the tree
     * traversal.
     *
     * @property _nodeCount
     * @type {Integer}
     * @protected
     * @readOnly
     */
    public $_nodeCount=0;


    /**
     * Called when entering a node (called by BaseNode).
     *
     * @method _enterNode
     * @param {Object} node The node that called this method.
     * @protected
     **/
    public function _enterNode ($node) {
        $this->_nodeCount++;
        array_push($this->_openNodes,$node);

        // TODO: call debug here
    }

    /**
     * Callback when opening a node (called by BaseNode).
     *
     * @method _openNode
     * @param {Object} node The node that called this method.
     * @protected
     **/
    public function _openNode($node) {
        // TODO: call debug here
    }

    /**
     * Callback when ticking a node (called by BaseNode).
     *
     * @method _tickNode
     * @param {Object} node The node that called this method.
     * @protected
     **/
    public function _tickNode ($node) {
        // TODO: call debug here
    }

    /**
     * Callback when closing a node (called by BaseNode).
     *
     * @method _closeNode
     * @param {Object} node The node that called this method.
     * @protected
     **/
    public function _closeNode ($node) {
        // TODO: call debug here
        array_pop($this->_openNodes);
    }

    /**
     * Callback when exiting a node (called by BaseNode).
     *
     * @method _exitNode
     * @param {Object} node The node that called this method.
     * @protected
     **/
    public function _exitNode ($node) {
        // TODO: call debug here
    }
}