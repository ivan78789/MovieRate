<?php
// 	Удобная функция для редиректа куда угодно
function redirect_to($link)
{
    return header("Location: $link");
}
