<?php
namespace Synga\LaravelDevelopment\Parser\Node\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class MethodFinderVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $method;
    /**
     * @var \Closure
     */
    private $closure;

    /**
     * MethodFinderVisitor constructor.
     * @param string $method
     * @param \Closure $closure
     */
    public function __construct(string $method, \Closure $closure)
    {
        $this->method = $method;
        $this->closure = $closure;
    }

    /**
     * @param Node $node
     * @return int|null|Node|void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\MethodCall && $this->method === $node->name) {
            $closure = $this->closure;
        }
    }
}