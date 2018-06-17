<?php
namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Whoops\Exception\ErrorException;
use App\Model\CheckoutModel;

class CompraAction implements ServerMiddlewareInterface
{

    private $router;

    private $template;

    private $checkoutModel;

    public function __construct(Router\RouterInterface $router, Template\TemplateRendererInterface $template = null, CheckoutModel $checkoutModel)
    {
        $this->router = $router;
        $this->template = $template;
        $this->checkoutModel = $checkoutModel;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = [];
        try {
            $transactionId = $this->checkoutModel->checkout($request->getParsedBody());
            $data['messageType'] = 'success';
            $data['message'] = "Compra efetuada com sucesso! Código transaçao: $transactionId";
            return new HtmlResponse($this->template->render('app::compra', $data));
        } catch (\Exception $e) {
            $message = 'Infelizmente não deu certo. Contate-nos pelo email ecommerce@yopmail.com';
            $data['messageType'] = 'danger';
            $data['message'] = $message;
            return new HtmlResponse($this->template->render('app::compra', $data));
//             throw new ErrorException($e->getMessage());
        }
    }
}
