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

class Behavior3 {

    /**
     *
     * @method Class
     * @param {Object} [baseClass] The super class.
     * @return {Object} A new class.
     **/
    public function __construct($params=null){
        // create a new class
        $this->initialize($params);
    }

    /**
     * Initialization method.
     *
     * @method initialize
     * @constructor
     **/
    public function initialize($params=null){

    }
}