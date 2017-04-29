<?php
if(isset($argv))
{
  register_shutdown_function(function(){
    echo PHP_EOL;
  });
}
if(isset($argv[1]))
{
  $method=$argv[1];
}
elseif(isset($_GET["method"]))
{
  $method=$_GET["method"];
}
else
{
  die("Method not found.");
}
if(isset($argv))
{
  foreach($argv as $arg)
  {
    if(!preg_match("/(.+)\=(.+)/", $arg,$matches))
    {
      continue;
    }
    $_GET[$matches[1]]=$matches[2];
  }
}
if(method_exists("api",$method))
{
  $f=new ReflectionMethod("api",$method);
  if(!$f->isPublic())
  {
    die("Method not found.");
  }
  $args=[];
  foreach($f->getParameters() as $param)
  {
    if(isset($_GET[$param->name]))
    {
      $args[]=$_GET[$param->name];
    }
    elseif($param->isDefaultValueAvailable())
    {
      $args[]=$param->getDefaultValue();
    }
    else
    {
      http_response_code(400);
      die("Requires param ".$param->name);
    }
  }
  $data=api::$method(...$args);
  $data->method=$method;
  $d=json_encode($data);
  echo $d;
}
else
{
  die("Method not found.");
}
