<?php
namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use App\Model\FantasiasModel;
use App\Model\CheckoutModel;

class CheckoutAction implements ServerMiddlewareInterface
{

    private $router;

    private $template;

    private $fantasiasModel;

    private $checkoutModel;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, FantasiasModel $fantasiasModel, CheckoutModel $checkoutModel)
    {
        $this->router = $router;
        $this->template = $template;
        $this->fantasiasModel = $fantasiasModel;
        $this->checkoutModel = $checkoutModel;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = [];        
        $data = $this->fantasiasModel->closePurchase();
        $code = $this->checkoutModel->generateCode($data);
        $this->checkoutModel->saveItems($data, $code);        
        $cart = $this->checkoutModel->getItems($code);
   
        return new HtmlResponse($this->template->render('app::checkout', $cart));
    }
}
