<?php
namespace App\Model;
use Psr\Container\ContainerInterface;
use Zend\Session\Container;


class CheckoutModelFactory
{
    public function __invoke(ContainerInterface $container){
        
        $configPagarme = $container->get('config')['pagarme'];
       
        $pagarMe = new \PagarMe\Sdk\PagarMe($configPagarme['apiKey']);
        $container = new Container('cart');
        
        return new CheckoutModel($pagarMe,$container);
        
    }
}
