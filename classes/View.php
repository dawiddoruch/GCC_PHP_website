<?php

namespace classes;

class View
{
    public string $page_title = "";
    private string $__layout = "";
    public string $__view;
    private array $__data = array();


    public function __construct($layout = 'layout')
    {
        $filepath = __DIR__."/../views/".$layout.".php";
        if(!file_exists($filepath)) {
            echo "Layout '{$layout}' doesn't exist.";
            return;
        }
        $this->__layout = $filepath;
    }

    public function load(string $view, array $data = null) {
        if($this->__layout == "")
        {
            echo "Layout isn't set.";
            return;
        }

        $filepath = __DIR__."/../views/".$view.".php";

        if(!file_exists($filepath)) {
            echo "View doesn't exist.";
            return;
        }

        if($data != null) {
            foreach($data as $key => $value) {
                $this->__data[$key] = $value;
            }
        }

        $this->__view = $filepath;

        ob_start();
        require($this->__layout);
        ob_end_flush();
    }

    public function echo($name, $default = null) {
        if(isset($this->__data[$name]))
            echo $this->__data[$name];
        else if($default !== null)
            echo $default;
    }

    public function __get($name)
    {
        if(isset($this->__data[$name]))
            return $this->__data[$name];
        return null;
    }

    public function __set($name, $value)
    {
        $this->__data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->__data[$name]);
    }

}