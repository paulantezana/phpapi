<?php

require_once(MODEL_PATH . '/Product.php');

class ProductController
{
    protected $connection;
    protected $productModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->productModel = new Product($this->connection);
    }

    public function all()
    {
        $res = new Result();

        $products = $this->productModel->getAll();

        $res->result = $products;
        $res->success = true;
        $res->message = 'El registro se eliminó exitosamente';

        return $res;
    }

    public function id()
    {
        $res = new Result();

        $postData = file_get_contents('php://input');
        $body = json_decode($postData, true);

        $product = $this->productModel->getById($body['id']);

        $res->result = $product;
        $res->success = true;
        $res->message = 'El registro se eliminó exitosamente';

        return $res;
    }

    public function create()
    {
        $res = new Result();

        $postData = file_get_contents('php://input');
        $body = json_decode($postData, true);

        $validate = $this->validateInput($body);
        if (!$validate->success) {
            throw new ControlledException($validate->message);
        }

        $productId =  $this->productModel->insert([
            'bar_code' => htmlspecialchars($body['barCode']),
            'description' => htmlspecialchars($body['description']),
            'purchase_price' => htmlspecialchars($body['purchasePrice']),
            'sale_price' => htmlspecialchars($body['salePrice']),
        ]);

        $res->result = [
            'id' => $productId,
        ];
        $res->success = true;
        $res->message = 'El registro se insertó exitosamente';

        return $res;
    }

    public function update()
    {
        $res = new Result();

        $postData = file_get_contents('php://input');
        $body = json_decode($postData, true);

        $validate = $this->validateInput($body, 'update');
        if (!$validate->success) {
            throw new ControlledException($validate->message);
        }

        $this->productModel->updateById($body['id'], [
            'full_name' => htmlspecialchars($body['fullName']),
            'email' => htmlspecialchars($body['email']),
            'phone' => htmlspecialchars($body['phone']),
        ]);

        $res->result = [
            'id' => $body['id'],
        ];
        $res->success = true;
        $res->message = 'El registro se actualizó exitosamente';

        return $res;
    }

    public function delete()
    {
        $res = new Result();

        $postData = file_get_contents('php://input');
        $body = json_decode($postData, true);

        $this->productModel->deleteById($body['id']);

        $res->success = true;
        $res->message = 'El registro se eliminó exitosamente';

        return $res;
    }

    public function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;

        if ($type == 'create' || $type == 'update') {
            if (($body['full_name'] ?? '') == '') {
                $res->message .= 'Falta ingresar el nombre completo | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['id'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message), '|');

        return $res;
    }
}