<?php
namespace App\Company\Models;
use Phalcon\Mvc\Model;

/**
 * 公司
 */
class Company extends Model
{
    public function initialize()
    {
        $this->setSource("Company");
    }
}
