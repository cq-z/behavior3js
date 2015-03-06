<?php
/**
 * The BehaviorTree class, as the name implies, represents the Behavior Tree
 * structure.
 *
 * There are two ways to construct a Behavior Tree: by manually setting the
 * root node, or by loading it from a data structure (which can be loaded from
 * a JSON). Both methods are shown in the examples below and better explained
 * in the user guide.
 *
 * The tick method must be called periodically, in order to send the tick
 * signal to all nodes in the tree, starting from the root. The method
 * `BehaviorTree.tick` receives a target object and a blackboard as parameters.
 * The target object can be anything: a game agent, a system, a DOM object,
 * etc. This target is not used by any piece of Behavior3JS, i.e., the target
 * object will only be used by custom nodes.
 *
 * The blackboard is obligatory and must be an instance of `Blackboard`. This
 * requirement is necessary due to the fact that neither `BehaviorTree` or any
 * node will store the execution variables in its own object (e.g., the BT does
 * not store the target, information about opened nodes or number of times the
 * tree was called). But because of this, you only need a single tree instance
 * to control multiple (maybe hundreds) objects.
 *
 * Manual construction of a Behavior Tree
 * --------------------------------------
 *
 *     var tree = new b3.BehaviorTree();
 *
 *     tree.root = new b3.Sequence({children:[
 *         new b3.Priority({children:[
 *             new MyCustomNode(),
 *             new MyCustomNode()
 *         ]}),
 *         ...
 *     ]});
 *
 *
 * Loading a Behavior Tree from data structure
 * -------------------------------------------
 *
 *     var tree = new b3.BehaviorTree();
 *
 *     tree.load({
 *         'title'       : 'Behavior Tree title'
 *         'description' : 'My description'
 *         'root'        : 'node-id-1'
 *         'nodes'       : {
 *             'node-id-1' : {
 *                 'name'        : 'Priority', // this is the node type
 *                 'title'       : 'Root Node',
 *                 'description' : 'Description',
 *                 'children'    : ['node-id-2', 'node-id-3'],
 *             },
 *             ...
 *         }
 *     })
 *
 *
 * @class BehaviorTree
 **/
namespace Behavior3php\Core;

use  Behavior3php\Behavior3;
use  Behavior3php\B3;


class BehaviorTree extends Behavior3 {
    /**
     * The tree id, must be unique. By default, created with `b3.createUUID`.
     *
     * @property id
     * @type {String}
     * @readOnly
     */
    public $id;

    /**
     * The tree title.
     *
     * @property title
     * @type {String}
     */
    public $title= 'The behavior tree';
    /**
     * Description of the tree.
     *
     * @property description
     * @type {String}
     */
    public $description = 'Default description';
    /**
     * A dictionary with (key-value) properties. Useful to define custom
     * variables in the visual editor.
     *
     * @property properties
     * @type {Object}
     */
    public $properties=array();
    /**
     * The reference to the root node. Must be an instance of `b3.BaseNode`.
     *
     * @property root
     * @type {BaseNode}
     */
    public $root=null;
    /**
     * The reference to the debug instance.
     *
     * @property debug
     * @type {Object}
     */
    public $debug;

    /**
     * Initialization method.
     *
     * @method initialize
     * @constructor
     **/
    public function initialize ($params=null) {
        $this->id      = B3::createUUID();
    }

    /**
     * This method loads a Behavior Tree from a data structure, populating this
     * object with the provided data. Notice that, the data structure must
     * follow the format specified by Behavior3JS. Consult the guide to know
     * more about this format.
     *
     * You probably want to use custom nodes in your BTs, thus, you need to
     * provide the `names` object, in which this method can find the nodes by
     * `names[NODE_NAME]`. This variable can be a namespace or a dictionary,
     * as long as this method can find the node by its name, for example:
     *
     *     //json
     *     ...
     *     'node1': {
     *       'name': MyCustomNode,
     *       'title': ...
     *     }
     *     ...
     *
     *     //code
     *     var bt = new b3.BehaviorTree();
     *     bt.load(data, {'MyCustomNode':MyCustomNode})
     *
     *
     * @method load
     * @param {Object} data The data structure representing a Behavior Tree.
     * @param {Object} [names] A namespace or dict containing custom nodes.
     **/
    public function load ($data, $names=array()) {

        $this->title       = $data->title ?: $this->title;
        $this->description = $data->description ?:$this->description;
        $this->properties  = $data->properties ?: $this->properties;

        $nodes = array();
        if(empty($data->nodes)){return ;}
        // Create the node list (without connection between them)
        foreach ($data->nodes as $id=>$spec) {
            if (array_key_exists($spec->name, $names)) {
                // Look for the name in custom nodes
                $cls = $names[$spec->name];
                $node = new $cls((array)$spec->parameters);
            } else if (array_key_exists($spec->name, B3::$classFile)) {
                // Look for the name in default nodes
                $cls = $spec->name;
                $node = B3::$cls((array)$spec->parameters);
            } else {
                // Invalid node name
                throw new \Exception('BehaviorTree.load: Invalid node name + "'.
                    $spec->name.'".');
            }

            // $node = new $cls($spec->parameters);
            $node->id = $spec->id ?: $node->id;
            $node->title = $spec->title ?: $node->title;
            $node->description = $spec->description ?: $node->description;
            $node->properties = $spec->properties ?: $node->properties;
            $node->parameters = $spec->parameters ?: $node->parameters;

            $nodes[$id] = $node;
        }

        // Connect the nodes
        foreach ($data->nodes as $id=>$spec) {
            $node = &$nodes[$id];

            if ($node->category === B3::$COMPOSITE && $spec->children) {
                foreach ($spec->children as $cid) {
                    array_push($node->children,$nodes[$cid]);
                }
            } else if ($node->category === B3::$DECORATOR && $spec->child) {
                $node->child = $nodes[$spec->child];

            }
        }
        $this->root = $nodes[$data->root];
    }

    /**
     * This method dump the current BT into a data structure.
     *
     * Note: This method does not record the current node parameters. Thus,
     * it may not be compatible with load for now.
     *
     * @method dump
     * @returns {Object} A data object representing this tree.
     **/
    public function dump () {
        $data =new \stdClass();
        $customNames = array();


        $data->title       = $this->title;
        $data->description = $this->description;
        $data->root        = ($this->root)? $this->root->id:null;
        $data->properties  = $this->properties;
        $data->nodes       = array();
        $data->custom_nodes = array();
        if (!$this->root) {return $data;}

        $stack = array($this->root);
        while (count($stack) > 0) {
            $node = array_pop($stack);
            $spec = new \stdClass();
            $spec->id = $node->id;
            $spec->name = $node->name;
            $spec->title = $node->title;
            $spec->description = $node->description;
            $spec->properties = $node->properties;
            $spec->parameters = $node->parameters;

            // verify custom node
            /*$nodeName = $node->name;
            if (!B3::$nodeName() && array_search($nodeName,$customNames) ==false) {
                $subdata = new \stdClass();
                $subdata->name = $nodeName;
                $subdata->title = $node.title;
                $subdata->category = $node.category;

                array_push($customNames,$nodeName);
                array_push($data->custom_nodes,$subdata);
            }   */

            // store children/child
            if ($node->category === B3::$COMPOSITE && $node->children) {
                $children = array();
                foreach ($node->children as $nodeChildren) {
                    array_push($children,$nodeChildren->id);
                    array_push($stack,$nodeChildren);
                }
                $spec->children = $children;
            } else if ($node->category === B3::$DECORATOR && $node->child) {
                array_push($stack,$node->child);
                $spec->child = $node->child->id;
            }

            $data->nodes[$node->id] = $spec;
        }

        return $data;
    }

    /**
     * Propagates the tick signal through the tree, starting from the root.
     *
     * This method receives a target object of any type (Object, Array,
     * DOMElement, whatever) and a `Blackboard` instance. The target object has
     * no use at all for all Behavior3JS components, but surely is important
     * for custom nodes. The blackboard instance is used by the tree and nodes
     * to store execution variables (e.g., last node running) and is obligatory
     * to be a `Blackboard` instance (or an object with the same interface).
     *
     * Internally, this method creates a Tick object, which will store the
     * target and the blackboard objects.
     *
     * Note: BehaviorTree stores a list of open nodes from last tick, if these
     * nodes weren't called after the current tick, this method will close them
     * automatically.
     *
     * @method tick
     * @param {Object} target A target object.
     * @param {Blackboard} blackboard An instance of blackboard object.
     * @returns {Constant} The tick signal state.
     **/
    public function tick ($target, $blackboard) {
        if (!$blackboard) {
            throw new \Exception( 'The blackboard parameter is obligatory and must be an ' .
                'instance of b3.Blackboard');
        }

        /* CREATE A TICK OBJECT */
        $tick = new Tick();
        $tick->debug      = $this->debug;
        $tick->target     = $target;
        $tick->blackboard = $blackboard;
        $tick->tree       = $this;

        /* TICK NODE */
        $state = $this->root->_execute($tick);

        /* CLOSE NODES FROM LAST TICK, IF NEEDED */
        $lastOpenNodes = $blackboard->get('openNodes', $this->id);
        $currOpenNodes = array_slice($tick->_openNodes,0);

        // does not close if it is still open in this tick
        $start = 0;
        $min=min(count($lastOpenNodes),count($currOpenNodes));
        for ($i=0; $i<$min; $i++) {
            $start = $i+1;
            if ($lastOpenNodes[$i] !== $currOpenNodes[$i]) {
                break;
            }
        }
        // close the nodes
        for ($i=count($lastOpenNodes)-1; $i>=$start; $i--) {
            $lastOpenNodes[$i]->_close($tick);
        }



        /* POPULATE BLACKBOARD */
        $blackboard->set('openNodes', $currOpenNodes, $this->id);
        $blackboard->set('nodeCount', $tick->_nodeCount, $this->id);

        return $state;
    }

}
