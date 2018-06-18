<?php
namespace AppTest\Model;

use PHPUnit\Framework\TestCase;
use App\Model\FantasiasModel;
class FantasiasModelTest extends TestCase
{
    public function testListFantasias()
    {
        $model =  new FantasiasModel();
        $this->assertInternalType('array', $model->listItems());
    }

    public function testCalculatetotal()
    {
        $model =  new FantasiasModel();
        $items = $model->listItems();

        $total = $model->calculateTotal($items);
        $this->assertEquals('375,00', $total);
    }

    public function testApplyFreight()
    {
        $model =  new FantasiasModel();
        $value = 100;
        $total = $model->applyFreight($value);
        $this->assertEquals('145,00', $total);
    }
}

?>