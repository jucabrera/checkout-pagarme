<?php
namespace App\Model;

use PagarMe\Sdk\PagarMe;
use Zend\Math\Rand;
use Zend\Session\Container;

class CheckoutModel
{

    private $pagarMe;

    private $container;

    public function __construct(PagarMe $pagarme, Container $container)
    {
        $this->pagarMe = $pagarme;
        $this->container = $container;
    }

    public function createCard($data)
    {
        $cartao = $data['cartao_credito'];
        $nomeCartao = $data['nome_cartao'];
        $validadeCartao = $data['validade_cartao'];
        $card = $this->pagarMe->card()->create($cartao, $nomeCartao, $validadeCartao);
        return $card;
    }

    public function createAddress($data)
    {
        $pagarMe = $this->pagarMe;
        $rua = $data['rua'];
        $numero_endereco = $data['numero_endereco'];
        $bairro = $data['bairro'];
        $cep = $_POST['cep'];
        
        $address = new \PagarMe\Sdk\Customer\Address([
            'street' => $rua,
            'streetNumber' => $numero_endereco,
            'neighborhood' => $bairro,
            'zipcode' => $cep
        ]);
        return $address;
    }

    public function createPhone($data)
    {
        $pagarMe = $this->pagarMe;
        $ddd = $data['ddd'];
        $telefone = $data['telefone'];
        
        $phone = new \PagarMe\Sdk\Customer\Phone([
            'ddd' => $ddd,
            'number' => $telefone
        ]);
        
        return $phone;
    }

    public function createCustomer($data)
    {
        $address = $this->createAddress($data);
        $phone = $this->createPhone($data);
        $nome = $data['nome'];
        $email = $data['email'];
        $cpf = $data['cpf'];
        $dataNascimento = $data['data_nascimento'];
        $sexo = $data['sexo'];
        
        $customer = $this->pagarMe->customer()->create($nome, $email, $cpf, $address, $phone, $dataNascimento, $sexo);
        return $customer;
    }

    public function checkout(array $data)
    {
        try {
            $code = $data['code'];
            $cart = $this->getItems($code);
            $total = (float)$cart['totalComFrete'] * 100;
            $amount = $total;  
//            $amount = 1;
            $card = $this->createCard($data);
            $customer = $this->createCustomer($data);
            $transaction = $this->pagarMe->transaction()->creditCardTransaction($amount, $card, $customer, 1, true, 'http://requestb.in/pkt7pgpk', [
                'idProduto' => $code
            ]);

            return $transaction->getId();
        } catch (\Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    public function generateCode()
    {
        $chars = '';
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars .= '0123456789';
        $now = new \DateTime();
        return $now->getTimestamp() . Rand::getString(5, $chars);
    }

    public function saveItems($data, $code)
    {

        $data['code'] = $code;
        $this->container->$code = $data;

    }

    public function getItems($code)
    {
        if (! isset($this->container->$code)) {
            throw new \Exception('Empty cart');
        }
        return $this->container->$code;
    }
}
