<?php
namespace AppTest\Model;

use PHPUnit\Framework\TestCase;
use App\Model\FantasiasModel;
class FantasiasModelTest extends TestCase
{
    public function testListFantasias()
    {
        $model =  new FantasiasModel();
        $this->assertInternalType('array', $model->findFantasias());
    }
}

?>