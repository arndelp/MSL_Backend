<?php

namespace App\Enum;
enum BookFormat: string
{
    case POCKET = 'pocket';     //poche
    case PAPERBACK = 'paperback';   //broché
    case HARDCOVER = 'hardcover';     //relié
    case EBOOK = 'ebook';
    
}