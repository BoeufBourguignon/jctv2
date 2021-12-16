<?php

class Controller
{
    protected static ClassModalManager $modal;

    public static function render($view, $params)
    {
        extract($params);
        require_once $view;
    }
}