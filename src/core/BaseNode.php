<?php
/**
 * The BaseNode class is used as super class to all nodes in BehaviorJS. It
 * comprises all common variables and methods that a node must have to execute.
 *
 * **IMPORTANT:** Do not inherit from this class, use `b3.Composite`,
 * `b3.Decorator`, `b3.Action` or `b3.Condition`, instead.
 *
 * The attributes are specially designed to serialization of the node in a JSON
 * format. In special, the `parameters` attribute can be set into the visual
 * editor (thus, in the JSON file), and it will be used as parameter on the
 * node initialization at `BehaviorTree.load`.
 *
 * BaseNode also provide 5 callback methods, which the node implementations can
 * override. They are `enter`, `open`, `tick`, `close` and `exit`. See their
 * documentation to know more. These callbacks are called inside the `_execute`
 * method, which is called in the tree traversal.
 *
 * @class BaseNode
 **/
namespace Behavior3php\Core;

use  Behavior3php\Behavior3;
use  Behavior3php\B3;

class BaseNode extends Behavior3 {
    /**
     * Node ID.
     *
     * @property id
     * @type {String}
     * @readonly
     **/
    public $id = null;

    /**
     * Node name. Must be a unique identifier, preferable the same name of the
     * class. You have to set the node name in the prototype.
     *
     * @property name
     * @type {String}
     * @readonly
     **/
    public $name = null;

    /**
     * Node category. Must be `b3.COMPOSITE`, `b3.DECORATOR`, `b3.ACTION` or
     * `b3.CONDITION`. This is defined automatically be inheriting the
     * correspondent class.
     *
     * @property category
     * @type constant
     * @readonly
     **/
    public $category = null;

    /**
     * Node title.
     *
     * @property title
     * @type {String}
     * @optional
     * @readonly
     **/
    public $title = null;

    /**
     * Node description.
     *
     * @property description
     * @type {String}
     * @optional
     * @readonly
     **/
    public $description = '';

    /**
     * A dictionary (key, value) describing the node parameters. Useful for
     * defining parameter values in the visual editor. Note: this is only
     * useful for nodes when loading trees from JSON files.
     *
     * @property parameters
     * @type {Object}
     * @readonly
     **/
    public $parameters = null;

    /**
     * A dictionary (key, value) describing the node properties. Useful for
     * defining custom variables inside the visual editor.
     *
     * @property properties
     * @type {Object}
     * @readonly
     **/
    public $properties = null;

    /**
     * Initialization method.
     *
     * @method initialize
     * @constructor
     **/
    public function initialize ($params=null) {
        $this->id          = B3::createUUID();
        $this->title       = $this->title ?: $this->name;
    }

    /**
     * This is the main method to propagate the tick signal to this node. This
     * method calls all callbacks: `enter`, `open`, `tick`, `close`, and
     * `exit`. It only opens a node if it is not already open. In the same
     * way, this method only close a node if the node  returned a status
     * different of `b3.RUNNING`.
     *
     * @method _execute
     * @param {Tick} tick A tick instance.
     * @returns {Constant} The tick state.
     * @protected
     **/
    public function _execute ($tick) {
        /* ENTER */
        $this->_enter($tick);

        /* OPEN */
        if (!$tick->blackboard->get('isOpen', $tick->tree->id, $this->id)) {
            $this->_open($tick);
        }

        /* TICK */
        $status = $this->_tick($tick);

        /* CLOSE */
        if ($status !== B3::$RUNNING) {
            $this->_close($tick);
        }

        /* EXIT */
        $this->_exit($tick);

        return $status;
    }

    /**
     * Wrapper for enter method.
     *
     * @method _enter
     * @param {Tick} tick A tick instance.
     * @protected
     **/
    public function _enter ($tick) {
        $tick->_enterNode($this);
        $this->enter($tick);
    }

    /**
     * Wrapper for open method.
     *
     * @method _open
     * @param {Tick} tick A tick instance.
     * @protected
     **/
    public function _open ($tick) {
        $tick->_openNode($this);
        $tick->blackboard->set('isOpen', true, $tick->tree->id, $this->id);
        $this->open($tick);
    }

    /**
     * Wrapper for tick method.
     *
     * @method _tick
     * @param {Tick} tick A tick instance.
     * @returns {Constant} A state constant.
     * @protected
     **/
    public function _tick ($tick) {
        $tick->_tickNode($this);
        return $this->tick($tick);
    }

    /**
     * Wrapper for close method.
     *
     * @method _close
     * @param {Tick} tick A tick instance.
     * @protected
     **/
    public function _close ($tick) {
        $tick->_closeNode($this);
        $tick->blackboard->set('isOpen', false, $tick->tree->id, $this->id);
        $this->close($tick);
    }

    /**
     * Wrapper for exit method.
     *
     * @method _exit
     * @param {Tick} tick A tick instance.
     * @protected
     **/
    public function _exit ($tick) {
        $tick->_exitNode($this);
        $this->exits($tick);
    }

    /**
     * Enter method, override this to use. It is called every time a node is
     * asked to execute, before the tick itself.
     *
     * @method enter
     * @param {Tick} tick A tick instance.
     **/
    public function enter ($tick) {}

    /**
     * Open method, override this to use. It is called only before the tick
     * callback and only if the not isn't closed.
     *
     * Note: a node will be closed if it returned `b3.RUNNING` in the tick.
     *
     * @method open
     * @param {Tick} tick A tick instance.
     **/
    public function open($tick) {}

    /**
     * Tick method, override this to use. This method must contain the real
     * execution of node (perform a task, call children, etc.). It is called
     * every time a node is asked to execute.
     *
     * @method tick
     * @param {Tick} tick A tick instance.
     **/
    public function tick($tick) {}

    /**
     * Close method, override this to use. This method is called after the tick
     * callback, and only if the tick return a state different from
     * `b3.RUNNING`.
     *
     * @method close
     * @param {Tick} tick A tick instance.
     **/
    public function close($tick) {}

    /**
     * Exits method, override this to use. Called every time in the end of the
     * execution.
     *
     * @method exits
     * @param {Tick} tick A tick instance.
     **/
    public function exits($tick) {}

}