<?php
/**
 * Behavior3php
 * ===========
 *
 * * * *
 *
 * **Behavior3php** 是用php的行为树库。 他是Behavior3js的PHP版本。
 * 它提供了结构和算法的任务来协助你创造你游戏或应用程序的智能代理。
 *
 *
 * 核心类和函数
 * --------------------------
 *
 * 本库的核心包括以下结构。
 *
 * **Public:**
 *
 * - **BehaviorTree**: 行为树;
 * - **Blackboard**: 黑板存储器;
 * - **Composite**: 综合节点;
 * - **Decorator**: 装饰节点;
 * - **Action**: 动作节点;
 * - **Condition**: 条件节点;
 *
 * **Internal:**
 *
 *
 * - **Tick**: 使用闭包函数通过树的执行信号执行;
 * - **BaseNode**: 基类，提供所有的公共节点的功能;
 *
 * *Some classes are used internally on Behavior3JS, but you may need to access
 * its functionalities eventually, specially the `Tick` object.*
 *
 *
 * Nodes Included
 * --------------
 *
 * **（Composite Nodes）综合节点**:
 *
 * - Sequence（序列）;
 * - Priority（优先）;
 * - MemSequence（纪念品序列）;
 * - MemPriority（纪念品优先）;
 *
 *
 * **Decorators（装饰节点）**:
 *
 * - Inverter（逆变器）;
 * - Limiter（限制器）;
 * - MaxTime（最大时间）;
 * - Repeater（重复）;
 * - RepeaterUntilFailure（重复，直到失败）;
 * - RepeaterUntilSuccess（重复，直到成功）;
 *
 *
 * **Actions（动作节点）**:
 *
 * - Succeeder（成功）;
 * - Failer（失败）;
 * - Error（错误）;
 * - Runner（运行）;
 * - Wait（等待）.
 *
 * @module Behavior3php
 * @main Behavior3php
 **/
namespace Behavior3php;



class B3 {

    /**
     * Version of the library.
     *
     * @property VERSION
     * @type {String}
     */
    public static $VERSION = '0.1.0';

    /**
     * Returned when a criterion has been met by a condition node or an action node
     * has been completed successfully.
     *
     * @property SUCCESS
     * @type {Integer}
     */
    public static $SUCCESS   = 1;

    /**
     * Returned when a criterion has not been met by a condition node or an action
     * node could not finish its execution for any reason.
     *
     * @property FAILURE
     * @type {Integer}
     */
    public static $FAILURE   = 2;

    /**
     * Returned when an action node has been initialized but is still waiting the
     * its resolution.
     *
     * @property FAILURE
     * @type {Integer}
     */
    public static $RUNNING   = 3;

    /**
     * Returned when some unexpected error happened in the tree, probably by a
     * programming error (trying to verify an undefined variable). Its use depends
     * on the final implementation of the leaf nodes.
     *
     * @property ERROR
     * @type {Integer}
     */
    public static $ERROR     = 4;


    /**
     * Describes the node category as Composite.
     *
     * @property COMPOSITE
     * @type {String}
     */
    public static $COMPOSITE = 'composite';

    /**
     * Describes the node category as Decorator.
     *
     * @property DECORATOR
     * @type {String}
     */
    public static $DECORATOR = 'decorator';

    /**
     * Describes the node category as Action.
     *
     * @property ACTION
     * @type {String}
     */
    public static $ACTION    = 'action';

    /**
     * Describes the node category as Condition.
     *
     * @property CONDITION
     * @type {String}
     */
    public static $CONDITION = 'condition';


    /**
     * New class.
     *
     * @property NEWCLASS
     * @type {array}
     */
    public static $NEWCLASS = array();

    public static $classFile=array(
        'Error'      =>'Actions\Error',
        'Succeeder'  =>'Actions\Succeeder',
        'Failer'     =>'Actions\Failer',
        'Runner'     =>'Actions\Runner',
        'Wait'       =>'Actions\Wait',

        'Sequence'   =>'Composites\Sequence',
        'Priority'   =>'Composites\Priority',
        'MemSequence'=>'Composites\MemSequence',
        'MemPriority'=>'Composites\MemPriority',

        'BehaviorTree'=>'Core\BehaviorTree',
        'Blackboard'  =>'Core\Blackboard',
        'Composite'   =>'Core\Composite',
        'Decorator'   =>'Core\Decorator',
        'Action'      =>'Core\Action',
        'Condition'   =>'Core\Condition',
        'Tick'        =>'Core\Tick',
        'BaseNode'    =>'Core\BaseNode',

        'Inverter'   =>'Decorators\Inverter',
        'Limiter'    =>'Decorators\Limiter',
        'MaxTime'    =>'Decorators\MaxTime',
        'Repeater'   =>'Decorators\Repeater',
        'RepeatUntilFailure'=>'Decorators\RepeatUntilFailure',
        'RepeatUntilSuccess'=>'Decorators\RepeatUntilSuccess'
    );
    /**
     * This function is used to create unique IDs for trees and nodes.
     *
     * (consult http://www.ietf.org/rfc/rfc4122.txt).
     *
     * @method createUUID
     * @return {String} A unique ID.
     **/
    public static function createUUID () {
        $s = array();
        $hexDigits = "0123456789abcdef";
        $hexDigitsLen=strlen($hexDigits)-1;
        for ($i = 0; $i < 36; $i++) {
            $s[$i] = substr($hexDigits,rand(0,$hexDigitsLen), 1);
        }
        // bits 12-15 of the time_hi_and_version field to 0010
        $s[14] = "4";

        // bits 6-7 of the clock_seq_hi_and_reserved to 01
        $s[19] = substr($hexDigits,($s[19] & 0x3) | 0x8, 1);

        $s[8] = $s[13] = $s[18] = $s[23] = "-";

        $uuid = implode("",$s);
        return $uuid;
    }
    /**
     * This function is used to create Behavior3php class.
     *
     * @method __callStatic
     * @return {object}
     **/
    public static function __callStatic($name, $arguments) {
        if(is_object(self::$NEWCLASS[$name])){
            return self::$NEWCLASS[$name];
        }
        $className=self::$classFile[$name];
        if($className){
            $className=  __NAMESPACE__."\\".$className;
            return new $className(current($arguments));
        }

        throw new \Exception('Class '.$name.' not found ');
    }
}