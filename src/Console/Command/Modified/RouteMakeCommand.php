<?php

namespace Synga\LaravelDevelopment\Console\Command\Modified;

use Illuminate\Console\GeneratorCommand;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Synga\LaravelDevelopment\Parser\Node\Visitor\AddMethodCallInMethodVisitor;
use Synga\LaravelDevelopment\Parser\Node\Visitor\MethodFinderVisitor;
use Synga\LaravelDevelopment\RunCommandTrait;

/**
 * Class RouteMakeCommand
 * @package Synga\LaravelDevelopment\Console\Command\Modified
 */
class RouteMakeCommand extends GeneratorCommand
{
    use RunCommandTrait, ModifyCommandTrait;

    /**
     * @var array
     */
    protected $mandatoryData = [
        'root_namespace' => '',
        'path' => '',
    ];

    /**
     * @var string
     */
    private $path = 'Routes';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:route {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @return bool|null|void
     */
    public function handle()
    {
        parent::handle();

        $path = $this->getPath($this->parseName($this->argument('name')));
        $relativePath = '/' . trim(substr($path, strlen($this->mandatoryData['path'])), '\/');

        $serviceProvider = $this->getServiceProvider();

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(file_get_contents($serviceProvider));

        $found = $this->findRouteInServiceProvider($ast);

        if (false === $found) {
            $ast = $this->addMethodCallToServiceProvider($relativePath, $ast);

            $prettyPrinter = new Standard();
            file_put_contents($serviceProvider, $prettyPrinter->prettyPrintFile($ast));

            exec('php vendor/bin/php-cs-fixer fix "' . $serviceProvider . '"');
        }


        $this->addFileToGit($path);
    }

    /**
     * @return string
     */
    protected function getServiceProvider(): string
    {
        $serviceProviders = [];

        foreach (scandir($this->mandatoryData['path']) as $file) {
            if (false !== strpos($file, 'ServiceProvider')) {
                $serviceProviders[$file] = $this->mandatoryData['path'] . '/' . $file;
            }
        }

        if (1 === count($serviceProviders)) {
            $serviceProvider = current($serviceProviders);
        } else {
            $serviceProvider = $serviceProviders[$this->choice(
                'To which service prodiver do you want to add this route file?',
                array_keys($serviceProviders)
            )];
        }

        return $serviceProvider;
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->getPathTrait($name);
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return $this->parseNameTrait($name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . $this->path;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->mandatoryData['root_namespace'];
    }

    /**
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../Stubs/routes.stub';
    }

    /**
     * @param $ast
     * @return bool
     */
    protected function findRouteInServiceProvider($ast): bool
    {
        $found = false;

        $findLoadRoutesTraverser = new NodeTraverser();
        $findLoadRoutesTraverser->addVisitor(new MethodFinderVisitor(
            'loadRoutesFrom',
            function (Node\Expr\MethodCall $node) use (&$found) {
                foreach ($node->args as $arg) {
                    if ($arg->value instanceof Node\Expr\BinaryOp\Concat) {
                        if ($this->path === $arg->value->right->value) {
                            $found = true;
                        }
                    }
                }
            }
        ));

        $findLoadRoutesTraverser->traverse($ast);

        return $found;
    }

    /**
     * @param $relativePath
     * @param $ast
     * @return Node[]
     */
    protected function addMethodCallToServiceProvider($relativePath, $ast): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AddMethodCallInMethodVisitor(
                'boot',
                function (Node\Stmt\ClassMethod $node, AddMethodCallInMethodVisitor $visitor) use ($relativePath) {
                    array_splice(
                        $node->stmts,
                        ($visitor->findLastCallOfMethod($node, 'loadRoutesFrom')),
                        0,
                        [new Node\Expr\MethodCall(
                            new Node\Expr\Variable('this'),
                            'loadRoutesFrom',
                            [
                                new Node\Arg(
                                    new Node\Expr\BinaryOp\Concat(
                                        new Node\Scalar\MagicConst\Dir(),
                                        new Node\Scalar\String_($relativePath)
                                    )
                                )
                            ]
                        )]
                    );
                }
            )
        );

        $ast = $traverser->traverse($ast);

        return $ast;
    }
}