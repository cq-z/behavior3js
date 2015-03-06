<?php
include "B3.php";
include "Behavior3.php";

include "Core/Blackboard.php";
include "Core/BehaviorTree.php";
include "Core/BaseNode.php";
include "Core/Tick.php";
include "Core/Composite.php";
include "Core/Decorator.php";
include "Core/Action.php";
include "Core/Condition.php";

include "Composites/Sequence.php";
include "Composites/Priority.php";
include "Composites/MemSequence.php";
include "Composites/MemPriority.php";

include "Decorators/Repeater.php";
include "Decorators/RepeatUntilFailure.php";
include "Decorators/RepeatUntilSuccess.php";
include "Decorators/MaxTime.php";
include "Decorators/Inverter.php";
include "Decorators/Limiter.php";

include "Actions/Failer.php";
include "Actions/Succeeder.php";
include "Actions/Runner.php";
include "Actions/Error.php";
include "Actions/Wait.php";

$TickStub = function() {
    $t=Behavior3php\B3::Tick();
    $t->blackboard=Behavior3php\B3::Blackboard();
    $t->tree->id = 'tree1';
    return $t;
    return array(
        'tree'      =>array('id'=> 'tree1'),
            'blackboard'=>array(
            'set'=> "",
                'get'=>  ""
            ),
            'openNodes' =>array(),
            'nodeCount' => 0,

            '_enterNode' => "",
            '_openNode'  => "",
            '_tickNode'  => "",
            '_closeNode' => "",
            '_exitNode'  => "",
        );
    };

echo "Core: Blackboard<br>";
echo "Basic Read & Write operations<br>";
$blackboard = Behavior3php\B3::Blackboard();

$blackboard->set('var1', 'this is some value');
$blackboard->set('var2', 999888);

var_dump($blackboard->get('var1'), 'this is some value');echo "<br>";
var_dump($blackboard->get('var2'), 999888);    echo "<br>";
var_dump($blackboard->get('var3'), null);   echo "<br>";
  echo "<br><br>Tree memory initialization<br>";
$blackboard->set('var1', 'value', 'tree1');

var_dump($blackboard->get('var1', 'tree1')); echo "isNotUndefined<br>";
var_dump($blackboard->get('nodeMemory', 'tree1'));  echo "isNotUndefined<br>";
var_dump($blackboard->get('openNodes', 'tree1'));   echo "isNotUndefined<br>";
var_dump($blackboard->get('traversalCycle', 'tree1')); echo "isNotUndefined<br>";
echo "<br><br>Read & Write operations within Tree Scope<br>";

$blackboard->set('var1', 'this is some value', 'tree 1');
$blackboard->set('var2', 999888, 'tree 2');

var_dump($blackboard->get('var1', 'tree 1'), 'this is some value'); echo "<br>";
var_dump($blackboard->get('var2', 'tree 2'), 999888);      echo "<br>";

var_dump($blackboard->get('var1', 'tree 2'), null);  echo "<br>";
var_dump($blackboard->get('var2', 'tree 1'), null);    echo "<br>";

echo "<br><br>Read & Write operations within Tree and Node Scopes<br>";

$blackboard->set('var1', 'value 1', 'tree 1');
$blackboard->set('var2', 'value 2', 'tree 1', 'node 1');
$blackboard->set('var3', 'value 3', 'tree 1', 'node 2');
$blackboard->set('var4', 999888, 'tree 2');


var_dump($blackboard->get('var2', 'tree 1', 'node 1'), 'value 2');echo "<br>";
var_dump($blackboard->get('var3', 'tree 1', 'node 2'), 'value 3'); echo "<br>";
var_dump($blackboard->get('var2', 'tree 1', 'node 2'), undefined); echo "<br>";
var_dump($blackboard->get('var3', 'tree 1', 'node 1'), undefined);  echo "<br>";
var_dump($blackboard->get('var2', 'tree 1'), undefined);      echo "<br>";
var_dump($blackboard->get('var1', 'tree 1', 'node 1'), undefined);  echo "<br>";

var_dump($blackboard->get('var2', 'tree 2', 'node 1'), undefined);   echo "<br>";

echo "<br>Core: Behavior Tree<br>";

$tree = Behavior3php\B3::BehaviorTree();

echo "Initialization<br>";

var_dump($tree->id);   echo "isNotUndefined<br>";
var_dump($tree->title); echo "isNotUndefined<br>";
var_dump($tree->description); echo "isNotUndefined<br>";
var_dump($tree->root);       echo "isNotUndefined<br>";
var_dump($tree->properties); echo "isNotUndefined<br>";

echo "Call root<br>";
$node = Behavior3php\B3::BaseNode();
$target = [];

$blackboard->get('openNodes', 'tree1');

$tree->id = 'tree1';
$tree->root = $node;
$tree->tick($target, $blackboard);

var_dump($node->_execute); echo "isTrue<br>";

echo "Populate blackboard<br>";


 class node1{ function _execute($tick) {
    $tick->_enterNode(Behavior3php\B3::BaseNode());
    $tick->_enterNode(Behavior3php\B3::BaseNode());
}};
$node = new node1();

$blackboard->get('openNodes', 'tree1');

$tree->id = 'tree1';
$tree->root = $node;
$tree->tick($target, $blackboard);

$method = $blackboard->set('openNodes', ['node1', 'node2'], 'tree1');
var_dump($method->calledOnce); echo "isTrue<br>";
$method = $blackboard->set('nodeCount', 2, 'tree1');
var_dump($method->calledOnce); echo "isTrue<br>";


echo "<br><br>Core: Behavior Tree - Serialization<br>";

$data = '{
    "title"       : "A JSON Behavior Tree",
    "description" : "This description",
    "root"        : "1",
    "properties"  : {
        "variable" : "value"
    },
    "nodes" : {
        "1": {
            "id"          : "1",
            "name"        : "Priority",
            "title"       : "Root Node",
            "description" : "Root Description",
            "children"    : ["2", "3"],
            "properties"  : {
                "var1" : 123,
                "composite": {
                    "var2" : true,
                    "var3" : "value"
                }
            }
        },
        "2": {
            "name"        : "Inverter",
            "title"       : "Node 1",
            "description" : "Node 1 Description",
            "child"       : "4"
        },
        "3": {
            "name"        : "MemSequence",
            "title"       : "Node 2",
            "description" : "Node 2 Description",
            "children"    : []
        },
        "4": {
            "name"        : "MaxTime",
            "title"       : "Node 3",
            "description" : "Node 3 Description",
            "child"       : null,
            "parameters"  : {
                "maxTime" : 1
            }
        }
    }
}';
$tree->load(json_decode($data));
// Tree information
var_dump($tree->title, 'A JSON Behavior Tree'); echo "<br>";
var_dump($tree->description, 'This description');  echo "<br>";
var_dump($tree->properties);                   echo "<br>";
var_dump($tree->properties->variable, 'value');  echo "<br>";

// Root
var_dump($tree->root, Behavior3php\B3::Priority());echo "<br>";
var_dump($tree->root->id, '1');            echo "<br>";
var_dump($tree->root->title, 'Root Node');    echo "<br>";
var_dump($tree->root->description, 'Root Description');   echo "<br>";
var_dump(count($tree->root->children), 2);          echo "<br>";
var_dump($tree->root->properties);                echo "<br>";
var_dump($tree->root->properties->var1, 123);   echo "<br>";
var_dump($tree->root->properties->composite);        echo "<br>";
var_dump($tree->root->properties->composite->var2, true);   echo "<br>";
var_dump($tree->root->properties->composite->var3, 'value');  echo "<br>";
   echo "<br>";
// Node 1
$node = $tree->root->children[0];
var_dump($node, Behavior3php\B3::Inverter()); echo "<br>";
var_dump($node->title, 'Node 1');     echo "<br>";
var_dump($node->description, 'Node 1 Description'); echo "<br>";
var_dump($node->child);         echo "<br>";
echo "<br>";

// Node 2
$node = $tree->root->children[1];
var_dump($node, Behavior3php\B3::MemSequence());       echo "<br>";
var_dump($node->title, 'Node 2');    echo "<br>";
var_dump($node->description, 'Node 2 Description');  echo "<br>";
var_dump(count($node->children), 0);           echo "<br>";
echo "<br>";
// Node 3
$node = $tree->root->children[0]->child;
var_dump($node);  echo "<br>";
var_dump($node->title, 'Node 3');             echo "<br>";
var_dump($node->description, 'Node 3 Description');   echo "<br>";

echo "<br><br>Load JSON model with custom nodes<br>";


$CustomNode = Behavior3php\B3::Condition();

$data = '{
    "title"       : "A JSON Behavior Tree",
    "description" : "This descriptions",
    "root"        : "1",
    "nodes"       : {
        "1": {
            "name"        : "Priority",
            "title"       : "Root Node",
            "description" : "Root Description",
            "children"    : ["2"]
        },
        "2": {
            "name"        : "CustomNode",
            "title"       : "Node 2",
            "description" : "Node 2 Description"
        }
    }
}';
$tree->load(json_decode($data), array('CustomNode'=> $CustomNode));

// Root
var_dump($tree->root, b3.Priority);    echo "<br>";
var_dump($tree->root->title, 'Root Node');    echo "<br>";
var_dump($tree->root->description, 'Root Description');  echo "<br>";
var_dump(count($tree->root->children), 1);    echo "<br>";
echo "<br>";
// Node 2
$node = $tree->root->children[0];
var_dump($node, CustomNode);       echo "<br>";
var_dump($node->title, 'Node 2');   echo "<br>";
var_dump($node->description, 'Node 2 Description'); echo "<br>";

echo "<br><br>Dump JSON model<br>";
$CustomNode->name = 'CustomNode';
$CustomNode->title = 'custom';

$tree->properties = json_decode('{
    "prop": "value",
    "comp": {
        "val1": "234",
        "val2": "value"
    }
} ');

$node5 = $CustomNode;
$node5->id = 'node-5';
$node5->title = 'Node5';
$node5->description = 'Node 5 Description';

$node4 = Behavior3php\B3::Wait();
$node4->id = 'node-4';
$node4->title = 'Node4';
$node4->description = 'Node 4 Description';

$node3 = Behavior3php\B3::MemSequence(array('children'=>array($node5)));
$node3->id = 'node-3';
$node3->title = 'Node3';
$node3->description = 'Node 3 Description';

$node2 = Behavior3php\B3::Inverter(array('child'=>$node4));
$node2->id = 'node-2';
$node2->title = 'Node2';
$node2->description = 'Node 2 Description';

$node1 = Behavior3php\B3::Priority(array('children'=>array($node2, $node3)));
$node1->id = 'node-1';
$node1->title = 'Node1';
$node1->description = 'Node 1 Description';
$node1->properties = array( 'key' => 'value');


$tree->root = $node1;
$tree->title = 'Title in Tree';
$tree->description = 'Tree Description';

$data = $tree->dump();

var_dump($data->title, 'Title in Tree');  echo "<br>";
var_dump($data->description, 'Tree Description');   echo "<br>";
var_dump($data->root, 'node-1');                    echo "<br>";
var_dump($data->properties->prop, 'value');        echo "<br>";
var_dump($data->properties->comp->val1, 234);       echo "<br>";
var_dump($data->properties->comp->val2, 'value');    echo "<br>";

var_dump($data->custom_nodes);      echo "<br>";
var_dump(count($data->custom_nodes), 1);  echo "<br>";
var_dump($data->custom_nodes[0]['name'], 'CustomNode');echo "<br>";
var_dump($data->custom_nodes[0]['title'], 'custom');  echo "<br>";
var_dump($data->custom_nodes[0]['category'], b3.CONDITION);  echo "<br>";

var_dump($data->nodes["node-1"]);   echo "<br>";
var_dump($data->nodes["node-2"]);   echo "<br>";
var_dump($data->nodes["node-3"]);   echo "<br>";
var_dump($data->nodes["node-4"]);     echo "<br>";
var_dump($data->nodes["node-5"]);   echo "<br>";

var_dump($data->nodes["node-1"]->id, 'node-1');  echo "<br>";
var_dump($data->nodes["node-1"]->name, 'Priority');   echo "<br>";
var_dump($data->nodes["node-1"]->title, 'Node1');      echo "<br>";
var_dump($data->nodes["node-1"]->description, 'Node 1 Description');  echo "<br>";
var_dump($data->nodes["node-1"]->children[0], 'node-3');        echo "<br>";
var_dump($data->nodes["node-1"]->children[1], 'node-2');       echo "<br>";
var_dump($data->nodes["node-1"]->properties['key'], 'value');    echo "<br>";

var_dump($data->nodes['node-2']->name, 'Inverter');    echo "<br>";
var_dump($data->nodes['node-2']->title, 'Node2');     echo "<br>";
var_dump($data->nodes['node-2']->description, 'Node 2 Description');  echo "<br>";
var_dump($data->nodes['node-2']->child);             echo "<br>";

var_dump($data->nodes['node-3']->name, 'MemSequence');    echo "<br>";
var_dump($data->nodes['node-3']->title, 'Node3');         echo "<br>";
var_dump($data->nodes['node-3']->description, 'Node 3 Description');   echo "<br>";
var_dump(count($data->nodes['node-3']->children), 1);        echo "<br>";

var_dump($data->nodes['node-4']->name, 'Wait');            echo "<br>";
var_dump($data->nodes['node-4']->title, 'Node4');        echo "<br>";
var_dump($data->nodes['node-4']->description, 'Node 4 Description'); echo "<br>";
var_dump($data->nodes['node-4']->children);                  echo "<br>";
var_dump($data->nodes['node-4']->child);                   echo "<br>";

var_dump($data->nodes['node-5']->name, 'CustomNode');   echo "<br>";
var_dump($data->nodes['node-5']->title, 'Node5');       echo "<br>";
var_dump($data->nodes['node-5']->description, 'Node 5 Description'); echo "<br>";
var_dump($data->nodes['node-5']->children);     echo "<br>";
var_dump($data->nodes['node-5']->child);       echo "<br>";


echo "<br><br>Core: BaseNode<br>";
echo "Initialization<br>";


$node = Behavior3php\B3::BaseNode();

var_dump($node->id);   echo "<br>";
var_dump($node->name);  echo "<br>";
var_dump($node->category);  echo "<br>";
var_dump($node->title);     echo "<br>";
var_dump($node->description);  echo "<br>";
var_dump($node->parameters);   echo "<br>";
var_dump($node->properties);   echo "<br>";
var_dump($node->children);    echo "<br>";
var_dump($node->child);      echo "<br>";

echo "Open Node<br>";


$tick = $TickStub();
$node->id = 'node1';
$node->_execute($tick);

$method = $tick->blackboard->set('isOpen', true, 'tree1', 'node1');
var_dump($method); echo "<br>";

echo "Close Node<br>";

$node->id = 'node1';
$node->_execute($tick);

$method = $tick->blackboard->set('isOpen', false, 'tree1', 'node1');
var_dump($method); echo "<br>";

echo "Execute is calling functions?<br>";

$tick->blackboard->get('isOpen', 'tree1', 'node1');

$node->id    = 'node1';
;
$node->_execute($tick);

var_dump($node->enter($tick));     echo "<br>";
var_dump($node->open($tick));    echo "<br>";
var_dump($node->tick($tick));     echo "<br>";
var_dump($node->close($tick));    echo "<br>";
var_dump($node->exits($tick));      echo "<br>";

echo "<br><br>Core: Tick<br>";
echo "Initialization<br>";

$tick = Behavior3php\B3::Tick();

var_dump($tick->tree);    echo "<br>";
var_dump($tick->debug);    echo "<br>";
var_dump($tick->target);       echo "<br>";
var_dump($tick->blackboard);   echo "<br>";
var_dump($tick->_openNodes);     echo "<br>";
var_dump($tick->_nodeCount);      echo "<br>";

var_dump($tick->_nodeCount, 0);      echo "<br>";
var_dump(count($tick->_openNodes), 0);   echo "<br>";

$node = array('id'=> 'node1');

$tick->_enterNode($node);    echo "<br>";
var_dump($tick->_nodeCount, 1);  echo "<br>";
var_dump(count($tick->_openNodes), 1); echo "<br>";
var_dump($tick->_openNodes[0], $node);   echo "<br>";

$tick->_nodeCount = 1;
$tick->_openNodes = [$node];

$tick->_closeNode($node);    echo "<br>";
var_dump($tick->_nodeCount, 1);    echo "<br>";
var_dump(count($tick->_openNodes), 0);  echo "<br>";

echo "<br><br>Core: Composite<br>";
echo "Initialization<br>";

var_dump(Behavior3php\B3::Composite()->category, Behavior3php\B3::$COMPOSITE);   echo "<br>";
$node = Behavior3php\B3::Composite(array('children'=>['child1', 'child2']));

var_dump($node->id);    echo "<br>";
var_dump($node->title);   echo "<br>";
var_dump($node->description); echo "<br>";
var_dump($node->children);     echo "<br>";

var_dump($node->category, 'composite');  echo "<br>";
var_dump($node->children[0], 'child1');  echo "<br>";
var_dump($node->children[1], 'child2');   echo "<br>";


echo "<br><br>Core: Decorator<br>";
echo "Initialization<br>";

var_dump(Behavior3php\B3::Decorator()->category, Behavior3php\B3::$DECORATOR);  echo "<br>";
$node = Behavior3php\B3::Decorator(['child'=>'child1']);

var_dump($node->id);    echo "<br>";
var_dump($node->title);   echo "<br>";
var_dump($node->description);   echo "<br>";
var_dump($node->child, 'child1');   echo "<br>";
var_dump($node->category, 'decorator');   echo "<br>";


echo "<br><br>Core: Action<br>";
echo "Initialization<br>";
var_dump(Behavior3php\B3::Action()->category, Behavior3php\B3::$ACTION);  echo "<br>";

$node =  Behavior3php\B3::Action();

var_dump($node->id);     echo "<br>";
var_dump($node->title);   echo "<br>";
var_dump($node->description);  echo "<br>";
var_dump($node->category, 'action');   echo "<br>";

echo "<br><br>Core: Condition<br>";
echo "Initialization<br>";
var_dump(Behavior3php\B3::Condition()->category, Behavior3php\B3::$CONDITION);  echo "<br>";


$node = Behavior3php\B3::Condition();

var_dump($node->id);      echo "<br>";
var_dump($node->title);   echo "<br>";
var_dump($node->description);  echo "<br>";
var_dump($node->category, 'condition');  echo "<br>";

echo "<br><br>Composite: Sequence<br>";
class getNode {
    public $a;
    public $a2;
    public function __construct($status,$status2=null) {
              $this->a=$status;
              $this->a2=$status2;
    }
    public  function _execute () {
    return $this->a? $this->a:$this->a2;
    }
}
$tick = $TickStub();
var_dump(Behavior3php\B3::Sequence()->name, 'Sequence'); echo "<br>";
$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$SUCCESS);

$sequence = Behavior3php\B3::Sequence([children=>[$node1, $node2, $node3]]);
$status = $sequence->tick($tick);

var_dump($status, Behavior3php\B3::$SUCCESS);   echo "<br>";

$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$FAILURE);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);


$sequence = Behavior3php\B3::Sequence([children=>[$node1, $node2, $node3,$node4]]);
$status = $sequence->tick($tick);

var_dump($status, Behavior3php\B3::$FAILURE);  echo "<br>";

$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);

$sequence = Behavior3php\B3::Sequence([children=>[$node1, $node2, $node3,$node4]]);
$status = $sequence->tick($tick);



var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";

echo "<br><br>Composite: Priority<br>";
var_dump(Behavior3php\B3::Priority()->name, 'Priority');  echo "<br>";

$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$SUCCESS);

$sequence = Behavior3php\B3::Priority([children=>[$node1, $node2, $node3]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);  echo "<br>";
$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$FAILURE);
$node3 = new getNode(Behavior3php\B3::$FAILURE);
$sequence = Behavior3php\B3::Priority([children=>[$node1, $node2, $node3]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$FAILURE);  echo "<br>";
$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$FAILURE);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);
$sequence = Behavior3php\B3::Priority([children=>[$node1, $node2, $node3,$node4]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";

echo "<br><br>Composite: MemSequence<br>";
var_dump(Behavior3php\B3::MemSequence()->name, 'MemSequence');  echo "<br>";



$msequence = Behavior3php\B3::MemSequence();
$msequence->id = 'node1';
$msequence->open($tick);

$method = $tick->blackboard->set('runningChild', 0, 'tree1', 'node1');

$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$SUCCESS);
$sequence = Behavior3php\B3::MemSequence([children=>[$node1, $node2, $node3]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);  echo "<br>";
$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$FAILURE);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);
$sequence = Behavior3php\B3::MemSequence([children=>[$node1, $node2, $node3,$node4]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$FAILURE);  echo "<br>";
$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);
$sequence = Behavior3php\B3::MemSequence([children=>[$node1, $node2, $node3,$node4]]);
$status = $sequence->tick($tick);
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";

$node1 = new getNode(Behavior3php\B3::$SUCCESS);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);
$node5 = new getNode(Behavior3php\B3::$FAILURE);
$node6 = new getNode(Behavior3php\B3::$SUCCESS);
$msequence = Behavior3php\B3::MemSequence([children=>[$node1, $node2, $node3,$node4,$node5,$node6]]);
$msequence->id = 'node1';
$tick->blackboard->get('runningChild', 'tree1', 'node1');

$status = $msequence->tick($tick);
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";
var_dump($tick->blackboard->get('runningChild',  0, 'tree1', 'node1'));
var_dump($tick->blackboard->get('runningChild', 2, 'tree1', 'node1'));
var_dump($tick->blackboard->get('runningChild', 'tree1', 'node1'));
$msequence = Behavior3php\B3::MemSequence([children=>[$node1, $node2,$node4,$node5,$node6]]);

$status = $msequence->tick($tick);
var_dump($status, Behavior3php\B3::$FAILURE);  echo "<br>";

echo "<br><br>Composite: MemPriority<br>";
var_dump(Behavior3php\B3::MemPriority()->name, 'MemPriority');  echo "<br>";

$mpriority = Behavior3php\B3::MemPriority();
$mpriority->id = 'node1';
$mpriority->open($tick);

$method = $tick->blackboard->set('runningChild', 0, 'tree1', 'node1');

$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$SUCCESS);
$node3 = new getNode(Behavior3php\B3::$SUCCESS);
$mpriority = Behavior3php\B3::MemPriority([children=>[$node1, $node2, $node3]]);
$status = $mpriority->tick($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);  echo "<br>";

$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$FAILURE);
$node3 = new getNode(Behavior3php\B3::$FAILURE);
$mpriority = Behavior3php\B3::MemPriority([children=>[$node1, $node2, $node3]]);
$status = $mpriority->tick($tick);
var_dump($status, Behavior3php\B3::$FAILURE);  echo "<br>";


$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$FAILURE);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$SUCCESS);
$mpriority = Behavior3php\B3::MemPriority([children=>[$node1, $node2, $node3,$node4]]);
$status = $mpriority->tick($tick);
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";

$node1 = new getNode(Behavior3php\B3::$FAILURE);
$node2 = new getNode(Behavior3php\B3::$FAILURE);
$node3 = new getNode(Behavior3php\B3::$RUNNING);
$node4 = new getNode(Behavior3php\B3::$FAILURE);
$node5 = new getNode(Behavior3php\B3::$SUCCESS);
$node6 = new getNode(Behavior3php\B3::$FAILURE);

$mpriority = Behavior3php\B3::MemPriority([children=>[$node1, $node2, $node3,$node5,$node6]]);
$mpriority->id = 'node1';

$method = $tick->blackboard->set('runningChild', 'tree1', 'node1');
$status = $mpriority->tick($tick);
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";
var_dump($tick->blackboard->get('runningChild',  0, 'tree1', 'node1'));
var_dump($tick->blackboard->get('runningChild', 2, 'tree1', 'node1'));
var_dump($tick->blackboard->get('runningChild', 'tree1', 'node1'));
$mpriority = Behavior3php\B3::MemPriority([children=>[$node1, $node2, $node4,$node5,$node6]]);

$status = $mpriority->tick($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);  echo "<br>";

echo "<br><br>Decorator: Repeater <br>";
var_dump(Behavior3php\B3::Repeater()->name, 'Repeater');  echo "<br>";

$node = Behavior3php\B3::Repeater();
var_dump($node->maxLoop, -1);       echo "<br>";
var_dump($node->name, 'Repeater');   echo "<br>";

$node = Behavior3php\B3::Repeater([maxLoop=>5]);
var_dump($node->maxLoop, 5);                  echo "<br>";


class getchild {
    public $a;
    public $a2;
    public $a3;
    public $co=0;
    public function __construct($status,$status2=null,$status3=null) {
        $this->a=$status;
        $this->a2=$status2;
        $this->a3=$status3;
    }
    public  function _execute ($a=null) {
        $aa=$this->co;
        $this->co++;
        return ($aa!==$this->a2)? $this->a:$this->a3;
    }
    public  function con () {
        return $this->co ;
    }
}
$child = new getchild(Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::Repeater([maxLoop=>7, child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 7);    echo "<br>";
var_dump($status, Behavior3php\B3::$SUCCESS);  echo "<br>";

$child = new getchild(Behavior3php\B3::$SUCCESS,5,Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Repeater([maxLoop=>50, child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 6);    echo "<br>";
var_dump($status, Behavior3php\B3::$RUNNING);  echo "<br>";

$child = new getchild(Behavior3php\B3::$SUCCESS,3,Behavior3php\B3::$ERROR);
$node = Behavior3php\B3::Repeater([maxLoop=>50, child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
var_dump($status, Behavior3php\B3::$ERROR);  echo "<br>";


$child = new getchild(Behavior3php\B3::$SUCCESS,3,Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Repeater([maxLoop=>10, child=>$child]);
$node->id= 'node1';
$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
$tick->blackboard->set('i', 3, 'tree1', 'node1');
$status = $node->_execute($tick);
var_dump($child->con(), 11);    echo "<br>";


echo "<br><br>Decorator: RepeatUntilSuccess<br>";
var_dump(Behavior3php\B3::RepeatUntilSuccess()->name, 'RepeatUntilSuccess');  echo "<br>";

$node = Behavior3php\B3::RepeatUntilSuccess();

var_dump($node->maxLoop, -1);    echo "<br>";
var_dump($node->name, 'RepeatUntilSuccess');  echo "<br>";
$node = Behavior3php\B3::RepeatUntilSuccess([maxLoop=>5]);
var_dump($node->maxLoop, 5);    echo "<br>";



$child = new getchild(Behavior3php\B3::$FAILURE);
$node = Behavior3php\B3::RepeatUntilSuccess([maxLoop=>7,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 7);    echo "<br>";
var_dump($status, Behavior3php\B3::$FAILURE);    echo "<br>";




$child = new getchild(Behavior3php\B3::$FAILURE,3,Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::RepeatUntilSuccess([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
var_dump($status, Behavior3php\B3::$SUCCESS);    echo "<br>";

$child = new getchild(Behavior3php\B3::$FAILURE,5,Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::RepeatUntilSuccess([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 6);    echo "<br>";
var_dump($status, Behavior3php\B3::$RUNNING);    echo "<br>";


$child = new getchild(Behavior3php\B3::$FAILURE,3,Behavior3php\B3::$ERROR);
$node = Behavior3php\B3::RepeatUntilSuccess([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
var_dump($status, Behavior3php\B3::$ERROR);    echo "<br>";

echo "<br><br>Decorator: RepeatUntilFailure<br>";
var_dump(Behavior3php\B3::RepeatUntilFailure()->name, 'RepeatUntilFailure');  echo "<br>";


$node = Behavior3php\B3::RepeatUntilFailure();
var_dump($node->maxLoop, -1);       echo "<br>";
var_dump($node->name, 'RepeatUntilFailure');   echo "<br>";

$node = Behavior3php\B3::RepeatUntilFailure([maxLoop=>5]);
var_dump($node->maxLoop, 5);       echo "<br>";

$child = new getchild(Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::RepeatUntilFailure([maxLoop=>7,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 7);    echo "<br>";
var_dump($status, Behavior3php\B3::$SUCCESS);    echo "<br>";



$child = new getchild(Behavior3php\B3::$SUCCESS,3,Behavior3php\B3::$FAILURE);
$node = Behavior3php\B3::RepeatUntilFailure([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
var_dump($status, Behavior3php\B3::$FAILURE);    echo "<br>";


$child = new getchild(Behavior3php\B3::$SUCCESS,5,Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::RepeatUntilFailure([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 6);    echo "<br>";
var_dump($status, Behavior3php\B3::$RUNNING);    echo "<br>";


$child = new getchild(Behavior3php\B3::$SUCCESS,3,Behavior3php\B3::$ERROR);
$node = Behavior3php\B3::RepeatUntilFailure([maxLoop=>50,child=>$child]);

$status = $node->_execute($tick);
var_dump($child->con(), 4);    echo "<br>";
var_dump($status, Behavior3php\B3::$ERROR);    echo "<br>";

echo "<br><br>Decorator: Inverter<br>";
var_dump(Behavior3php\B3::Inverter()->name, 'Inverter');  echo "<br>";

$node = Behavior3php\B3::Inverter();
var_dump($node->name, 'Inverter');  echo "<br>";

$child = new getchild(Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::Inverter(['child'=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$FAILURE);    echo "<br>";

$child = new getchild(Behavior3php\B3::$FAILURE);
$node = Behavior3php\B3::Inverter([child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);    echo "<br>";

$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Inverter([child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$RUNNING);    echo "<br>";

$child = new getchild(Behavior3php\B3::$ERROR);
$node = Behavior3php\B3::Inverter([child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$ERROR);    echo "<br>";


echo "<br><br>Decorator: MaxTime<br>";
var_dump(Behavior3php\B3::MaxTime(['maxTime'=>1])->name, 'MaxTime');  echo "<br>";




$startTime = time();

$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::MaxTime([maxTime=>15, child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$RUNNING);    echo "<br>";

//while (time() - $startTime < 25) { sleep(1);}

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$FAILURE);    echo "<br>";


$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::MaxTime([maxTime=>15, child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$RUNNING);    echo "<br>";


//while (time() - $startTime < 5) { sleep(1);}

$child = new getchild(Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::MaxTime([maxTime=>15, child=>$child]);

$status = $node->_execute($tick);
var_dump($status, Behavior3php\B3::$SUCCESS);    echo "<br>";


echo "<br><br>Decorator: Limiter<br>";
var_dump(Behavior3php\B3::Limiter(['maxLoop'=>1])->name, 'Limiter');  echo "<br>";

$node = Behavior3php\B3::Limiter([maxLoop=>3]);
var_dump($node->name, 'Limiter');  echo "<br>";

$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Limiter([maxLoop=>3, child=>$child]);
$node->id = 'node1';
$status = $node->_execute($tick);
$tick->blackboard->set('i', 0, 'tree1', 'node1') ;    echo "<br>";

$child = new getchild(Behavior3php\B3::$SUCCESS);
$node = Behavior3php\B3::Limiter([maxLoop=>10, child=>$child]);
$node->id = 'node1';

$status = $node->_execute($tick);
var_dump($child->con());    echo "<br>";
$status = $node->_execute($tick);
var_dump($child->con());    echo "<br>";


$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Limiter([maxLoop=>3, child=>$child]);
$node->id = 'node1';
$status = $node->_execute($tick);
$tick->blackboard->set('i', 0, 'tree1', 'node1') ;    echo "<br>";

$child = new getchild(Behavior3php\B3::$RUNNING);
$node = Behavior3php\B3::Limiter([maxLoop=>10, child=>$child]);
$node->id = 'node1';

$status = $node->_execute($tick);

var_dump($tick->blackboard->get('i',  'tree1', 'node1')) ;    echo "<br>";


echo "<br><br>Action: Failer<br>";
var_dump(Behavior3php\B3::Failer()->name, 'Failer');  echo "<br>";

$failer = Behavior3php\B3::Failer();

$status = $failer->_execute($tick);
var_dump($status,  Behavior3php\B3::$FAILURE); echo "<br>";

echo "<br><br>Action: Succeeder<br>";
var_dump(Behavior3php\B3::Succeeder()->name, 'Succeeder');  echo "<br>";

$failer = Behavior3php\B3::Succeeder();

$status = $failer->_execute($tick);
var_dump($status,  Behavior3php\B3::$SUCCESS); echo "<br>";

echo "<br><br>Action: Runner<br>";
var_dump(Behavior3php\B3::Runner()->name, 'Runner');  echo "<br>";

$failer = Behavior3php\B3::Runner();

$status = $failer->_execute($tick);
var_dump($status,  Behavior3php\B3::$RUNNING); echo "<br>";

echo "<br><br>Action: Error<br>";
var_dump(Behavior3php\B3::Error()->name, 'Error');  echo "<br>";

$failer = Behavior3php\B3::Error();

$status = $failer->_execute($tick);
var_dump($status,  Behavior3php\B3::$ERROR); echo "<br>";

echo "<br><br>Action: Wait<br>";
var_dump(Behavior3php\B3::Wait()->name, 'Wait');  echo "<br>";

$wait = Behavior3php\B3::Wait([milliseconds=>15]);
$wait->id=  'node1';
$tick->blackboard->set('startTime',$startTime, 'tree1', 'node1');
$status = $wait->_execute($tick);
var_dump($status,  Behavior3php\B3::$RUNNING); echo "<br>";

while (time() - $startTime < 25) { sleep(1); }

$status = $wait->_execute($tick);
var_dump($status,  Behavior3php\B3::$SUCCESS); echo "<br>";


















