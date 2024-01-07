<?php
namespace Kernel\Async;
class AsyncClosure
{
    public static function prepare($func)
    {
        $refl = new \ReflectionFunction($func);
        $path = $refl->getFileName();
        $begn = $refl->getStartLine();
        $endn = $refl->getEndLine();
        $dlim = PHP_EOL;
        $list = explode($dlim, file_get_contents($path));
        $list = array_slice($list, ($begn-1), ($endn-($begn-1)));
        $last = (count($list)-1);
    
        if((substr_count($list[0],'function')>1)|| (substr_count($list[0],'{')>1) || (substr_count($list[$last],'}')>1))
        { throw new \Exception("Too complex context definition in: `$path`. Check lines: $begn & $endn."); }
    
        $list[0] = ('function'.explode('function',$list[0])[1]);
        $list[$last] = (explode('}',$list[$last])[0].'}');
    
    
        return base64_encode(implode($dlim,$list));
    }

    public static function execute($preparedFunction, $args)
    {
        $closure = base64_decode($preparedFunction);
        ob_start();
        eval("echo ($closure)($args);");
        return ob_get_clean();
    }
}