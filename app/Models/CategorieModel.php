<?php

namespace App\Models;

use App\Core\Model;

/**
 * Table simple : all()/find($id)/delete($id) suffisent, herites tels
 * quels du Model parent. Pas besoin de les redefinir ici.
 */
class CategorieModel extends Model
{
    protected $table = 'categories';
}
