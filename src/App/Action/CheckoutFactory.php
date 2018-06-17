<?php
namespace App\Action;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use App\Model\FantasiasModel;
use App\Model\CheckoutModel;

class CheckoutFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class) ? $container->get(TemplateRendererInterface::class) : null;
        $fantasiasModel = $container->get(FantasiasModel::class);
        $checkoutModel = $container->get(CheckoutModel::class);       
        
        return new CheckoutAction($router, $template, $fantasiasModel,$checkoutModel);
    }
}
