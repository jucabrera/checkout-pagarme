<?php
namespace App\Model;

use App\Exception\AppException;
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
        $cartao = $data['cartao_credito']??null;
        $nomeCartao = $data['nome_cartao']??null;
        $validadeCartao = $data['validade_cartao']??null;

        if(!$cartao or !$nomeCartao or !$validadeCartao){
            throw new AppException('Dados do cartão são campos obrigatórios.');
        }

        $card = $this->pagarMe->card()->create($cartao, $nomeCartao, $validadeCartao);
        return $card;
    }

    public function createAddress($data)
    {
        $pagarMe = $this->pagarMe;
        $rua = $data['rua']??null;
        $numero_endereco = $data['numero_endereco']??null;
        $bairro = $data['bairro']??null;
        $cep = $_POST['cep']??null;

        if(!$rua or !$numero_endereco or !$bairro or !$cep){
            throw new AppException('Endereço é um campo obrigatório.');
        }
        
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
        $ddd = $data['ddd']??null;
        $telefone = $data['telefone']??null;

        if(!$ddd or !$telefone ){
            throw new AppException('Telefone é um campo obrigatório');
        }
        
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
        $nome = $data['nome']??null;
        $email = $data['email']??null;
        $cpf = $data['cpf']??null;
        $dataNascimento = $data['data_nascimento']??null;
        $sexo = $data['sexo']??null;

        if(!$nome or !$email or !$cpf or !$dataNascimento or !$sexo ){
            throw new AppException('Dados pessoais são obrigatórios.');
        }

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
            $card = $this->createCard($data);
            $customer = $this->createCustomer($data);
            $transaction = $this->pagarMe->transaction()->creditCardTransaction($amount, $card, $customer, 1, true, 'http://localhost:8080/transaction', [
                'idProduto' => $code
            ]);

            return $transaction->getId();
            $this->clearItems($code);
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

    public function clearItems($code)
    {
        if (! isset($this->container->$code)) {
            unset($this->container->$code);
        }
    }
}
