<?php
namespace App\Model;

class FantasiasModel
{
    const FRETE = 45;

    public function listItems()
    {
        $fantasias[] = [
            'id' => 1,
            'description' => 'Fantasia do Darth Vader',
            'provider' => 'Maria Barros',
            'price' => '125,00',
            'percentageOwner' => 100,
            'percentageProvider'=>0
        ];
        $fantasias[] = [
            'id' => 2,
            'description' => 'Fantasia do Cafú',
            'provider' => 'João Thiago Samuel Cavalcanti',
            'price' => '100,00',
            'percentageOwner' => 15,
            'percentageProvider' => 85,

        ];
        $fantasias[] = [
            'id' => 3,
            'description' => 'Máscara de Cavalo',
            'provider' => 'César Anthony João Martins',
            'price' => '150,00',
            'percentageOwner' => 15,
            'percentageProvider' => 85,
        ];
        
        return $fantasias;
    }

    public function applyFreight($value)
    {
        return number_format((float)$value + self::FRETE, 2, ',', '');
    }

    public function calculateTotal($fantasias)
    {
        $total = 0;
        foreach ($fantasias as $fantasia) {
            $total += (float)$fantasia['price'];
        }
        return number_format($total, 2,',','');
    }

    public function getFreight()
    {
        return number_format((float)FantasiasModel::FRETE, 2, ',', '');
    }   

    public function closePurchase()
    {
        $fantasias = $this->listItems();
        $total = $this->calculateTotal($fantasias);
        $totalComFrete = $this->applyFreight($total);
        
        $data['fantasias'] = $fantasias;
        $data['total'] = $total;
        $data['totalComFrete'] = $totalComFrete;
        $data['frete'] = $this->getFreight();
     
        return $data;
    }
}