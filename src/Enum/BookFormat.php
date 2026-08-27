<?php

namespace App\Enum;
enum BookFormat: string
{
    case POCKET = 'Poche';     //poche
    case PAPERBACK = 'Broché';   //broché
    case HARDCOVER = 'Relié';     //relié
    case EBOOK = 'ebook';
    //Pour devloppement
    case POCKET2 = "pocket";
    case PAPERBACK2 = 'paperback';
    case HARDCOVER3= "hardcover";
    
}