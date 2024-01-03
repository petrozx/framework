<?php
namespace Kernel\ControllerEngine;
use Kernel\HttpEngine\HttpRequest;
use Kernel\Reflection\ReflectionProcessing;
use Kernel\HttpEngine\HttpResponse;
use Kernel\ErrorRequest\Response_404;

class PointMatches extends ReflectionProcessing
{

    private const needle = \Kernel\ControllerEngine\Attributes\Controller::class;
    private const default404 = Response_404::class;
    private const methods = [
        'GET' => \Kernel\ControllerEngine\Attributes\GET::class,
        'POST'=> \Kernel\ControllerEngine\Attributes\POST::class,
        'PUT' => \Kernel\ControllerEngine\Attributes\PUT::class,
        'DELETE' => \Kernel\ControllerEngine\Attributes\DELETE::class,
        'PATCH' => \Kernel\ControllerEngine\Attributes\PATCH::class,
        'OPTIONS' => \Kernel\ControllerEngine\Attributes\OPTIONS::class,
        'HEAD' => \Kernel\ControllerEngine\Attributes\HEAD::class,
    ];

    public function __construct(
        private HttpRequest $request,
        private HttpResponse $response,
    ){
        parent::__construct();
        $finded = $this->find(self::needle, [
            'method' => self::methods[$this->request->getMethod()],
            'uri'    => $this->request->getRoute(),
        ]);
        $this->execute($finded);
    } 

    public function find($name, $args): array
    {
        foreach ($this->getAttributes() as $attributeClass) {
            if (isset($attributeClass[$name])) {
                foreach ($attributeClass[$name]['methods'] as $key => $method) {
                    $finalRoute = $method['args']['uri'];
                    if (isset($attributeClass[$name]['args']['uri'])) {
                        $finalRoute = $attributeClass[$name]['args']['uri'].$finalRoute;
                    }
                    $pattern = preg_replace('/{[^}]+}/', '([^/]+)', $finalRoute);
                    $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
                    if (preg_match($pattern, $args['uri'], $mathes) && $args['method'] === $method['name']) {
                        array_shift($mathes);
                        $method['args']['params'] ??= [];
                        foreach ($method['args']['params'] as $paramKey => &$paramType) {
                            $paramType = current($mathes);
                            next($mathes);
                        }
                        return [
                            'instance' => $attributeClass[$name]['instance'],
                            'methods'  => $method,
                            'params'   => $method['args']['params'],
                        ];
                    }
                }
            }
        }
        if (!isset($attributeClass['response_404'])) {
            $instance = new (self::default404);
            $attributeClass['response_404'] = [
                'instance' => $instance,
                'methods'  => [
                    'method' => new \ReflectionMethod($instance, 'NOT_FOUND')
                ],
                'params' => [],
            ];
        }

        return $attributeClass['response_404'];
    }

    public function execute($attributeClass): void
    {
        $attributeClass['methods']['method']->invoke($attributeClass['instance'], ...$attributeClass['params']);
    }

    private function matchingMethod()
    {

    }
}