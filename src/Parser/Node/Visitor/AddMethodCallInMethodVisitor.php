<?php

namespace Synga\LaravelDevelopment\Parser\Node\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddMethodCallInMethodVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * AddMethodCallInMethodVisitor constructor.
     * @param string $methodName
     * @param \Closure $closure
     */
    public function __construct(string $methodName, \Closure $closure)
    {
        $this->methodName = $methodName;
        $this->closure = $closure;
    }

    /**
     * @param Node $node
     * @return int|null|Node|void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->methodName === $node->name) {
                $closure = $this->closure;
                $closure($node, $this);
            }
        }
    }

    /**
     * @param Node\Stmt\ClassMethod $method
     * @param $name
     * @return int|string
     */
    public function findLastCallOfMethod(Node\Stmt\ClassMethod $method, $name)
    {
        $last = 0;
        if (!empty($method->stmts) && is_array($method->stmts)) {
            end($method->stmts);
            $last = key($method->stmts);
            foreach ($method->stmts as $key => $statement) {
                if ($statement instanceof Node\Expr\MethodCall) {
                    $last = ($name === $statement->name) ? $key + 1 : $last;
                }
            }
        }

        return $last;
    }

    public function insertAfter(){

    }
}